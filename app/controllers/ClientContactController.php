<?php

/**
 * ClientContactController – CRUD for client_contacts.
 * When saving with is_primary = true, all other contacts for the same client are set to is_primary = false.
 */
class ClientContactController
{
    /**
     * List contacts by client_id. If format=json, return JSON; else redirect to client view.
     */
    public static function index(): void
    {
        $clientId = trim($_GET['client_id'] ?? '');
        if ($clientId === '') {
            if (isset($_GET['format']) && $_GET['format'] === 'json') {
                header('Content-Type: application/json');
                echo json_encode([]);
                exit;
            }
            header('Location: ' . base_url('?page=clients'));
            exit;
        }

        $sb = supabase();
        [$code, $rows] = $sb->get('client_contacts', '?client_id=eq.' . urlencode($clientId) . '&select=id,contact_name,contact_email,contact_phone,designation,is_primary&order=is_primary.desc,contact_name.asc');
        $list = (is_array($rows) && $code === 200) ? $rows : [];

        if (isset($_GET['format']) && $_GET['format'] === 'json') {
            header('Content-Type: application/json');
            echo json_encode($list);
            exit;
        }

        header('Location: ' . base_url('?page=clients&id=' . urlencode($clientId)));
        exit;
    }

    /**
     * Add contact. Unset other primaries if is_primary = true.
     */
    public static function store(): void
    {
        Auth::requireLogin();
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
        $data = [
            'client_id' => $clientId,
            'contact_name' => $contactName,
            'contact_email' => trim($_POST['contact_email'] ?? '') ?: null,
            'contact_phone' => trim($_POST['contact_phone'] ?? '') ?: null,
            'designation' => trim($_POST['designation'] ?? '') ?: null,
            'is_primary' => $isPrimary,
        ];
        [$contactCode, $result] = $sb->post('client_contacts', $data);
        if ($contactCode < 200 || $contactCode >= 300) {
            $_SESSION['form_error'] = is_array($result) ? ($result['message'] ?? 'Failed to add contact') : 'Failed to add contact';
        }
        header('Location: ' . base_url('?page=clients&id=' . urlencode($clientId)));
        exit;
    }

    /**
     * Update contact. Unset other primaries if is_primary = true.
     */
    public static function update(): void
    {
        Auth::requireLogin();
        $id = trim($_POST['id'] ?? '');
        if ($id === '') {
            header('Location: ' . base_url('?page=clients'));
            exit;
        }
        $sb = supabase();
        [$_, $rows] = $sb->get('client_contacts', '?id=eq.' . urlencode($id) . '&select=client_id');
        if (!is_array($rows) || count($rows) === 0) {
            header('Location: ' . base_url('?page=clients'));
            exit;
        }
        $clientId = $rows[0]['client_id'] ?? '';
        if ($clientId === '') {
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
        $data = [
            'contact_name' => $contactName,
            'contact_email' => trim($_POST['contact_email'] ?? '') ?: null,
            'contact_phone' => trim($_POST['contact_phone'] ?? '') ?: null,
            'designation' => trim($_POST['designation'] ?? '') ?: null,
            'is_primary' => $isPrimary,
        ];
        [$code, $result] = $sb->patch('client_contacts', 'id=eq.' . urlencode($id), $data);
        if ($code < 200 || $code >= 300) {
            $_SESSION['form_error'] = is_array($result) ? ($result['message'] ?? 'Update failed') : 'Update failed';
        }
        header('Location: ' . base_url('?page=clients&id=' . urlencode($clientId)));
        exit;
    }

    /**
     * Delete contact.
     */
    public static function delete(): void
    {
        Auth::requireLogin();
        $id = trim($_GET['id'] ?? $_POST['id'] ?? '');
        if ($id === '') {
            header('Location: ' . base_url('?page=clients'));
            exit;
        }
        $sb = supabase();
        [$_, $rows] = $sb->get('client_contacts', '?id=eq.' . urlencode($id) . '&select=client_id');
        $clientId = (is_array($rows) && count($rows) > 0) ? ($rows[0]['client_id'] ?? '') : '';
        $sb->delete('client_contacts', 'id=eq.' . urlencode($id));
        if ($clientId !== '') {
            header('Location: ' . base_url('?page=clients&id=' . urlencode($clientId)));
        } else {
            header('Location: ' . base_url('?page=clients'));
        }
        exit;
    }

    /**
     * Set contact as primary (unset others for same client).
     */
    public static function setPrimary(): void
    {
        Auth::requireLogin();
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
        $sb->patch('client_contacts', 'id=eq.' . urlencode($contactId), ['is_primary' => true]);
        header('Location: ' . base_url('?page=clients&id=' . urlencode($clientId)));
        exit;
    }
}
