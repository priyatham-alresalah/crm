<?php

class DashboardController
{
    public static function index(): void
    {
        $sb = supabase();

        $totalClients = $sb->count('clients');
        $totalInteractions = $sb->count('interactions');

        $today = date('Y-m-d');
        $tomorrow = date('Y-m-d', strtotime($today . ' +1 day'));
        $followUpsToday = $sb->count('interactions', "?created_at=gte.{$today}&created_at=lt.{$tomorrow}");

        [$status, $clients] = $sb->get('clients', '?select=status');
        $statusCounts = [];
        $statuses = ['Intro Email Sent', 'Follow-up Email Sent', 'Client Responded', 'No Response from Client', 'Client Acquired'];
        foreach ($statuses as $s) {
            $statusCounts[$s] = 0;
        }
        if (is_array($clients)) {
            foreach ($clients as $row) {
                $s = $row['status'] ?? '';
                $statusCounts[$s] = ($statusCounts[$s] ?? 0) + 1;
            }
        }

        $title = 'Dashboard';
        ob_start();
        include __DIR__ . '/../views/dashboard.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layout/master.php';
    }
}
