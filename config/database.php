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

    return $connection;
}
?>