<?php

$env = parse_ini_file(__DIR__ . '/../../.env');

define('SUPABASE_URL', $env['SUPABASE_URL']);
define('SUPABASE_ANON_KEY', $env['SUPABASE_ANON_KEY']);
define('BASE_URL', $env['BASE_URL'] ?? '/crm/public');
define('SUPABASE_SERVICE_ROLE_KEY', $env['SUPABASE_SERVICE_ROLE_KEY'] ?? '');
