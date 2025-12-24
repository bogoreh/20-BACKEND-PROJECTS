<?php
require_once __DIR__ . '/../utils/Database.php';

class Comment {
    private $conn;
    
    public function __construct() {
        $this->conn = Database::getConnection();
    }
    
    public function create($postId, $userId, $content) {
        $created_at = date('Y-m-d H:i:s');
        
        $stmt = $this->conn->prepare("INSERT INTO comments (post_id, user_id, content, created_at) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $postId, $userId, $content, $created_at);
        
        if ($stmt->execute()) {
            return [
                'id' => $this->conn->insert_id,
                'post_id' => $postId,
                'user_id' => $userId,
                'content' => $content,
                'created_at' => $created_at
            ];
        }
        
        return false;
    }
    
    public function getByPostId($postId) {
        $stmt = $this->conn->prepare("
            SELECT c.*, u.username 
            FROM comments c 
            JOIN users u ON c.user_id = u.id 
            WHERE c.post_id = ? 
            ORDER BY c.created_at ASC
        ");
        $stmt->bind_param("i", $postId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $comments = [];
        while ($row = $result->fetch_assoc()) {
            $comments[] = $row;
        }
        
        return $comments;
    }
    
    public function delete($commentId, $userId) {
        $stmt = $this->conn->prepare("DELETE FROM comments WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $commentId, $userId);
        
        return $stmt->execute();
    }
}

    // Add these methods to the Comment class:

    public function getById($id) {
        $stmt = $this->conn->prepare("
            SELECT c.*, u.username, p.title as post_title 
            FROM comments c 
            JOIN users u ON c.user_id = u.id 
            JOIN posts p ON c.post_id = p.id 
            WHERE c.id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return null;
        }
        
        return $result->fetch_assoc();
    }
    
    public function getByUserId($userId) {
        $stmt = $this->conn->prepare("
            SELECT c.*, p.title as post_title, p.slug as post_slug 
            FROM comments c 
            JOIN posts p ON c.post_id = p.id 
            WHERE c.user_id = ? 
            ORDER BY c.created_at DESC
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $comments = [];
        while ($row = $result->fetch_assoc()) {
            $comments[] = $row;
        }
        
        return $comments;
    }
    
    public function update($id, $content) {
        $updated_at = date('Y-m-d H:i:s');
        
        $stmt = $this->conn->prepare("
            UPDATE comments 
            SET content = ?, updated_at = ? 
            WHERE id = ?
        ");
        $stmt->bind_param("ssi", $content, $updated_at, $id);
        
        return $stmt->execute();
    }
?>