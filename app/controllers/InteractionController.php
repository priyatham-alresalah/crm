<?php

class InteractionController
{
    public static function index(): void
    {
        $sb = supabase();
        [$_, $rows] = $sb->get('interactions', '?select=*,clients(client_name)&order=created_at.desc');
        $list = is_array($rows) ? $rows : [];

        $title = 'Interactions';
        ob_start();
        include __DIR__ . '/../views/interactions/index.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layout/master.php';
    }

    public static function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            self::store();
            return;
        }

        $sb = supabase();
        $clientId = isset($_GET['client_id']) ? (int) $_GET['client_id'] : 0;
        [$_, $clients] = $sb->get('clients', '?select=id,client_name&order=client_name');
        $clients = is_array($clients) ? $clients : [];

        $title = 'Log Interaction';
        $statuses = [
            'Intro Email Sent', 'Follow-up Email Sent', 'Client Responded',
            'No Response from Client', 'Client Acquired'
        ];
        ob_start();
        include __DIR__ . '/../views/interactions/create.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layout/master.php';
    }

    private static function store(): void
    {
        $sb = supabase();
        $clientId = (int) ($_POST['client_id'] ?? 0);
        $type = $_POST['type'] ?? 'email';
        if (!in_array($type, ['email', 'call'], true)) {
            $type = 'email';
        }

        $data = [
            'client_id' => $clientId,
            'type' => $type,
            'notes' => trim($_POST['notes'] ?? ''),
            'status_at_time' => trim($_POST['status_at_time'] ?? '')
        ];

        $userId = $_SESSION['user_id'] ?? null;
        if ($userId !== null) {
            $data['created_by'] = $userId;
        }

        [$code, $result] = $sb->post('interactions', $data);

        if ($code >= 200 && $code < 300) {
            header('Location: ' . base_url('?page=clients&id=' . $clientId));
            exit;
        }

        $_SESSION['form_error'] = is_array($result) ? ($result['message'] ?? 'Failed to log interaction') : 'Failed to log interaction';
        header('Location: ' . base_url('?page=interactions/create&client_id=' . $clientId));
        exit;
    }
}
