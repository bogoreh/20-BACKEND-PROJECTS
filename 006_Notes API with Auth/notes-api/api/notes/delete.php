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

if (!empty($data->id)) {
    $id = $data->id;
    
    // Check if note belongs to user
    $check_query = "SELECT id FROM notes WHERE id = :id AND user_id = :user_id";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->bindParam(':id', $id);
    $check_stmt->bindParam(':user_id', $user['id']);
    $check_stmt->execute();
    
    if ($check_stmt->rowCount() == 0) {
        jsonResponse(null, "Note not found or unauthorized", 404);
    }
    
    $query = "DELETE FROM notes WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    
    if ($stmt->execute()) {
        jsonResponse(null, "Note deleted successfully");
    } else {
        jsonResponse(null, "Failed to delete note", 500);
    }
} else {
    jsonResponse(null, "Note ID is required", 400);
}
?>