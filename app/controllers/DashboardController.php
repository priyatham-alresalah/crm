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
        $followUpsToday = $sb->count('interactions', "?interaction_date=gte.{$today}&interaction_date=lte.{$today}");

        [$_, $clients] = $sb->get('clients', '?select=client_status');
        $statusCounts = [];
        $statuses = ['new', 'contacted', 'converted', 'lost'];
        foreach ($statuses as $s) {
            $statusCounts[$s] = 0;
        }
        if (is_array($clients)) {
            foreach ($clients as $row) {
                $s = $row['client_status'] ?? '';
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
