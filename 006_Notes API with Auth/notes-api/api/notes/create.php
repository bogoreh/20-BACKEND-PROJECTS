<?php
require_once '../config/cors.php';
require_once '../config/functions.php';

$user = validateToken();
if (!$user) {
    jsonResponse(null, "Unauthorized", 401);
}

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->title)) {
    $title = htmlspecialchars(strip_tags($data->title));
    $content = !empty($data->content) ? htmlspecialchars(strip_tags($data->content)) : '';
    
    $query = "INSERT INTO notes (user_id, title, content) VALUES (:user_id, :title, :content)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user['id']);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':content', $content);
    
    if ($stmt->execute()) {
        $note_id = $db->lastInsertId();
        
        // Fetch the created note
        $query = "SELECT * FROM notes WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $note_id);
        $stmt->execute();
        
        $note = $stmt->fetch(PDO::FETCH_ASSOC);
        jsonResponse($note, "Note created successfully", 201);
    } else {
        jsonResponse(null, "Failed to create note", 500);
    }
} else {
    jsonResponse(null, "Title is required", 400);
}
?>