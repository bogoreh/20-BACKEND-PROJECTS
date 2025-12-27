<?php
require_once '../config/cors.php';
require_once '../config/functions.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->username) && !empty($data->email) && !empty($data->password)) {
    $username = htmlspecialchars(strip_tags($data->username));
    $email = htmlspecialchars(strip_tags($data->email));
    $password = password_hash($data->password, PASSWORD_DEFAULT);
    
    // Check if user exists
    $check_query = "SELECT id FROM users WHERE email = :email OR username = :username";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->bindParam(':email', $email);
    $check_stmt->bindParam(':username', $username);
    $check_stmt->execute();
    
    if ($check_stmt->rowCount() > 0) {
        jsonResponse(null, "User already exists", 400);
    }
    
    // Create user
    $query = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);
    
    if ($stmt->execute()) {
        $user_id = $db->lastInsertId();
        
        // Start session for web interface
        session_start();
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        
        jsonResponse([
            'id' => $user_id,
            'username' => $username,
            'email' => $email
        ], "Registration successful", 201);
    } else {
        jsonResponse(null, "Registration failed", 500);
    }
} else {
    jsonResponse(null, "Invalid input", 400);
}
?>