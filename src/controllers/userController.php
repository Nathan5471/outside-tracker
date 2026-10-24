<?php
require_once __DIR__ . '/../models/user.php';

class UserController {
    public function index() {
        $requestUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        if ($requestMethod === 'POST' && $requestUrl === '/api/auth/signup') {
            $this->handleSignup();
        } else if ($requestMethod === 'POST' && $requestUrl === '/api/auth/login') {
            $this->handleLogin();
        } else if ($requestMethod === 'POST' && $requestUrl === '/api/auth/logout') {
            $this->logout();
        } else if ($requestMethod === 'GET' && $requestUrl === '/api/auth/user') {
            $this->getUserById();
        } else if ($requestMethod === 'DELETE' && $requestUrl === '/api/auth/user') {
            $this->deleteUser();
        }
    }

    private function handleSignup() {
        $body = json_decode(file_get_contents('php://input'), true);
        $username = $body['username'] ?? '';
        $password = $body['password'] ?? '';
        if (empty($username) || empty($password)) {
            $this->sendResponse(['error' => 'Username and password are required.'], 400);
        }
        if (User::getUserByUsername($username)) {
            $this->sendResponse(['error' => 'Username already exists.'], 400);
        }
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $result = User::createUser($username, $hashedPassword);
        if ($result) {
            $this->sendResponse(['message' => 'User created successfully!'], 201);
        } else {
            $this->sendResponse(['error' => 'Failed to create user.'], 500);
        }
    }

    private function handleLogin() {
        $body = json_decode(file_get_contents('php://input'), true);
        $username = $body['username'] ?? '';
        $password = $body['password'] ?? '';
        if (empty($username) || empty($password)) {
            $this->sendResponse(['error' => 'Username and password are required.'], 400);
        }
        $user = User::getUserByUsername($username);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $this->sendResponse(['message' => 'Logged in sucessfully!'], 200);
        } else {
            $this->sendResponse(['error' => 'Username or password is incorrect.'], 401);
        }
    }

    private function getUserById() {
        $id = $_GET['id'] ?? '';
        if (empty($id)) {
            $this->sendResponse(['error' => 'User ID is required.'], 400);
        }
        $user = User::getUserById($id);
        if ($user) {
            $cleanUser = [
                'id' => $user['id'],
                'username' => $user['username']
            ];
            $this->sendResponse($cleanUser, 200);
        } else {
            $this->sendResponse(['error' => 'User not found.'], 404);
        }
    }

    private function logout() {
        unset($_SESSION['user_id']);
        $this->sendResponse(['message' => 'Logged out sucessfully!'], 200);
    }

    private function deleteUser() {
        $id = $_SESSION['user_id'] ?? '';
        if (empty($id)) {
            $this->sendResponse(['error' => 'You are not logged in!'], 403);
        }
        $result = User::deleteUser($id);
        if ($result) {
            unset($_SESSION['user_id']);
            $this->sendResponse(['message' => 'Sucessfully deleted user!'], 200);
        } else {
            $this->sendResponse(['error' => 'Failed to delete user.', 500]);
        }
    }

    private function sendResponse($data, int $statusCode) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
?>