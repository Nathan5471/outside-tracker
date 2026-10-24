<?php

function getDatabaseConnection() {
    require_once __DIR__ . '/vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    $serverName = $_ENV['SERVER_NAME'];
    $username = $_ENV['USERNAME'];
    $password = $_ENV['PASSWORD'];
    $databaseName = $_ENV['DATABASE_NAME'];
    $connection = new mysqli($serverName, $username, $password, $databaseName);
    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
        return null;
    }

    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL
    )";
    $connection->query($sql);

    return $connection;
}
?>