<?php

class ProfileController
{
    public static function index(): void
    {
        Auth::requireLogin();
        $sb = supabase();
        $userId = Auth::id();

        $profile = [
            'name' => Auth::name() ?? '',
            'email' => (string) (Auth::email() ?? ''),
            'phone' => (string) (Auth::phone() ?? ''),
        ];

        if ($userId) {
            [$_, $rows] = $sb->get('profiles', '?id=eq.' . urlencode($userId) . '&select=full_name,email,phone');
            if (is_array($rows) && count($rows) > 0) {
                $row = $rows[0];
                if (isset($row['full_name']) && trim((string) $row['full_name']) !== '') {
                    $profile['name'] = trim((string) $row['full_name']);
                }
                if (isset($row['email']) && trim((string) $row['email']) !== '') {
                    $profile['email'] = trim((string) $row['email']);
                }
                if (isset($row['phone']) && trim((string) $row['phone']) !== '') {
                    $profile['phone'] = trim((string) $row['phone']);
                }
            } else {
                $sb->post('profiles', [
                    'id' => $userId,
                    'full_name' => $profile['name'],
                    'email' => '',
                    'phone' => '',
                ]);
                // Refetch
                [$_, $rows] = $sb->get('profiles', '?id=eq.' . urlencode($userId) . '&select=full_name,email,phone');
                if (is_array($rows) && count($rows) > 0) {
                    $row = $rows[0];
                    if (isset($row['full_name']) && trim((string) $row['full_name']) !== '') {
                        $profile['name'] = trim((string) $row['full_name']);
                    }
                    if (isset($row['email']) && trim((string) $row['email']) !== '') {
                        $profile['email'] = trim((string) $row['email']);
                    }
                    if (isset($row['phone']) && trim((string) $row['phone']) !== '') {
                        $profile['phone'] = trim((string) $row['phone']);
                    }
                }
            }

            // If profile has no email, fetch from Supabase Auth (auth.users) so the field is populated
            if ($profile['email'] === '' && Auth::jwt()) {
                $authUser = self::fetchAuthUser(Auth::jwt());
                if ($authUser !== null && isset($authUser['email']) && $authUser['email'] !== '') {
                    $profile['email'] = $authUser['email'];
                }
            }
            // Ensure we don't overwrite with empty: prefer session when DB gave empty
            if ($profile['phone'] === '' && Auth::phone() !== null && Auth::phone() !== '') {
                $profile['phone'] = Auth::phone();
            }
            if ($profile['email'] === '' && Auth::email() !== null && Auth::email() !== '') {
                $profile['email'] = Auth::email();
            }
        }

        $title = 'My Profile';
        ob_start();
        include __DIR__ . '/../views/profile/index.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layout/master.php';
    }

    public static function update(): void
    {
        Auth::requireLogin();
        $sb = supabase();
        $userId = Auth::id();
        if (!$userId) {
            header('Location: ' . base_url());
            exit;
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        if ($password !== '' && $password !== $passwordConfirm) {
            $_SESSION['form_error'] = 'New password and confirmation do not match.';
            header('Location: ' . base_url('?page=profile'));
            exit;
        }

        // Ensure profile row exists (create on first save if needed), then update with service role so write always persists
        $serviceKey = defined('SUPABASE_SERVICE_ROLE_KEY') ? (SUPABASE_SERVICE_ROLE_KEY ?: '') : '';
        $baseUrl = rtrim(SUPABASE_URL, '/');
        $profileData = [
            'full_name' => $name !== '' ? $name : null,
            'email' => $email !== '' ? $email : null,
            'phone' => $phone !== '' ? $phone : null,
            'updated_at' => date('c'),
        ];

        [$_, $existing] = $sb->get('profiles', '?id=eq.' . urlencode($userId) . '&select=id');
        if (!is_array($existing) || count($existing) === 0) {
            $sb->post('profiles', [
                'id' => $userId,
                'full_name' => $profileData['full_name'],
                'email' => $profileData['email'],
                'phone' => $profileData['phone'],
            ]);
        } else {
            $profileSaved = false;
            if ($serviceKey !== '') {
                $url = $baseUrl . '/rest/v1/profiles?id=eq.' . urlencode($userId);
                $ch = curl_init($url);
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CUSTOMREQUEST  => 'PATCH',
                    CURLOPT_POSTFIELDS     => json_encode($profileData),
                    CURLOPT_HTTPHEADER     => [
                        'Content-Type: application/json',
                        'apikey: ' . $serviceKey,
                        'Authorization: Bearer ' . $serviceKey,
                    ],
                    CURLOPT_TIMEOUT        => 10,
                ]);
                $res = curl_exec($ch);
                $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                if ($code >= 200 && $code < 300) {
                    $profileSaved = true;
                }
            }
            if (!$profileSaved) {
                [$patchStatus] = $sb->patch('profiles', 'id=eq.' . urlencode($userId), $profileData);
                if ($patchStatus < 200 || $patchStatus >= 300) {
                    $_SESSION['form_error'] = 'Profile could not be saved (HTTP ' . ($patchStatus ?: 404) . '). Create the profiles table: in Supabase Dashboard go to SQL Editor and run the SQL from database/migrations/006_profiles_table.sql in this project.';
                    header('Location: ' . base_url('?page=profile'));
                    exit;
                }
            }
        }

        // Sync name to public.users so it persists after logout/login (login reads users.name)
        if ($serviceKey !== '' && $name !== '') {
            $url = $baseUrl . '/rest/v1/users?id=eq.' . urlencode($userId);
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST  => 'PATCH',
                CURLOPT_POSTFIELDS     => json_encode(['name' => $name]),
                CURLOPT_HTTPHEADER     => [
                    'Content-Type: application/json',
                    'apikey: ' . $serviceKey,
                    'Authorization: Bearer ' . $serviceKey,
                ],
                CURLOPT_TIMEOUT        => 10,
            ]);
            curl_exec($ch);
            curl_close($ch);
        }

        // Keep session name in sync so user sees updated name immediately
        if ($name !== '') {
            Session::set('user_name', $name);
        }
        Session::set('user_email', $email);
        Session::set('user_phone', $phone);

        // Update email/phone/password via Supabase Auth Admin API if provided
        if ($password !== '' || $email !== '' || $phone !== '') {
            $serviceKey = defined('SUPABASE_SERVICE_ROLE_KEY') ? (SUPABASE_SERVICE_ROLE_KEY ?: '') : '';
            if ($serviceKey !== '') {
                $baseUrl = rtrim(SUPABASE_URL, '/');
                $url = $baseUrl . '/auth/v1/admin/users/' . urlencode($userId);
                $payloadArr = [];
                if ($password !== '') {
                    $payloadArr['password'] = $password;
                }
                if ($email !== '') {
                    $payloadArr['email'] = $email;
                }
                $meta = [];
                if ($phone !== '') {
                    $meta['phone'] = $phone;
                }
                if (!empty($meta)) {
                    $payloadArr['user_metadata'] = $meta;
                }
                $payload = json_encode($payloadArr);
                $ch = curl_init($url);
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CUSTOMREQUEST  => 'PUT',
                    CURLOPT_POSTFIELDS     => $payload,
                    CURLOPT_HTTPHEADER     => [
                        'Content-Type: application/json',
                        'apikey: ' . $serviceKey,
                        'Authorization: Bearer ' . $serviceKey,
                    ],
                    CURLOPT_TIMEOUT        => 15,
                ]);
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($httpCode < 200 || $httpCode >= 300) {
                    $resp = json_decode($response ?? '', true);
                    $detail = 'Unknown error';
                    if (is_array($resp)) {
                        $detail = $resp['error_description'] ?? $resp['msg'] ?? $resp['error'] ?? json_encode($resp);
                    } elseif (is_string($response) && $response !== '') {
                        $detail = $response;
                    }
                    $_SESSION['form_error'] = 'Profile saved, but auth update failed (HTTP ' . ($httpCode ?: 0) . '): ' . $detail;
                    header('Location: ' . base_url('?page=profile'));
                    exit;
                }
            } else {
                $_SESSION['form_error'] = 'Profile saved, but password/email update is not configured (missing SUPABASE_SERVICE_ROLE_KEY in .env).';
                header('Location: ' . base_url('?page=profile'));
                exit;
            }
        }

        $_SESSION['form_success'] = 'Profile updated successfully.';
        header('Location: ' . base_url('?page=profile'));
        exit;
    }

    /**
     * Fetch current user from Supabase Auth (GET /auth/v1/user) to get email when profiles.email is empty.
     */
    private static function fetchAuthUser(string $jwt): ?array
    {
        $baseUrl = rtrim(SUPABASE_URL, '/');
        $url = $baseUrl . '/auth/v1/user';
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'apikey: ' . SUPABASE_ANON_KEY,
                'Authorization: Bearer ' . $jwt,
            ],
            CURLOPT_TIMEOUT => 10,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpCode !== 200) {
            return null;
        }
        $data = json_decode($response, true);
        return is_array($data) ? $data : null;
    }
}

