<?php

class BranchController
{
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
        [$_, $rows] = $sb->get('branches', '?select=id,name,code,address,is_active&order=name.asc');
        $raw = is_array($rows) ? $rows : [];
        $list = array_values(array_filter($raw, static function ($row) {
            return trim((string) ($row['name'] ?? '')) !== '';
        }));

        $title = 'Branches';
        ob_start();
        include __DIR__ . '/../views/branches/index.php';
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

        $title = 'Add Branch';
        $error = $_SESSION['form_error'] ?? '';
        if ($error) {
            unset($_SESSION['form_error']);
        }
        ob_start();
        include __DIR__ . '/../views/branches/create.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layout/master.php';
    }

    private static function store(): void
    {
        self::requireAdmin();
        $name = trim($_POST['name'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $code = trim($_POST['code'] ?? '');

        if ($name === '') {
            $_SESSION['form_error'] = 'Branch name is required.';
            header('Location: ' . base_url('?page=branches/create'));
            exit;
        }

        $sb = supabase();
        [$codeStatus, $result] = $sb->post('branches', [
            'name' => $name,
            'address' => $address !== '' ? $address : null,
            'code' => $code !== '' ? $code : null,
            'is_active' => true,
        ]);

        if ($codeStatus >= 200 && $codeStatus < 300) {
            header('Location: ' . base_url('?page=branches'));
            exit;
        }

        $msg = is_array($result) ? ($result['message'] ?? 'Failed to create branch') : 'Failed to create branch';
        $_SESSION['form_error'] = $msg;
        header('Location: ' . base_url('?page=branches/create'));
        exit;
    }

    public static function edit(): void
    {
        self::requireAdmin();
        $id = trim($_GET['id'] ?? '');
        if ($id === '') {
            header('Location: ' . base_url('?page=branches'));
            exit;
        }

        $sb = supabase();
        [$_, $rows] = $sb->get('branches', '?id=eq.' . urlencode($id) . '&select=*');
        $branch = (is_array($rows) && count($rows) > 0) ? $rows[0] : null;
        if (!$branch) {
            header('Location: ' . base_url('?page=branches'));
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            self::update($id);
            return;
        }

        $title = 'Edit Branch';
        $error = $_SESSION['form_error'] ?? '';
        if ($error) {
            unset($_SESSION['form_error']);
        }
        ob_start();
        include __DIR__ . '/../views/branches/edit.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layout/master.php';
    }

    private static function update(string $id): void
    {
        self::requireAdmin();
        $name = trim($_POST['name'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $code = trim($_POST['code'] ?? '');
        $isActive = isset($_POST['is_active']) && $_POST['is_active'] === '1';

        if ($name === '') {
            $_SESSION['form_error'] = 'Branch name is required.';
            header('Location: ' . base_url('?page=branches/edit&id=' . urlencode($id)));
            exit;
        }

        $sb = supabase();
        [$status, $result] = $sb->patch('branches', 'id=eq.' . urlencode($id), [
            'name' => $name,
            'address' => $address !== '' ? $address : null,
            'code' => $code !== '' ? $code : null,
            'is_active' => $isActive,
        ]);

        if ($status >= 200 && $status < 300) {
            header('Location: ' . base_url('?page=branches'));
            exit;
        }

        $msg = is_array($result) ? ($result['message'] ?? 'Update failed') : 'Update failed';
        $_SESSION['form_error'] = $msg;
        header('Location: ' . base_url('?page=branches/edit&id=' . urlencode($id)));
        exit;
    }
}

