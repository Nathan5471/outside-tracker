<?php
require_once __DIR__ . '/../config/database.php';

class User {
  public static function createUser($username, $password) {
    $db = getDatabaseConnection();
    if (!$db) {
        return null;
    }

    $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $result = $stmt->execute();
    $stmt->close();
    $db->close();
    return $result;
  }

  public static function getUserById($id) {
    $db = getDatabaseConnection();
    if (!$db) {
        return null;
    }
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    $db->close();
    return $result->fetch_assoc();
  }

  public static function getUserByUsername($username) {
    $db = getDatabaseConnection();
    if (!$db) {
        return null;
    }
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    $db->close();
    return $result->fetch_assoc();
  }

  public static function deleteUser($id) {
    $db = getDatabaseConnection();
    if (!$db) {
        return null;
    }
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $id);
    $result = $stmt->execute();
    $stmt->close();
    $db->close();
    return $result;
  }
}
?>