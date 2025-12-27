<?php
require_once 'database.php';

function validateToken() {
    $headers = apache_request_headers();
    
    if (isset($headers['Authorization'])) {
        $token = str_replace('Bearer ', '', $headers['Authorization']);
        
        $database = new Database();
        $db = $database->getConnection();
        
        $query = "SELECT u.id, u.username, u.email FROM users u 
                  INNER JOIN user_tokens ut ON u.id = ut.user_id 
                  WHERE ut.token = :token AND ut.expires_at > NOW()";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            return $user;
        }
    }
    
    // Fallback to session (for web interface)
    session_start();
    if (isset($_SESSION['user_id'])) {
        $database = new Database();
        $db = $database->getConnection();
        
        $query = "SELECT id, username, email FROM users WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $_SESSION['user_id']);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
    
    return null;
}

function jsonResponse($data = null, $message = "", $status = 200) {
    header('Content-Type: application/json');
    http_response_code($status);
    
    $response = [
        'status' => $status < 400 ? 'success' : 'error',
        'message' => $message,
        'data' => $data
    ];
    
    echo json_encode($response);
    exit;
}
?>