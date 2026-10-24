<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

class Authenticate {
    public static function generateToken($userId) {
        $issuedAt = time();
        $expirationTime = $issuedAt + 60*60*24*365*2; // 2 years
        $payload = [
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'sub' => $userId
        ];
        return JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');
    }
}
?>