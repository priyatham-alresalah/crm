<?php

class CallingScriptController
{
    public static function index(): void
    {
        $sb = supabase();
        [$_, $scripts] = $sb->get('calling_scripts', '?is_active=eq.true&select=*&order=stage,title');
        $scripts = is_array($scripts) ? $scripts : [];

        $byStage = [];
        $stages = ['Intro', 'Follow-up', 'Objection handling', 'Closing'];
        foreach ($stages as $s) {
            $byStage[$s] = [];
        }
        foreach ($scripts as $row) {
            $stage = $row['stage'] ?? 'Intro';
            if (!isset($byStage[$stage])) {
                $byStage[$stage] = [];
            }
            $byStage[$stage][] = $row;
        }

        $title = 'Calling Script';
        ob_start();
        include __DIR__ . '/../views/calling_script/index.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layout/master.php';
    }
}
