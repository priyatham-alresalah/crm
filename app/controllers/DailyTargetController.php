<?php

class DailyTargetController
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

    private static function requireAdmin(): void
    {
        if (!Auth::isAdmin()) {
            header('Location: ' . base_url());
            exit;
        }
    }

    public static function edit(): void
    {
        self::requireAdmin();
        $sb = supabase();

        // Load all users for dropdown
        [$_, $rows] = $sb->get('users', '?select=id,name,role,branch_id&order=name.asc');
        $rawUsers = is_array($rows) ? $rows : [];
        // Keep only users with some identifying info
        $users = array_values(array_filter($rawUsers, static function ($u) {
            return trim((string) ($u['name'] ?? '')) !== '' || trim((string) ($u['role'] ?? '')) !== '';
        }));
        // Fallback: ensure at least current user is available
        if (empty($users) && Auth::id() !== null) {
            $users[] = [
                'id' => Auth::id(),
                'name' => Auth::name() ?: 'Current User',
                'role' => Auth::role() ?: 'user',
                'branch_id' => null,
            ];
        }

        // Load branches for branch-wise targeting
        [$_, $branchRows] = $sb->get('branches', '?select=id,name&order=name.asc');
        $rawBranches = is_array($branchRows) ? $branchRows : [];
        $branches = array_values(array_filter($rawBranches, static function ($b) {
            return trim((string) ($b['name'] ?? '')) !== '';
        }));

        $mode = $_POST['mode'] ?? $_GET['mode'] ?? 'user'; // user | branch | all
        $userId = $_POST['user_id'] ?? $_GET['user_id'] ?? ($users[0]['id'] ?? '');
        $branchId = $_POST['branch_id'] ?? $_GET['branch_id'] ?? ($branches[0]['id'] ?? '');

        // Month/year selection: no past months; in December, allow all 12 months of next year
        $nowYear = (int) date('Y');
        $nowMonth = (int) date('n');
        if ($nowMonth === 12) {
            $targetYearDefault = $nowYear + 1;
            $startMonth = 1;
        } else {
            $targetYearDefault = $nowYear;
            $startMonth = $nowMonth;
        }
        $targetYear = (int) ($_POST['target_year'] ?? $_GET['target_year'] ?? $targetYearDefault);
        $targetMonth = (int) ($_POST['target_month'] ?? $_GET['target_month'] ?? $startMonth);
        $monthOptions = [];
        for ($m = $startMonth; $m <= 12; $m++) {
            $monthOptions[] = [
                'value' => $m,
                'label' => date('F', mktime(0, 0, 0, $m, 1, $targetYear)),
            ];
        }
        if ($targetMonth < $startMonth || $targetMonth > 12) {
            $targetMonth = $startMonth;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            self::saveTargets($sb, $mode, $userId, $branchId, $users, $targetYear, $targetMonth);
            header('Location: ' . base_url(
                '?page=daily_targets'
                . '&mode=' . urlencode($mode)
                . '&user_id=' . urlencode($userId)
                . '&branch_id=' . urlencode($branchId)
                . '&target_year=' . urlencode((string) $targetYear)
                . '&target_month=' . urlencode((string) $targetMonth)
            ));
            exit;
        }

        // For display, when in "user" mode, load that user's existing targets
        $targets = [];
        if ($mode === 'user' && $userId !== '') {
            $filter = '?user_id=eq.' . urlencode($userId)
                . '&target_year=eq.' . urlencode((string) $targetYear)
                . '&target_month=eq.' . urlencode((string) $targetMonth)
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

        $title = 'Daily Targets';
        ob_start();
        include __DIR__ . '/../views/daily_progress/targets.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layout/master.php';
    }

    private static function saveTargets(
        SupabaseClient $sb,
        string $mode,
        string $userId,
        string $branchId,
        array $users,
        int $targetYear,
        int $targetMonth
    ): void
    {
        // Determine which users to apply to based on mode
        $targetUserIds = [];
        if ($mode === 'all') {
            foreach ($users as $u) {
                if (!empty($u['id'])) {
                    $targetUserIds[] = $u['id'];
                }
            }
        } elseif ($mode === 'branch') {
            foreach ($users as $u) {
                if (!empty($u['id']) && ($u['branch_id'] ?? '') === $branchId) {
                    $targetUserIds[] = $u['id'];
                }
            }
        } else { // user mode
            if ($userId !== '') {
                $targetUserIds[] = $userId;
            }
        }

        if (empty($targetUserIds)) {
            return;
        }

        $targets = $_POST['target'] ?? [];
        $workingDays = self::workingDaysInMonth($targetYear, $targetMonth);
        foreach ($targetUserIds as $uid) {
            // Remove existing targets for this user
            $sb->delete(
                'daily_progress_targets',
                'user_id=eq.' . urlencode($uid)
                . '&target_year=eq.' . urlencode((string) $targetYear)
                . '&target_month=eq.' . urlencode((string) $targetMonth)
            );

            foreach (self::ACTIVITIES as $key => $_label) {
                $daily = isset($targets[$key]['daily']) ? (int) $targets[$key]['daily'] : 0;
                if ($daily <= 0) {
                    continue;
                }
                // Monthly auto-calculated: daily * working days (excluding Sundays)
                $monthly = $daily * $workingDays;
                $sb->post('daily_progress_targets', [
                    'user_id' => $uid,
                    'activity' => $key,
                    'daily_target' => $daily,
                    'monthly_target' => $monthly,
                    'target_year' => $targetYear,
                    'target_month' => $targetMonth,
                ]);
            }
        }
    }

    public static function activities(): array
    {
        return self::ACTIVITIES;
    }

    private static function workingDaysInMonth(int $year, int $month): int
    {
        $count = 0;
        $daysInMonth = (int) date('t', mktime(0, 0, 0, $month, 1, $year));
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $w = (int) date('w', mktime(0, 0, 0, $month, $day, $year)); // 0=Sunday
            if ($w === 0) {
                continue; // Sunday not working
            }
            $count++;
        }
        return $count;
    }
}
