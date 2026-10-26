<?php

function getDatabaseConnection() {
    if (file_exists(__DIR__ . '/../.env')) {
        $file = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($file as $line) {
            list($key, $value) = explode('=', $line, 2);
            $name = trim($key);
            $value = trim($value);
            if (!getenv($name)) {
                putenv("$name=$value");
            }
        }
    }

    $serverName = getenv('SERVER_NAME');
    $username = getenv('USERNAME');
    $password = getenv('PASSWORD');
    $databaseName = getenv('DATABASE_NAME');
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
    $sql = "CREATE TABLE IF NOT EXISTS activities (
        id INT AUTO_INCREMENT PRIMARY KEY,
        userId INT NOT NULL,
        activityName VARCHAR(255) NOT NULL,
        activityDate DATE NOT NULL,
        activityLength INT NOT NULL,
        FOREIGN KEY (userId) REFERENCES users(id)
    )";
    $connection->query($sql);

    return $connection;
}
?>