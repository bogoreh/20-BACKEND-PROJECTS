<?php
require_once '../config/cors.php';
require_once '../config/functions.php';

session_start();

// Clear session
session_unset();
session_destroy();

// If token is provided via header, delete it
$headers = apache_request_headers();
if (isset($headers['Authorization'])) {
    $token = str_replace('Bearer ', '', $headers['Authorization']);
    
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "DELETE FROM user_tokens WHERE token = :token";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':token', $token);
    $stmt->execute();
}

jsonResponse(null, "Logged out successfully");
?>