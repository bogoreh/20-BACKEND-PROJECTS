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

if (!empty($data->id) && !empty($data->title)) {
    $id = $data->id;
    $title = htmlspecialchars(strip_tags($data->title));
    $content = !empty($data->content) ? htmlspecialchars(strip_tags($data->content)) : '';
    
    // Check if note belongs to user
    $check_query = "SELECT id FROM notes WHERE id = :id AND user_id = :user_id";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->bindParam(':id', $id);
    $check_stmt->bindParam(':user_id', $user['id']);
    $check_stmt->execute();
    
    if ($check_stmt->rowCount() == 0) {
        jsonResponse(null, "Note not found or unauthorized", 404);
    }
    
    $query = "UPDATE notes SET title = :title, content = :content WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':content', $content);
    
    if ($stmt->execute()) {
        // Fetch updated note
        $query = "SELECT * FROM notes WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $note = $stmt->fetch(PDO::FETCH_ASSOC);
        jsonResponse($note, "Note updated successfully");
    } else {
        jsonResponse(null, "Failed to update note", 500);
    }
} else {
    jsonResponse(null, "ID and title are required", 400);
}
?>