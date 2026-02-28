<?php

class DailyProgressController
{
    private const ACTIVITIES = [
        'calls_made' => 'Calls made to clients',
        'received_calls' => 'Received calls / inquiries',
        'whatsapp_msgs' => 'WhatsApp intro / follow-up messages',
        'emails_sent' => 'Emails sent to clients / prospects',
        'intro_mails_new_existing' => 'Intro mails to new & existing clients',
        'intro_mails_prospects' => 'Intro mails to new prospect clients',
        'reconnect_mails' => 'Reconnecting emails to clients / prospects',
        'followup_quotations' => 'Follow-up on quotations',
        'followup_payments' => 'Follow-up on payments',
        'invoices_issued' => 'Invoices issued',
        'emails_received' => 'Emails received',
        'inquiries_received' => 'Inquiries received',
        'responses_sent' => 'Responses sent for inquiries',
        'quotations_issued' => 'Quotations issued',
        'lpo_received' => 'LPO / confirmation received',
        'trainings_conducted' => 'Trainings conducted (offline/online)',
        'training_schedule_confirmed' => 'Training schedule / confirmations',
        'new_clients' => 'New clients added',
        'trainings_delivered' => 'Trainings delivered (offline/online)',
        'others' => 'Others',
    ];

    public static function index(): void
    {
        Auth::requireLogin();
        $sb = supabase();

        $date = $_GET['date'] ?? date('Y-m-d');
        $query = '?progress_date=eq.' . urlencode($date) . '&select=*&order=created_at.desc';

        [$_, $rows] = $sb->get('daily_progress', $query);
        $entries = is_array($rows) ? $rows : [];

        // Aggregate done counts per activity for the selected date
        $done = [];
        foreach (self::ACTIVITIES as $key => $_label) {
            $done[$key] = 0;
        }
        foreach ($entries as $row) {
            if (!empty($row['activities']) && is_array($row['activities'])) {
                foreach ($row['activities'] as $key => $val) {
                    if (isset($done[$key])) {
                        $done[$key] += (int) $val;
                    }
                }
            }
        }

        // Load targets for current user for this month/year
        $userId = Auth::id() ?? '';
        $targets = [];
        if ($userId !== '') {
            $year = (int) date('Y', strtotime($date));
            $month = (int) date('n', strtotime($date));
            $filter = '?user_id=eq.' . urlencode($userId)
                . '&target_year=eq.' . urlencode((string) $year)
                . '&target_month=eq.' . urlencode((string) $month)
                . '&select=activity,daily_target,monthly_target';
            [$_, $targetRows] = $sb->get('daily_progress_targets', $filter);
            if (is_array($targetRows)) {
                foreach ($targetRows as $t) {
                    $act = $t['activity'] ?? '';
                    if ($act !== '') {
                        $targets[$act] = [
                            'daily_target' => (int) ($t['daily_target'] ?? 0),
                            'monthly_target' => (int) ($t['monthly_target'] ?? 0),
                        ];
                    }
                }
            }
        }

        $title = 'Daily Progress';
        ob_start();
        include __DIR__ . '/../views/daily_progress/index.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layout/master.php';
    }

    public static function create(): void
    {
        Auth::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            self::store();
            return;
        }

        $title = 'Add Daily Progress';
        $error = $_SESSION['form_error'] ?? '';
        if ($error) {
            unset($_SESSION['form_error']);
        }
        ob_start();
        include __DIR__ . '/../views/daily_progress/create.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layout/master.php';
    }

    private static function store(): void
    {
        Auth::requireLogin();
        $userId = Auth::id();
        if ($userId === null) {
            header('Location: ' . base_url());
            exit;
        }

        $progressDate = trim($_POST['progress_date'] ?? '');
        if ($progressDate === '') {
            $progressDate = date('Y-m-d');
        }
        $summary = trim($_POST['summary'] ?? '');

        // Only allow editing past dates when override exists or user is admin
        if (!self::canEditDate($userId, $progressDate)) {
            $_SESSION['form_error'] = 'This day is locked. Please contact an admin to allow editing.';
            header('Location: ' . base_url('?page=daily_progress&date=' . urlencode($progressDate)));
            exit;
        }

        // Build activities payload
        $activities = [];
        foreach (self::ACTIVITIES as $key => $_label) {
            $val = isset($_POST['activity'][$key]) ? (int) $_POST['activity'][$key] : 0;
            if ($val > 0) {
                $activities[$key] = $val;
            }
        }

        $data = [
            'user_id' => $userId,
            'progress_date' => $progressDate,
            'summary' => $summary !== '' ? $summary : null,
            'activities' => !empty($activities) ? $activities : null,
        ];
        $branchId = Auth::branchId();
        if ($branchId !== null && $branchId !== '') {
            $data['branch_id'] = $branchId;
        }

        $sb = supabase();
        [$code, $result] = $sb->post('daily_progress', $data);

        if ($code >= 200 && $code < 300) {
            header('Location: ' . base_url('?page=daily_progress'));
            exit;
        }

        $_SESSION['form_error'] = is_array($result) ? ($result['message'] ?? 'Failed to save daily progress') : 'Failed to save daily progress';
        header('Location: ' . base_url('?page=daily_progress/create'));
        exit;
    }

    public static function activities(): array
    {
        return self::ACTIVITIES;
    }

    private static function canEditDate(string $userId, string $date): bool
    {
        if (Auth::isAdmin()) {
            return true;
        }
        $today = date('Y-m-d');
        if ($date >= $today) {
            return true;
        }

        $sb = supabase();
        $filter = '?user_id=eq.' . urlencode($userId) . '&progress_date=eq.' . urlencode($date) . '&can_edit=eq.true&limit=1';
        [$_, $rows] = $sb->get('daily_progress_overrides', $filter);
        return is_array($rows) && count($rows) > 0;
    }
}

