<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$requestUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($requestUrl === '/signup') {
    require_once __DIR__ . '/../src/views/signup.php';
} else if ($requestUrl === '/login') {
    require_once __DIR__ . '/../src/views/login.php';
} else if (str_starts_with($requestUrl, '/api/auth/')) {
    require_once __DIR__ . '/../src/controllers/userController.php';
    $userController = new UserController();
    $userController->index();
}
?>