<?php

/**
 * AuthController – login via Supabase Auth, load public.users for role, store in session.
 * All auth state (JWT + user_id, user_name, user_role) lives in session; RLS enforces server-side.
 */
class AuthController
{
    private const AUTH_TOKEN_URL = '/auth/v1/token?grant_type=password';

    /**
     * Handle POST login: Supabase Auth → public.users → session.
     */
    public static function login(): void
    {
        Session::start();

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $setError = static function (string $msg): void {
            Session::flash('login_error', $msg);
            Session::set('login_error', $msg);
        };

        if ($email === '' || $password === '') {
            $setError('Email and password are required.');
            header('Location: ' . base_url());
            exit;
        }

        $auth = self::callSupabaseAuth($email, $password);

        if (isset($auth['error'])) {
            $setError(self::authErrorMessage($auth));
            header('Location: ' . base_url());
            exit;
        }

        $accessToken = $auth['access_token'] ?? null;
        $authUserId = $auth['user']['id'] ?? null;

        if (!$accessToken || !$authUserId) {
            $setError('Invalid response from authentication service.');
            header('Location: ' . base_url());
            exit;
        }

        $profile = self::fetchUserProfile($authUserId, $accessToken);

        if ($profile === null) {
            $setError('Your account is not set up in the system or you do not have access. Please contact an administrator.');
            header('Location: ' . base_url());
            exit;
        }

        if (isset($profile['is_active']) && $profile['is_active'] === false) {
            $setError('Your account has been deactivated. Please contact an administrator.');
            header('Location: ' . base_url());
            exit;
        }

        // Prefer display name and profile data from profiles (updated via My Profile) over users
        $profileRow = self::fetchProfileRow($authUserId, $accessToken);
        if ($profileRow !== null) {
            $displayName = isset($profileRow['full_name']) ? trim((string) $profileRow['full_name']) : '';
            if ($displayName !== '') {
                $profile['name'] = $displayName;
            }
            $profile['email'] = isset($profileRow['email']) ? trim((string) $profileRow['email']) : '';
            $profile['phone'] = isset($profileRow['phone']) ? trim((string) $profileRow['phone']) : '';
        }
        // If still no email, use auth.users (e.g. first login before profile has email)
        if ($profile['email'] === '' && isset($auth['user']['email'])) {
            $profile['email'] = trim((string) $auth['user']['email']);
        }

        self::setSessionUser($accessToken, $profile);
        Session::remove('login_error');
        unset($_SESSION['_flash']['login_error']);
        header('Location: ' . base_url());
        exit;
    }

    /**
     * Logout: destroy session and redirect to login.
     */
    public static function logout(): void
    {
        Session::destroy();
        header('Location: ' . base_url());
        exit;
    }

    /**
     * Call Supabase Auth REST API (email/password).
     * Returns decoded JSON or ['error' => ..., 'error_description' => ...].
     */
    private static function callSupabaseAuth(string $email, string $password): array
    {
        $url = SUPABASE_URL . self::AUTH_TOKEN_URL;
        $payload = json_encode([
            'email' => $email,
            'password' => $password,
        ]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'apikey: ' . SUPABASE_ANON_KEY,
            ],
            CURLOPT_TIMEOUT => 15,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errno = curl_errno($ch);
        curl_close($ch);

        if ($errno) {
            return ['error' => 'connection_error', 'error_description' => 'Could not reach the authentication service.'];
        }

        $data = json_decode($response, true);
        if (!is_array($data)) {
            return ['error' => 'invalid_response', 'error_description' => 'Invalid response from server.'];
        }

        if ($httpCode >= 400 || isset($data['error'])) {
            return [
                'error' => $data['error'] ?? 'unknown',
                'error_description' => $data['error_description'] ?? $data['msg'] ?? 'Login failed.',
            ];
        }

        return $data;
    }

    /**
     * Fetch public.users row by id using JWT (for role + name + is_active).
     */
    private static function fetchUserProfile(string $userId, string $jwt): ?array
    {
        $url = SUPABASE_URL . '/rest/v1/users?id=eq.' . urlencode($userId);
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'apikey: ' . SUPABASE_ANON_KEY,
                'Authorization: Bearer ' . $jwt,
                'Content-Type: application/json',
            ],
            CURLOPT_TIMEOUT => 10,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            return null;
        }

        $rows = json_decode($response, true);
        if (!is_array($rows) || count($rows) === 0) {
            return null;
        }

        return $rows[0];
    }

    /**
     * Fetch display name (and email, phone) from public.profiles so data updated on My Profile persists after re-login.
     * Uses service role when available so the read is not blocked by RLS on server-side requests.
     */
    private static function fetchProfileRow(string $userId, string $jwt): ?array
    {
        $baseUrl = rtrim(SUPABASE_URL, '/');
        $url = $baseUrl . '/rest/v1/profiles?id=eq.' . urlencode($userId) . '&select=full_name,email,phone';
        $serviceKey = defined('SUPABASE_SERVICE_ROLE_KEY') ? (SUPABASE_SERVICE_ROLE_KEY ?: '') : '';
        $useServiceRole = $serviceKey !== '';

        $headers = [
            'Content-Type: application/json',
            'apikey: ' . ($useServiceRole ? $serviceKey : SUPABASE_ANON_KEY),
            'Authorization: Bearer ' . ($useServiceRole ? $serviceKey : $jwt),
        ];
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 10,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpCode !== 200) {
            return null;
        }
        $rows = json_decode($response, true);
        if (!is_array($rows) || count($rows) === 0) {
            return null;
        }
        return $rows[0];
    }

    /**
     * Fetch display name from public.profiles.full_name so name updated on My Profile persists after re-login.
     */
    private static function fetchProfileDisplayName(string $userId, string $jwt): ?string
    {
        $row = self::fetchProfileRow($userId, $jwt);
        if ($row === null) {
            return null;
        }
        $fullName = isset($row['full_name']) ? trim((string) $row['full_name']) : null;
        return ($fullName !== null && $fullName !== '') ? $fullName : null;
    }

    private static function setSessionUser(string $jwt, array $profile): void
    {
        Session::set('jwt', $jwt);
        Session::set('user_id', $profile['id'] ?? null);
        Session::set('user_name', $profile['name'] ?? '');
        Session::set('user_role', $profile['role'] ?? 'user');
        Session::set('user_branch_id', $profile['branch_id'] ?? null);
        Session::set('user_email', $profile['email'] ?? '');
        Session::set('user_phone', $profile['phone'] ?? '');
        Session::set('last_activity', time());
    }

    private static function authErrorMessage(array $auth): string
    {
        $desc = $auth['error_description'] ?? $auth['msg'] ?? 'Login failed.';
        $error = $auth['error'] ?? '';

        if ($error === 'invalid_grant' || strpos(strtolower($desc), 'invalid') !== false) {
            return 'Invalid email or password.';
        }
        return $desc;
    }
}
