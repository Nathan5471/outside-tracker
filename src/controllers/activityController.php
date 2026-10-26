<?php
require_once __DIR__ . '/../models/activity.php';
require_once __DIR__ . '/../models/user.php';

class ActivityController {
    public function index() {
        $requestUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        if ($requestMethod === 'POST' && $requestUrl === '/api/activities/create') {
            $this->handleCreate();
        } else if ($requestMethod === 'PUT' && $requestUrl === '/api/activities/update') {
            $this->handleUpdate();
        } else if ($requestMethod === 'GET' && $requestUrl === '/api/activities/get') {
            $this->handleGetActivity();
        } else if ($requestMethod === 'GET' && $requestUrl === '/api/activities/user') {
            $this->handleGetUserActivities();
        } else if ($requestMethod === 'GET' && $requestUrl === '/api/activities/export') {
            $this->handleExportActivities();
        } else if ($requestMethod === 'DELETE' && str_starts_with($requestUrl, '/api/activities/delete/')) {
            $this->handleDeleteActivity();
        }
    }

    private function handleCreate() {
        $body = json_decode(file_get_contents('php://input'), true);
        $userId = $_SESSION['user_id'] ?? '';
        $activityName = $body['activityName'] ?? '';
        $activityDate = $body['activityDate'] ?? '';
        $activityLength = $body['activityLength'] ?? '';
        if (empty($userId)) {
            $this->sendResponse(['error' => 'You are not logged in!'], 401);
        }
        if (empty($activityName) || empty($activityDate) || empty($activityLength)) {
            $this->sendResponse(['error' => 'Name, date, and length are required.'], 400);
        }
        $user = User::getUserById($userId);
        if (!$user) {
            unset($_SESSION['user_id']);
            $this->sendResponse(['error' => 'You are not logged in!'], 401);
        }

        $activityId = Activity::createActivity($userId, $activityName, $activityDate, $activityLength);
        if ($activityId) {
            $this->sendResponse(['activityId' => $activityId], 201);
        } else {
            $this->sendResponse(['error' => 'Failed to create activity.'], 500);
        }
    }

    private function handleUpdate() {
        $body = json_decode(file_get_contents('php://input'), true);
        $userId = $_SESSION['user_id'] ?? '';
        $activityId = $body['activityId'] ?? '';
        $activityName = $body['activityName'] ?? '';
        $activityDate = $body['activityDate'] ?? '';
        $activityLength = $body['activityLength'] ?? '';
        if (empty($userId)) {
            $this->sendResponse(['error' => 'You are not logged in!'], 401);
        }
        if (empty($activityId) || empty($activityName) || empty($activityDate) || empty($activityLength)) {
            $this->sendResponse(['error' => 'Activity ID, name, date, and length are required.'], 400);
        }
        $user = User::getUserById($userId);
        if (!$user) {
            unset($_SESSION['user_id']);
            $this->sendResponse(['error' => 'You are not logged in!'], 401);
        }
        $activity = Activity::getActivityById($activityId);
        if (!$activity || $activity['userId'] != $userId) {
            $this->sendResponse(['error' => 'Activity not found.'], 404);
        }

        $success = Activity::updateActivity($activityId, $activityName, $activityDate, $activityLength);
        if ($success) {
            $this->sendResponse(['success' => true], 200);
        } else {
            $this->sendResponse(['error' => 'Failed to update activity.'], 500);
        }
    }

    private function handleGetActivity() {
        $userId = $_SESSION['user_id'] ?? '';
        $id = $_GET['id'] ?? '';
        if (empty($userId)) {
            $this->sendResponse(['error' => 'You are not logged in!'], 401);
        }
        if (empty($id)) {
            $this->sendResponse(['error' => 'Activity ID is required.'], 400);
        }
        $user = User::getUserById($userId);
        if (!$user) {
            unset($_SESSION['user_id']);
            $this->sendResponse(['error' => 'You are not logged in!'], 401);
        }
        $activity = Activity::getActivityById($id);
        if (!$activity || $activity['userId'] != $userId) {
            $this->sendResponse(['error' => 'Activity not found.'], 404);
        }
        $this->sendResponse($activity, 200);
    }

    private function handleGetUserActivities() {
        $userId = $_SESSION['user_id'] ?? '';
        if (empty($userId)) {
            $this->sendResponse(['error' => 'You are not logged in!'], 401);
        }
        $user = User::getUserById($userId);
        if (!$user) {
            unset($_SESSION['user_id']);
            $this->sendResponse(['error' => 'You are not logged in!'], 401);
        }
        $activities = Activity::getUserAcitivities($userId);
        $this->sendResponse($activities, 200);
    }

    private function handleExportActivities() {
        $userId = $_SESSION['user_id'] ?? '';
        if (empty($userId)) {
            $this->sendResponse(['error' => 'You are not logged in!'], 401);
        }
        $user = User::getUserById($userId);
        if (!$user) {
            unset($_SESSION['user_id']);
            $this->sendResponse(['error' => 'You are not logged in!'], 401);
        }
        $activities = Activity::getUserAcitivities($userId);
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="outside-activities-export.csv"');
        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'Name', 'Date', 'Length (hours)']);
        foreach ($activities as $activity) {
            fputcsv($output, [$activity['id'], $activity['activityName'], $activity['activityDate'], $activity['activityLength']]);
        }
        fclose($output);
        exit;
    }

    private function handleDeleteActivity() {
        $userId = $_SESSION['user_id'] ?? '';
        $id = preg_replace('/\/api\/activities\/delete\/(\d+)/', '$1', $_SERVER['REQUEST_URI']);
        if (empty($userId)) {
            $this->sendResponse(['error' => 'You are not logged in!'], 401);
        }
        if (empty($id)) {
            $this->sendResponse(['error' => 'Activity ID is required.'], 400);
        }
        $user = User::getUserById($userId);
        if (!$user) {
            unset($_SESSION['user_id']);
            $this->sendResponse(['error' => 'You are not logged in!'], 401);
        }
        $activity = Activity::getActivityById($id);
        if (!$activity || $activity['userId'] != $userId) {
            $this->sendResponse(['error' => 'Activity not found.'], 404);
        }
        $success = Activity::deleteActivity($id);
        if ($success) {
            $this->sendResponse(['message' => 'Activity deleted successfully!'], 200);
        } else {
            $this->sendResponse(['error' => 'Failed to delete activity.'], 500);
        }
    }

    private function sendResponse(mixed $data, int $statusCode) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
?>