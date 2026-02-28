<?php

class CallingScriptController
{
    /** Map DB enum values to display labels (enum may use follow_up, closure, etc.) */
    private const STAGE_LABELS = [
        'intro' => 'Intro',
        'follow_up' => 'Follow-up',
        'followup' => 'Follow-up',
        'objection_handling' => 'Objection handling',
        'closure' => 'Closing',
        'closing' => 'Closing',
    ];

    public static function index(): void
    {
        Auth::requireLogin();
        $sb = supabase();
        [$status, $scripts] = $sb->get('calling_scripts', '?select=id,stage,title,content&order=stage.asc,title.asc');
        $scripts = ($status >= 400 || !is_array($scripts)) ? [] : $scripts;

        $stages = ['Intro', 'Follow-up', 'Objection handling', 'Closing'];
        $byStage = array_fill_keys($stages, []);
        foreach ($scripts as $row) {
            $stageKey = $row['stage'] ?? 'intro';
            $label = self::STAGE_LABELS[$stageKey] ?? ucfirst((string) $stageKey);
            if (!isset($byStage[$label])) {
                $byStage[$label] = [];
            }
            $byStage[$label][] = $row;
        }

        $title = 'Calling Script';
        ob_start();
        include __DIR__ . '/../views/calling_script/index.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layout/master.php';
    }
}
