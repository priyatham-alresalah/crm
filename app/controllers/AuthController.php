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

        if ($email === '' || $password === '') {
            Session::flash('login_error', 'Email and password are required.');
            header('Location: ' . BASE_URL);
            exit;
        }

        $auth = self::callSupabaseAuth($email, $password);

        if (isset($auth['error'])) {
            Session::flash('login_error', self::authErrorMessage($auth));
            header('Location: ' . BASE_URL);
            exit;
        }

        $accessToken = $auth['access_token'] ?? null;
        $authUserId = $auth['user']['id'] ?? null;

        if (!$accessToken || !$authUserId) {
            Session::flash('login_error', 'Invalid response from authentication service.');
            header('Location: ' . BASE_URL);
            exit;
        }

        $profile = self::fetchUserProfile($authUserId, $accessToken);

        if ($profile === null) {
            Session::flash('login_error', 'Your account is not set up or has been deactivated. Please contact an administrator.');
            header('Location: ' . BASE_URL);
            exit;
        }

        if (isset($profile['is_active']) && $profile['is_active'] === false) {
            Session::flash('login_error', 'Your account has been deactivated. Please contact an administrator.');
            header('Location: ' . BASE_URL);
            exit;
        }

        self::setSessionUser($accessToken, $profile);
        Session::remove('login_error');
        header('Location: ' . BASE_URL);
        exit;
    }

    /**
     * Logout: destroy session and redirect to login.
     */
    public static function logout(): void
    {
        Session::destroy();
        header('Location: ' . BASE_URL);
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

    private static function setSessionUser(string $jwt, array $profile): void
    {
        Session::set('jwt', $jwt);
        Session::set('user_id', $profile['id'] ?? null);
        Session::set('user_name', $profile['name'] ?? '');
        Session::set('user_role', $profile['role'] ?? 'user');
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
