<?php

class ClientController
{
    private const STATUSES = [
        'Intro Email Sent',
        'Follow-up Email Sent',
        'Client Responded',
        'No Response from Client',
        'Client Acquired'
    ];

    public static function index(): void
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($id > 0) {
            self::view($id);
            return;
        }

        $sb = supabase();
        [$status, $clients] = $sb->get('clients', '?select=*&order=created_at.desc');
        $list = is_array($clients) ? $clients : [];

        $title = 'Clients';
        ob_start();
        include __DIR__ . '/../views/clients/index.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layout/master.php';
    }

    private static function view(int $id): void
    {
        $sb = supabase();
        [$code, $rows] = $sb->get('clients', "?id=eq.{$id}&select=*");
        $client = (is_array($rows) && count($rows) > 0) ? $rows[0] : null;
        if (!$client) {
            header('Location: ' . base_url('?page=clients'));
            exit;
        }
        [$_, $interactions] = $sb->get('interactions', "?client_id=eq.{$id}&select=*&order=created_at.desc");
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
        $statuses = self::STATUSES;
        ob_start();
        include __DIR__ . '/../views/clients/create.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layout/master.php';
    }

    private static function store(): void
    {
        $sb = supabase();
        $userId = $_SESSION['user_id'] ?? null;

        $data = [
            'client_name' => trim($_POST['client_name'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'status' => $_POST['status'] ?? 'Intro Email Sent'
        ];

        if (!in_array($data['status'], self::STATUSES, true)) {
            $data['status'] = 'Intro Email Sent';
        }

        if ($userId !== null) {
            $data['user_id'] = $userId;
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
