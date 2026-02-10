<?php

class ClientController
{
    private const CLIENT_STATUSES = ['new', 'contacted', 'converted', 'lost'];

    public static function index(): void
    {
        $id = isset($_GET['id']) ? trim((string) $_GET['id']) : '';
        if ($id !== '') {
            self::view($id);
            return;
        }

        $sb = supabase();
        [$_, $clients] = $sb->get('clients', '?select=*&order=created_at.desc');
        $list = is_array($clients) ? $clients : [];

        $title = 'Clients';
        ob_start();
        include __DIR__ . '/../views/clients/index.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layout/master.php';
    }

    private static function view(string $id): void
    {
        $sb = supabase();
        [$code, $rows] = $sb->get('clients', '?id=eq.' . urlencode($id) . '&select=*');
        $client = (is_array($rows) && count($rows) > 0) ? $rows[0] : null;
        if (!$client) {
            header('Location: ' . base_url('?page=clients'));
            exit;
        }
        [$_, $interactions] = $sb->get('interactions', '?client_id=eq.' . urlencode($id) . '&select=*&order=interaction_date.desc,created_at.desc');
        $timeline = is_array($interactions) ? $interactions : [];

        $title = 'Client: ' . ($client['client_name'] ?? '');
        ob_start();
        include __DIR__ . '/../views/clients/view.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layout/master.php';
    }

    public static function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            self::store();
            return;
        }

        $title = 'Add Client';
        $statuses = self::CLIENT_STATUSES;
        ob_start();
        include __DIR__ . '/../views/clients/create.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layout/master.php';
    }

    private static function store(): void
    {
        $sb = supabase();
        $userId = $_SESSION['user_id'] ?? null;

        $phone = trim($_POST['phone'] ?? '');
        $clientStatus = trim($_POST['client_status'] ?? '');
        $data = [
            'client_name' => trim($_POST['client_name'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => $phone === '' ? null : $phone
        ];
        if ($clientStatus !== '' && in_array($clientStatus, self::CLIENT_STATUSES, true)) {
            $data['client_status'] = $clientStatus;
        }

        if ($userId !== null) {
            $data['created_by'] = $userId;
            $data['assigned_to'] = $userId;
        }

        [$code, $result] = $sb->post('clients', $data);

        if ($code >= 200 && $code < 300) {
            header('Location: ' . base_url('?page=clients'));
            exit;
        }

        $msg = is_array($result) ? ($result['message'] ?? 'Failed to create client') : 'Failed to create client';
        $_SESSION['form_error'] = $msg;
        header('Location: ' . base_url('?page=clients/create'));
        exit;
    }
}
