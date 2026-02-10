<?php

/*
|--------------------------------------------------------------------------
| Bootstrap
|--------------------------------------------------------------------------
*/
require __DIR__ . '/../app/config/env.php';
require __DIR__ . '/../app/core/Session.php';
require __DIR__ . '/../app/core/SupabaseClient.php';
require __DIR__ . '/../app/core/Auth.php';
require __DIR__ . '/../app/helpers/functions.php';
require __DIR__ . '/../app/controllers/AuthController.php';

Session::start();

/*
|--------------------------------------------------------------------------
| Basic Routing Helpers
|--------------------------------------------------------------------------
*/
$method = $_SERVER['REQUEST_METHOD'];
$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

/*
| Strip script base path so routing works for /crm, /crm/public, or cPanel subdirs.
*/
$scriptPath = dirname($_SERVER['SCRIPT_NAME']);
if ($scriptPath !== '/' && strpos($uri, $scriptPath) === 0) {
    $uri = substr($uri, strlen($scriptPath)) ?: '/';
}
$uri = $uri ?: '/';

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/
if ($method === 'POST' && isset($_POST['email'], $_POST['password'])) {
    AuthController::login();
    exit;
}

if ($uri === '/logout' || (isset($_GET['action']) && ($_GET['action'] ?? '') === 'logout')) {
    AuthController::logout();
    exit;
}

/*
|--------------------------------------------------------------------------
| GUEST ROUTES – require login
|--------------------------------------------------------------------------
*/
if (!Auth::check()) {
    require __DIR__ . '/login.php';
    exit;
}

/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES
|--------------------------------------------------------------------------
*/
$page = $_GET['page'] ?? 'dashboard';

switch ($page) {

    case 'clients':
        require __DIR__ . '/../app/controllers/ClientController.php';
        ClientController::index();
        break;

    case 'clients/create':
        require __DIR__ . '/../app/controllers/ClientController.php';
        ClientController::create();
        break;

    case 'interactions':
        require __DIR__ . '/../app/controllers/InteractionController.php';
        InteractionController::index();
        break;

    case 'interactions/create':
        require __DIR__ . '/../app/controllers/InteractionController.php';
        InteractionController::create();
        break;

    case 'email_generator':
        require __DIR__ . '/../app/controllers/EmailTemplateController.php';
        EmailTemplateController::generator();
        break;

    case 'calling_script':
        require __DIR__ . '/../app/controllers/CallingScriptController.php';
        CallingScriptController::index();
        break;

    case 'reports':
        require __DIR__ . '/../app/controllers/ReportController.php';
        ReportController::index();
        break;

    default:
        require __DIR__ . '/../app/controllers/DashboardController.php';
        DashboardController::index();
        break;
}
