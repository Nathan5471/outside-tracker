<?php
require_once __DIR__ . '/../../config/database.php';

class Activity {
  public static function createActivity(int $userId, string $activityName, string $activityDate, int $activityLength) {
    $db = getDatabaseConnection();
    if (!$db) {
        return null;
    }

    $sql = "INSERT INTO activities (userId, activityName, activityDate, activityLength) VALUES (?, ?, ?, ?)";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("issi", $userId, $activityName, $activityDate, $activityLength);
    $result = $stmt->execute();
    $newId = $db->insert_id;
    $stmt->close();
    $db->close();
    if ($result) {
        return $newId;
    } else {
        return null;
    }
  }

  public static function updateActivity(int $id, string $activityName, string $activityDate, int $activityLength) {
    $db = getDatabaseConnection();
    if (!$db) {
        return null;
    }

    $sql = "UPDATE activities SET activityName = ?, activityDate = ?, activityLength = ? WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("ssii", $activityName, $activityDate, $activityLength, $id);
    $result = $stmt->execute();
    $stmt->close();
    $db->close();
    return $result;
  }

  public static function getActivityById(int $id) {
    $db = getDatabaseConnection();
    if (!$db) {
        return null;
    }

    $sql = "SELECT * FROM activities WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
  }

  public static function getUserAcitivities(int $userId) {
    $db = getDatabaseConnection();
    if (!$db) {
        return null;
    }

    $sql = "SELECT * FROM activities WHERE userId = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result;
  }

  public static function deleteActivity(int $id) {
    $db = getDatabaseConnection();
    if (!$db) {
        return null;
    }

    $sql = "DELETE FROM activities WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $id);
    $result = $stmt->execute();
    $stmt->close();
    $db->close();
    return $result;
  }
}
?>