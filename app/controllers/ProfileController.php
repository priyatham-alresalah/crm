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
            'email' => '',
            'phone' => '',
        ];

        if ($userId) {
            [$_, $rows] = $sb->get('users', '?id=eq.' . urlencode($userId) . '&select=name,email,phone');
            if (is_array($rows) && count($rows) > 0) {
                $row = $rows[0];
                $profile['name'] = $row['name'] ?? $profile['name'];
                $profile['email'] = $row['email'] ?? '';
                $profile['phone'] = $row['phone'] ?? '';
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

        // Update users table
        $data = [
            'name' => $name !== '' ? $name : null,
            'email' => $email !== '' ? $email : null,
            'phone' => $phone !== '' ? $phone : null,
        ];
        $sb->patch('users', 'id=eq.' . urlencode($userId), $data);

        // Update email/phone/password via Supabase Auth Admin API if provided
        if ($password !== '' || $email !== '' || $phone !== '') {
            $serviceKey = defined('SUPABASE_SERVICE_ROLE_KEY') ? (SUPABASE_SERVICE_ROLE_KEY ?: '') : '';
            if ($serviceKey !== '') {
                $url = SUPABASE_URL . '/auth/v1/admin/users/' . urlencode($userId);
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
                    CURLOPT_CUSTOMREQUEST  => 'PATCH',
                    CURLOPT_POSTFIELDS     => $payload,
                    CURLOPT_HTTPHEADER     => [
                        'Content-Type: application/json',
                        // Use service role for both apikey and Authorization as per Supabase docs
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
                    $detail = is_array($resp)
                        ? ($resp['error_description'] ?? $resp['msg'] ?? $resp['error'] ?? 'Unknown error')
                        : 'Unknown error';
                    $_SESSION['form_error'] = 'Profile updated, but password change failed (HTTP ' . ($httpCode ?: 0) . '): ' . $detail;
                    header('Location: ' . base_url('?page=profile'));
                    exit;
                }
            } else {
                $_SESSION['form_error'] = 'Profile updated, but password change is not configured (missing service key).';
                header('Location: ' . base_url('?page=profile'));
                exit;
            }
        }

        $_SESSION['form_error'] = 'Profile updated successfully.';
        header('Location: ' . base_url('?page=profile'));
        exit;
    }
}

