<?php

/**
 * Email Generator – read-only. Fetches active templates, replaces placeholders.
 * No sending, no DB writes.
 */
class EmailTemplateController
{
    private const COMPANY_NAME = 'Al Resalah Consultancies & Training';
    private const CATEGORIES = ['All', 'Intro', 'Follow-up', 'Reminder', 'Closure'];

    private static function replacePlaceholders(string $text): string
    {
        $text = str_replace('{{company_name}}', self::COMPANY_NAME, $text);
        $text = str_replace('{{today_date}}', date('Y-m-d'), $text);
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
            if ($categoryFilter !== '' && $categoryFilter !== 'All' && in_array($categoryFilter, array_slice(self::CATEGORIES, 1), true) && $cat !== $categoryFilter) {
                continue;
            }
            $subject = (string) ($t['subject'] ?? $t['name'] ?? '');
            $body = (string) ($t['body'] ?? $t['content'] ?? '');
            $templates[] = [
                'id' => $t['id'] ?? null,
                'name' => $t['name'] ?? '',
                'category' => $cat,
                'subject' => self::replacePlaceholders($subject),
                'body' => self::replacePlaceholders($body),
            ];
        }

        $title = 'Email Generator';
        $categories = self::CATEGORIES;
        ob_start();
        include __DIR__ . '/../views/email_generator/index.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layout/master.php';
    }
}
