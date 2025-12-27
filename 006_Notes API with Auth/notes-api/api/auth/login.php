<?php
require_once '../config/cors.php';
require_once '../config/functions.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->email) && !empty($data->password)) {
    $email = htmlspecialchars(strip_tags($data->email));
    $password = $data->password;
    
    $query = "SELECT id, username, email, password FROM users WHERE email = :email";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (password_verify($password, $user['password'])) {
            // Start session
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            // Generate API token
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 week'));
            
            // Create user_tokens table if it doesn't exist
            $createTable = "CREATE TABLE IF NOT EXISTS user_tokens (
                id INT PRIMARY KEY AUTO_INCREMENT,
                user_id INT NOT NULL,
                token VARCHAR(64) UNIQUE NOT NULL,
                expires_at DATETIME NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )";
            $db->exec($createTable);
            
            // Insert token
            $tokenQuery = "INSERT INTO user_tokens (user_id, token, expires_at) VALUES (:user_id, :token, :expires_at)";
            $tokenStmt = $db->prepare($tokenQuery);
            $tokenStmt->bindParam(':user_id', $user['id']);
            $tokenStmt->bindParam(':token', $token);
            $tokenStmt->bindParam(':expires_at', $expires);
            $tokenStmt->execute();
            
            jsonResponse([
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'token' => $token,
                'token_expires' => $expires
            ], "Login successful");
        } else {
            jsonResponse(null, "Invalid credentials", 401);
        }
    } else {
        jsonResponse(null, "User not found", 404);
    }
} else {
    jsonResponse(null, "Email and password required", 400);
}
?>