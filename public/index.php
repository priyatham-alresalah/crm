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
| Only treat POST as login when it's coming from the login page (no ?page=...).
| This prevents other forms that also have email + password fields (e.g. Add User)
| from being mis-routed as login attempts.
*/
if ($method === 'POST' && !isset($_GET['page']) && isset($_POST['email'], $_POST['password'])) {
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
| If the session is missing or has auto-logged out, show the login page
| when hitting the base URL, and redirect there for any other page.
*/
if (!Auth::check()) {
    if (!isset($_GET['page'])) {
        require __DIR__ . '/login.php';
        exit;
    }
    header('Location: ' . base_url());
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

    case 'client_contacts':
        require __DIR__ . '/../app/controllers/ClientContactController.php';
        ClientContactController::index();
        break;

    case 'client_contacts/store':
        require __DIR__ . '/../app/controllers/ClientContactController.php';
        ClientContactController::store();
        break;

    case 'client_contacts/update':
        require __DIR__ . '/../app/controllers/ClientContactController.php';
        ClientContactController::update();
        break;

    case 'client_contacts/delete':
        require __DIR__ . '/../app/controllers/ClientContactController.php';
        ClientContactController::delete();
        break;

    case 'client_contacts/primary':
        require __DIR__ . '/../app/controllers/ClientContactController.php';
        ClientContactController::setPrimary();
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

    case 'users':
        require __DIR__ . '/../app/controllers/UserController.php';
        UserController::index();
        break;

    case 'users/create':
        require __DIR__ . '/../app/controllers/UserController.php';
        UserController::create();
        break;

    case 'users/edit':
        require __DIR__ . '/../app/controllers/UserController.php';
        UserController::edit();
        break;

    case 'users/delete':
        require __DIR__ . '/../app/controllers/UserController.php';
        UserController::delete();
        break;

    case 'branches':
        require __DIR__ . '/../app/controllers/BranchController.php';
        BranchController::index();
        break;

    case 'branches/create':
        require __DIR__ . '/../app/controllers/BranchController.php';
        BranchController::create();
        break;

    case 'branches/edit':
        require __DIR__ . '/../app/controllers/BranchController.php';
        BranchController::edit();
        break;

    case 'daily_targets':
        require __DIR__ . '/../app/controllers/DailyTargetController.php';
        DailyTargetController::edit();
        break;

    case 'daily_progress':
        require __DIR__ . '/../app/controllers/DailyProgressController.php';
        DailyProgressController::index();
        break;

    case 'daily_progress/create':
        require __DIR__ . '/../app/controllers/DailyProgressController.php';
        DailyProgressController::create();
        break;

    case 'profile':
        require __DIR__ . '/../app/controllers/ProfileController.php';
        if ($method === 'POST') {
            ProfileController::update();
        } else {
            ProfileController::index();
        }
        break;

    default:
        require __DIR__ . '/../app/controllers/DashboardController.php';
        DashboardController::index();
        break;
}
