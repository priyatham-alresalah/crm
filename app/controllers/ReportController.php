<?php

class ReportController
{
    public static function index(): void
    {
        $sb = supabase();

        $dateFrom = $_GET['date_from'] ?? date('Y-m-01');
        $dateTo = $_GET['date_to'] ?? date('Y-m-d');

        [$_, $clients] = $sb->get('clients', '?select=status');
        $clientsByStatus = [];
        if (is_array($clients)) {
            foreach ($clients as $row) {
                $s = $row['status'] ?? '—';
                $clientsByStatus[$s] = ($clientsByStatus[$s] ?? 0) + 1;
            }
        }

        [$_, $interactions] = $sb->get('interactions', '?select=created_by,created_at&order=created_at.desc');
        $interactionsPerUser = [];
        $followUpsInRange = 0;
        if (is_array($interactions)) {
            foreach ($interactions as $row) {
                $uid = $row['created_by'] ?? 'unknown';
                $interactionsPerUser[$uid] = ($interactionsPerUser[$uid] ?? 0) + 1;
                $created = $row['created_at'] ?? '';
                $datePart = substr($created, 0, 10);
                if ($datePart >= $dateFrom && $datePart <= $dateTo) {
                    $followUpsInRange++;
                }
            }
        }

        $title = 'Reports';
        ob_start();
        include __DIR__ . '/../views/reports/index.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layout/master.php';
    }
}
