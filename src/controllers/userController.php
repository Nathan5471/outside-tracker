<?php
require_once __DIR__ . '/../models/user.php';

class UserController {
    public function index() {
        $requestUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        if ($requestMethod === 'POST' && $requestUrl === '/signup') {
            $this->handleSignup();
        } else if ($requestMethod === 'POST' && $requestUrl === '/login') {
            $this->handleLogin();
        } else if ($requestMethod === 'GET' && $requestUrl === '/user') {
            $this->getUserById();
        } else if ($requestMethod === 'DELETE' && $requestUrl === '/user') {
            $this->deleteUser();
        }
    }

    private function handleSignup() {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        if (empty($username) || empty($password)) {
            echo "Username and password are required.";
            return;
        }
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $result = User::createUser($username, $hashedPassword);
        if ($result) {
            echo "User created successfully!";
        } else {
            echo "Failed to create user.";
        }
    }

    private function handleLogin() {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        if (empty($username) || empty($password)) {
            echo "Username and password are required.";
            return;
        }
        $user = User::getUserByUsername($username);
        if ($user && password_verify($password, $user['password'])) {
            echo "Login successful!";
        } else {
            echo "Invalid username or password.";
        }
    }

    private function getUserById() {
        $id = $_GET['id'] ?? '';
        if (empty($id)) {
            echo "User ID is required.";
            return;
        }
        $user = User::getUserById($id);
        if ($user) {
            $cleanUser = [
                'id' => $user['id'],
                'username' => $user['username']
            ];
            echo json_encode($cleanUser);
        } else {
            echo "User not found.";
        }
    }

    private function deleteUser() {
        $id = $_GET['id'] ?? '';
        if (empty($id)) {
            echo "User ID is required.";
            return;
        }
        $result = User::deleteUser($id);
        if ($result) {
            echo "User deleted successfully!";
        } else {
            echo "Failed to delete user.";
        }
    }
}
?>