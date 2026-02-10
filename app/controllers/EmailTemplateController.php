<?php

class EmailTemplateController
{
    public static function generator(): void
    {
        $sb = supabase();
        [$_, $templates] = $sb->get('email_templates', '?is_active=eq.true&select=id,name,subject,body&order=name');
        $templates = is_array($templates) ? $templates : [];

        [$_, $clients] = $sb->get('clients', '?select=id,client_name&order=client_name');
        $clients = is_array($clients) ? $clients : [];

        $title = 'Email Generator';
        ob_start();
        include __DIR__ . '/../views/email_generator/index.php';
        $content = ob_get_clean();
        include __DIR__ . '/../views/layout/master.php';
    }
}
