<?php
require_once '../config/cors.php';
require_once '../config/functions.php';

$user = validateToken();
if (!$user) {
    jsonResponse(null, "Unauthorized", 401);
}

$database = new Database();
$db = $database->getConnection();

// Get single note by ID
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $query = "SELECT * FROM notes WHERE id = :id AND user_id = :user_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':user_id', $user['id']);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $note = $stmt->fetch(PDO::FETCH_ASSOC);
        jsonResponse($note);
    } else {
        jsonResponse(null, "Note not found", 404);
    }
} 
// Get all notes for user
else {
    $query = "SELECT * FROM notes WHERE user_id = :user_id ORDER BY updated_at DESC";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user['id']);
    $stmt->execute();
    
    $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    jsonResponse($notes);
}
?>