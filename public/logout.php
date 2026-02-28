<?php
require __DIR__ . '/../app/config/env.php';
require __DIR__ . '/../app/core/Session.php';
require __DIR__ . '/../app/helpers/functions.php';
require __DIR__ . '/../app/controllers/AuthController.php';

Session::start();
AuthController::logout();
