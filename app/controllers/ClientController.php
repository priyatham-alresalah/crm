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
        [$_, $clients] = $sb->get('clients', '?select=id,client_name,client_status,created_at&order=created_at.desc');
        $raw = is_array($clients) ? $clients : [];
        $list = array_values(array_filter($raw, static function ($row) {
            return trim((string) ($row['client_name'] ?? '')) !== '';
        }));
        [$_, $primaryContacts] = $sb->get('client_contacts', '?is_primary=eq.true&select=client_id,contact_name,contact_phone');
        $primaryByClient = [];
        if (is_array($primaryContacts)) {
            foreach ($primaryContacts as $c) {
                $primaryByClient[$c['client_id'] ?? ''] = $c;
            }
        }

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
        [$_, $contactsRows] = $sb->get('client_contacts', '?client_id=eq.' . urlencode($id) . '&select=*&order=is_primary.desc,contact_name.asc');
        $contacts = is_array($contactsRows) ? $contactsRows : [];

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

        $clientStatus = trim($_POST['client_status'] ?? '');
        $data = [
            'client_name' => trim($_POST['client_name'] ?? ''),
            'address' => trim($_POST['address'] ?? '') ?: null,
            'email' => trim($_POST['email'] ?? '') ?: null,
            'phone' => trim($_POST['phone'] ?? '') ?: null,
        ];
        if ($clientStatus !== '' && in_array($clientStatus, self::CLIENT_STATUSES, true)) {
            $data['client_status'] = $clientStatus;
        }
        if ($userId !== null) {
            $data['created_by'] = $userId;
            $data['assigned_to'] = $userId;
        }
        $branchId = Auth::branchId();
        if ($branchId !== null && $branchId !== '') {
            $data['branch_id'] = $branchId;
        }

        [$code, $result] = $sb->post('clients', $data, true);

        if ($code < 200 || $code >= 300) {
            $msg = is_array($result) ? ($result['message'] ?? 'Failed to create client') : 'Failed to create client';
            $_SESSION['form_error'] = $msg;
            header('Location: ' . base_url('?page=clients/create'));
            exit;
        }

        $created = is_array($result) && isset($result[0]) ? $result[0] : $result;
        $clientId = $created['id'] ?? null;
        if (!$clientId) {
            $_SESSION['form_error'] = 'Client created but could not create primary contact.';
            header('Location: ' . base_url('?page=clients/create'));
            exit;
        }

        $contactName = trim($_POST['contact_name'] ?? '');
        if ($contactName === '') {
            $sb->delete('clients', 'id=eq.' . urlencode($clientId));
            $_SESSION['form_error'] = 'Contact Name is required.';
            header('Location: ' . base_url('?page=clients/create'));
            exit;
        }

        $contactData = [
            'client_id' => $clientId,
            'contact_name' => $contactName,
            'contact_email' => trim($_POST['contact_email'] ?? '') ?: null,
            'contact_phone' => trim($_POST['contact_phone'] ?? '') ?: null,
            'designation' => trim($_POST['designation'] ?? '') ?: null,
            'is_primary' => true,
        ];

        [$contactCode, $contactResult] = $sb->post('client_contacts', $contactData);

        if ($contactCode < 200 || $contactCode >= 300) {
            $sb->delete('clients', 'id=eq.' . urlencode($clientId));
            $msg = is_array($contactResult) ? ($contactResult['message'] ?? 'Failed to create primary contact') : 'Failed to create primary contact';
            $_SESSION['form_error'] = $msg;
            header('Location: ' . base_url('?page=clients/create'));
            exit;
        }

        header('Location: ' . base_url('?page=clients'));
        exit;
    }

    public static function contactAdd(): void
    {
        $clientId = trim($_POST['client_id'] ?? '');
        if ($clientId === '') {
            header('Location: ' . base_url('?page=clients'));
            exit;
        }
        $sb = supabase();
        [$code, $rows] = $sb->get('clients', '?id=eq.' . urlencode($clientId) . '&select=id');
        if ($code !== 200 || !is_array($rows) || count($rows) === 0) {
            header('Location: ' . base_url('?page=clients'));
            exit;
        }
        $contactName = trim($_POST['contact_name'] ?? '');
        if ($contactName === '') {
            $_SESSION['form_error'] = 'Contact name is required.';
            header('Location: ' . base_url('?page=clients&id=' . urlencode($clientId)));
            exit;
        }
        $isPrimary = isset($_POST['is_primary']) && $_POST['is_primary'] === '1';
        if ($isPrimary) {
            $sb->patch('client_contacts', 'client_id=eq.' . urlencode($clientId), ['is_primary' => false]);
        }
        $contactData = [
            'client_id' => $clientId,
            'contact_name' => $contactName,
            'contact_email' => trim($_POST['contact_email'] ?? '') ?: null,
            'contact_phone' => trim($_POST['contact_phone'] ?? '') ?: null,
            'designation' => trim($_POST['designation'] ?? '') ?: null,
            'is_primary' => $isPrimary,
        ];
        [$contactCode, $contactResult] = $sb->post('client_contacts', $contactData);
        if ($contactCode < 200 || $contactCode >= 300) {
            $msg = is_array($contactResult) ? ($contactResult['message'] ?? 'Failed to add contact') : 'Failed to add contact';
            $_SESSION['form_error'] = $msg;
        }
        header('Location: ' . base_url('?page=clients&id=' . urlencode($clientId)));
        exit;
    }

    public static function contactSetPrimary(): void
    {
        $clientId = trim($_GET['client_id'] ?? '');
        $contactId = trim($_GET['id'] ?? '');
        if ($clientId === '' || $contactId === '') {
            header('Location: ' . base_url('?page=clients'));
            exit;
        }
        $sb = supabase();
        [$_, $rows] = $sb->get('client_contacts', '?id=eq.' . urlencode($contactId) . '&client_id=eq.' . urlencode($clientId) . '&select=id');
        if (!is_array($rows) || count($rows) === 0) {
            header('Location: ' . base_url('?page=clients&id=' . urlencode($clientId)));
            exit;
        }
        $sb->patch('client_contacts', 'client_id=eq.' . urlencode($clientId), ['is_primary' => false]);
        [$code, $result] = $sb->patch('client_contacts', 'id=eq.' . urlencode($contactId), ['is_primary' => true]);
        if ($code < 200 || $code >= 300) {
            $_SESSION['form_error'] = 'Could not set primary contact.';
        }
        header('Location: ' . base_url('?page=clients&id=' . urlencode($clientId)));
        exit;
    }
}
