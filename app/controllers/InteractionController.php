<?php

class InteractionController
{
    private const INTERACTION_TYPES = ['call', 'email', 'meeting', 'whatsapp'];
    private const STAGES = ['intro', 'followup', 'closing'];

    public static function index(): void
    {
        $sb = supabase();
        [$_, $rows] = $sb->get('interactions', '?select=*,clients(client_name)&order=interaction_date.desc,created_at.desc');
        $raw = is_array($rows) ? $rows : [];
        $list = array_values(array_filter($raw, static function ($row) {
            $clientId = trim((string) ($row['client_id'] ?? ''));
            $clientName = trim((string) (($row['clients']['client_name'] ?? $row['client_name'] ?? '')));
            $type = trim((string) ($row['interaction_type'] ?? ''));
            $subject = trim((string) ($row['subject'] ?? ''));
            $notes = trim((string) ($row['notes'] ?? ''));
            return ($clientId !== '' || $clientName !== '') && ($type !== '' || $subject !== '' || $notes !== '');
        }));

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
        $clientId = isset($_GET['client_id']) ? trim((string) $_GET['client_id']) : '';
        [$_, $clients] = $sb->get('clients', '?select=id,client_name&order=client_name');
        $clients = is_array($clients) ? $clients : [];

        $title = 'Log Interaction';
        ob_start();
        include __DIR__ . '/../views/interactions/create.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layout/master.php';
    }

    private static function store(): void
    {
        $sb = supabase();
        $clientId = trim((string) ($_POST['client_id'] ?? ''));
        $type = $_POST['interaction_type'] ?? 'email';
        if (!in_array($type, self::INTERACTION_TYPES, true)) {
            $type = 'email';
        }
        $stage = $_POST['stage'] ?? 'intro';
        if (!in_array($stage, self::STAGES, true)) {
            $stage = 'intro';
        }
        $interactionDate = trim($_POST['interaction_date'] ?? date('Y-m-d'));
        if ($interactionDate === '') {
            $interactionDate = date('Y-m-d');
        }

        $data = [
            'client_id' => $clientId,
            'interaction_type' => $type,
            'stage' => $stage,
            'subject' => trim($_POST['subject'] ?? ''),
            'notes' => trim($_POST['notes'] ?? ''),
            'interaction_date' => $interactionDate
        ];

        $userId = $_SESSION['user_id'] ?? null;
        if ($userId !== null) {
            $data['created_by'] = $userId;
        }

        [$code, $result] = $sb->post('interactions', $data);

        if ($code >= 200 && $code < 300) {
            header('Location: ' . base_url('?page=clients&id=' . urlencode($clientId)));
            exit;
        }

        $_SESSION['form_error'] = is_array($result) ? ($result['message'] ?? 'Failed to log interaction') : 'Failed to log interaction';
        header('Location: ' . base_url('?page=interactions/create&client_id=' . urlencode($clientId)));
        exit;
    }
}
