<?php
require_once __DIR__ . '/../models/user.php';

class UserController {
    public function index() {
        $requestUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        if ($requestMethod === 'POST' && $requestUrl === '/api/user/signup') {
            $this->handleSignup();
        } else if ($requestMethod === 'POST' && $requestUrl === '/api/user/login') {
            $this->handleLogin();
        } else if ($requestMethod === 'POST' && $requestUrl === '/api/user/logout') {
            $this->logout();
        } else if ($requestMethod === 'GET' && $requestUrl === '/api/user/user') {
            $this->getUserById();
        } else if ($requestMethod === 'DELETE' && $requestUrl === '/api/user/user') {
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
            $_SESSION['user_id'] = $result['id'];
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
            $_SESSION['user_id'] = $user['id'];
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

    private function logout() {
        unset($_SESSION['user_id']);
        echo "Logout successful!";
    }

    private function deleteUser() {
        $id = $_SESSION['user_id'] ?? '';
        if (empty($id)) {
            echo "User ID is required.";
            return;
        }
        $result = User::deleteUser($id);
        if ($result) {
            echo "User deleted successfully!";
            unset($_SESSION['user_id']);
        } else {
            echo "Failed to delete user.";
        }
    }
}
?>