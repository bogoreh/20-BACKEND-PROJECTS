<?php
require_once 'vendor/autoload.php'; // You'll need to install firebase/php-jwt via composer

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function generateJWT($user) {
    $payload = [
        'user_id' => $user['id'],
        'username' => $user['username'],
        'email' => $user['email'],
        'iat' => time(),
        'exp' => time() + (60 * 60 * 24) // Token expires in 24 hours
    ];
    
    return JWT::encode($payload, JWT_SECRET, JWT_ALGO);
}

function validateJWT($token) {
    try {
        $decoded = JWT::decode($token, new Key(JWT_SECRET, JWT_ALGO));
        return (array) $decoded;
    } catch (Exception $e) {
        return null;
    }
}

function isLoggedInJWT() {
    if (isset($_COOKIE['auth_token'])) {
        $user = validateJWT($_COOKIE['auth_token']);
        return $user !== null;
    }
    return false;
}

function loginUserJWT($user) {
    $token = generateJWT($user);
    setcookie('auth_token', $token, time() + (60 * 60 * 24), '/', '', false, true); // HTTP Only cookie
    return true;
}

function logoutUserJWT() {
    setcookie('auth_token', '', time() - 3600, '/');
    return true;
}

function getCurrentUserJWT() {
    if (isset($_COOKIE['auth_token'])) {
        return validateJWT($_COOKIE['auth_token']);
    }
    return null;
}

function requireAuthJWT() {
    if (!isLoggedInJWT()) {
        header('Location: login.php');
        exit();
    }
}
?>