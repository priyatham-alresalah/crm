<?php

/**
 * Email Generator – read-only. Fetches active templates, replaces placeholders.
 * No sending, no DB writes.
 */
class EmailTemplateController
{
    private const COMPANY_NAME = 'Al Resalah Consultancies & Training';
    /** enum email_category values => display labels for dropdown */
    private const CATEGORY_OPTIONS = [
        ['value' => 'All', 'label' => 'All'],
        ['value' => 'intro', 'label' => 'Intro'],
        ['value' => 'follow_up', 'label' => 'Follow-up'],
        ['value' => 'meeting_request', 'label' => 'Meeting Request'],
        ['value' => 'reminder', 'label' => 'Reminder'],
        ['value' => 'thank_you', 'label' => 'Thank You'],
        ['value' => 'closure', 'label' => 'Closure'],
    ];

    private static function replacePlaceholders(string $text): string
    {
        $text = str_replace('{{company_name}}', self::COMPANY_NAME, $text);
        $text = str_replace('{{today_date}}', date('Y-m-d'), $text);
        $yourName = Auth::name();
        $text = str_replace('{{your_name}}', $yourName !== null && $yourName !== '' ? $yourName : '{{your_name}}', $text);
        return $text;
    }

    public static function generator(): void
    {
        $sb = supabase();
        $categoryFilter = isset($_GET['category']) ? trim((string) $_GET['category']) : '';
        [$_, $raw] = $sb->get('email_templates', '?is_active=eq.true&select=*&order=name.asc');
        $rawTemplates = is_array($raw) ? $raw : [];

        $templates = [];
        foreach ($rawTemplates as $t) {
            $cat = $t['category'] ?? null;
            $filterValues = array_column(array_slice(self::CATEGORY_OPTIONS, 1), 'value');
            if ($categoryFilter !== '' && $categoryFilter !== 'All' && in_array($categoryFilter, $filterValues, true) && $cat !== $categoryFilter) {
                continue;
            }
            $subject = (string) ($t['subject'] ?? $t['name'] ?? '');
            $body = (string) ($t['body'] ?? '');
            $templates[] = [
                'id' => $t['id'] ?? null,
                'name' => $t['name'] ?? '',
                'category' => $cat,
                'subject' => self::replacePlaceholders($subject),
                'body' => self::replacePlaceholders($body),
            ];
        }

        $title = 'Email Generator';
        $categories = self::CATEGORY_OPTIONS;

        [$_, $clientsRows] = $sb->get('clients', '?select=id,client_name&order=client_name.asc');
        [$_, $primaryContacts] = $sb->get('client_contacts', '?is_primary=eq.true&select=client_id,contact_name');
        $clientsForEmail = [];
        $contactsByClient = [];
        if (is_array($primaryContacts)) {
            foreach ($primaryContacts as $c) {
                $contactsByClient[$c['client_id'] ?? ''] = $c['contact_name'] ?? '';
            }
        }
        if (is_array($clientsRows)) {
            foreach ($clientsRows as $row) {
                $cid = $row['id'] ?? null;
                $clientsForEmail[] = [
                    'id' => $cid,
                    'client_name' => trim((string) ($row['client_name'] ?? '')),
                    'contact_name' => $contactsByClient[$cid] ?? '',
                ];
            }
        }

        ob_start();
        include __DIR__ . '/../views/email_generator/index.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layout/master.php';
    }
}
