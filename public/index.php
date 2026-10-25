<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$requestUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($requestUrl === '/signup') {
    require_once __DIR__ . '/../src/views/signup.php';
} else if ($requestUrl === '/login') {
    require_once __DIR__ . '/../src/views/login.php';
} else if ($requestUrl === '/') {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }
    require_once __DIR__ . '/../src/views/home.php';
} else if ($requestUrl === '/create') {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }
    require_once __DIR__ . '/../src/views/createActivity.php';
} else if (str_starts_with($requestUrl, '/activity/')) {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }
    require_once __DIR__ . '/../src/views/activity.php';
} else if (str_starts_with($requestUrl, '/api/auth/')) {
    require_once __DIR__ . '/../src/controllers/userController.php';
    $userController = new UserController();
    $userController->index();
} else if (str_starts_with($requestUrl, '/api/activities/')) {
    require_once __DIR__ . '/../src/controllers/activityController.php';
    $activityController = new ActivityController();
    $activityController->index();
}
?>