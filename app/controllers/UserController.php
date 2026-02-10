<?php

/**
 * UserController – CRUD for public.users (admin only).
 * Create uses Supabase Auth Admin API when SUPABASE_SERVICE_ROLE_KEY is set.
 */
class UserController
{
    private const ROLES = ['admin', 'manager', 'user'];

    private static function requireAdmin(): void
    {
        if (!Auth::isAdmin()) {
            header('Location: ' . base_url());
            exit;
        }
    }

    public static function index(): void
    {
        self::requireAdmin();
        $sb = supabase();
        [$_, $rows] = $sb->get('users', '?select=*&order=name.asc');
        $list = is_array($rows) ? $rows : [];

        $title = 'Users';
        ob_start();
        include __DIR__ . '/../views/users/index.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layout/master.php';
    }

    public static function create(): void
    {
        self::requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            self::store();
            return;
        }

        $title = 'Add User';
        $roles = self::ROLES;
        $error = $_SESSION['form_error'] ?? '';
        if ($error) {
            unset($_SESSION['form_error']);
        }
        ob_start();
        include __DIR__ . '/../views/users/create.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layout/master.php';
    }

    private static function store(): void
    {
        self::requireAdmin();
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $name = trim($_POST['name'] ?? '');
        $role = trim($_POST['role'] ?? 'user');

        if ($email === '' || $password === '') {
            $_SESSION['form_error'] = 'Email and password are required.';
            header('Location: ' . base_url('?page=users/create'));
            exit;
        }
        if (!in_array($role, self::ROLES, true)) {
            $role = 'user';
        }

        $serviceKey = defined('SUPABASE_SERVICE_ROLE_KEY') ? (SUPABASE_SERVICE_ROLE_KEY ?: '') : '';
        if ($serviceKey === '') {
            $_SESSION['form_error'] = 'User creation is not configured. Set SUPABASE_SERVICE_ROLE_KEY in .env to create users from CRM.';
            header('Location: ' . base_url('?page=users/create'));
            exit;
        }

        $authUrl = SUPABASE_URL . '/auth/v1/admin/users';
        $ch = curl_init($authUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode([
                'email' => $email,
                'password' => $password,
                'email_confirm' => true,
            ]),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'apikey: ' . SUPABASE_ANON_KEY,
                'Authorization: Bearer ' . $serviceKey,
            ],
            CURLOPT_TIMEOUT => 15,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $data = json_decode($response, true);

        if ($httpCode < 200 || $httpCode >= 300 || !is_array($data)) {
            $msg = is_array($data) ? ($data['msg'] ?? $data['error_description'] ?? 'Failed to create user') : 'Failed to create user';
            $_SESSION['form_error'] = $msg;
            header('Location: ' . base_url('?page=users/create'));
            exit;
        }

        $authId = $data['id'] ?? null;
        if (!$authId) {
            $_SESSION['form_error'] = 'User created in Auth but no ID returned.';
            header('Location: ' . base_url('?page=users/create'));
            exit;
        }

        $sb = supabase();
        [$code, $result] = $sb->post('users', [
            'id' => $authId,
            'name' => $name !== '' ? $name : $email,
            'role' => $role,
            'is_active' => true,
        ]);

        if ($code >= 200 && $code < 300) {
            header('Location: ' . base_url('?page=users'));
            exit;
        }
        $msg = is_array($result) ? ($result['message'] ?? 'User created in Auth but failed to save profile.') : 'Failed to save profile.';
        $_SESSION['form_error'] = $msg;
        header('Location: ' . base_url('?page=users/create'));
        exit;
    }

    public static function edit(): void
    {
        self::requireAdmin();
        $id = trim($_GET['id'] ?? '');
        if ($id === '') {
            header('Location: ' . base_url('?page=users'));
            exit;
        }

        $sb = supabase();
        [$_, $rows] = $sb->get('users', '?id=eq.' . urlencode($id) . '&select=*');
        $user = (is_array($rows) && count($rows) > 0) ? $rows[0] : null;
        if (!$user) {
            header('Location: ' . base_url('?page=users'));
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            self::update($id);
            return;
        }

        $title = 'Edit User';
        $roles = self::ROLES;
        $error = $_SESSION['form_error'] ?? '';
        if ($error) {
            unset($_SESSION['form_error']);
        }
        ob_start();
        include __DIR__ . '/../views/users/edit.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layout/master.php';
    }

    private static function update(string $id): void
    {
        self::requireAdmin();
        $name = trim($_POST['name'] ?? '');
        $role = trim($_POST['role'] ?? 'user');
        $isActive = isset($_POST['is_active']) && $_POST['is_active'] === '1';

        if (!in_array($role, self::ROLES, true)) {
            $role = 'user';
        }

        $sb = supabase();
        [$code, $result] = $sb->patch('users', 'id=eq.' . urlencode($id), [
            'name' => $name,
            'role' => $role,
            'is_active' => $isActive,
        ]);

        if ($code >= 200 && $code < 300) {
            header('Location: ' . base_url('?page=users'));
            exit;
        }
        $msg = is_array($result) ? ($result['message'] ?? 'Update failed') : 'Update failed';
        $_SESSION['form_error'] = $msg;
        header('Location: ' . base_url('?page=users/edit&id=' . urlencode($id)));
        exit;
    }

    public static function delete(): void
    {
        self::requireAdmin();
        $id = trim($_GET['id'] ?? $_POST['id'] ?? '');
        if ($id === '') {
            header('Location: ' . base_url('?page=users'));
            exit;
        }
        if (Auth::id() === $id) {
            $_SESSION['form_error'] = 'You cannot deactivate your own account.';
            header('Location: ' . base_url('?page=users'));
            exit;
        }

        $sb = supabase();
        [$code, $result] = $sb->patch('users', 'id=eq.' . urlencode($id), ['is_active' => false]);

        if ($code >= 200 && $code < 300) {
            header('Location: ' . base_url('?page=users'));
            exit;
        }
        $msg = is_array($result) ? ($result['message'] ?? 'Deactivate failed') : 'Deactivate failed';
        $_SESSION['form_error'] = $msg;
        header('Location: ' . base_url('?page=users'));
        exit;
    }
}
