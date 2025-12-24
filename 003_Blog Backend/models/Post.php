<?php
require_once __DIR__ . '/../utils/Database.php';

class Post {
    private $conn;
    
    public function __construct() {
        $this->conn = Database::getConnection();
    }
    
    public function create($title, $content, $userId) {
        $slug = $this->createSlug($title);
        $created_at = date('Y-m-d H:i:s');
        
        $stmt = $this->conn->prepare("INSERT INTO posts (title, content, slug, user_id, created_at) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssis", $title, $content, $slug, $userId, $created_at);
        
        if ($stmt->execute()) {
            return [
                'id' => $this->conn->insert_id,
                'title' => $title,
                'content' => $content,
                'slug' => $slug,
                'user_id' => $userId,
                'created_at' => $created_at
            ];
        }
        
        return false;
    }
    
    public function getAll($page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        
        $stmt = $this->conn->prepare("
            SELECT p.*, u.username 
            FROM posts p 
            JOIN users u ON p.user_id = u.id 
            ORDER BY p.created_at DESC 
            LIMIT ? OFFSET ?
        ");
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $posts = [];
        while ($row = $result->fetch_assoc()) {
            $posts[] = $row;
        }
        
        // Get total count
        $countResult = $this->conn->query("SELECT COUNT(*) as total FROM posts");
        $total = $countResult->fetch_assoc()['total'];
        
        return [
            'posts' => $posts,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'total_pages' => ceil($total / $limit)
        ];
    }
    
    public function getBySlug($slug) {
        $stmt = $this->conn->prepare("
            SELECT p.*, u.username 
            FROM posts p 
            JOIN users u ON p.user_id = u.id 
            WHERE p.slug = ?
        ");
        $stmt->bind_param("s", $slug);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return null;
        }
        
        return $result->fetch_assoc();
    }
    
    public function update($id, $title, $content, $userId) {
        $slug = $this->createSlug($title);
        $updated_at = date('Y-m-d H:i:s');
        
        $stmt = $this->conn->prepare("
            UPDATE posts 
            SET title = ?, content = ?, slug = ?, updated_at = ? 
            WHERE id = ? AND user_id = ?
        ");
        $stmt->bind_param("ssssii", $title, $content, $slug, $updated_at, $id, $userId);
        
        return $stmt->execute();
    }
    
    public function delete($id, $userId) {
        // First, delete associated comments
        $stmt = $this->conn->prepare("DELETE FROM comments WHERE post_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        // Then delete the post
        $stmt = $this->conn->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $id, $userId);
        
        return $stmt->execute();
    }
    
    private function createSlug($title) {
        $slug = strtolower($title);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');
        $slug .= '-' . time();
        return $slug;
    }
}

    public function getById($id) {
        $stmt = $this->conn->prepare("
            SELECT p.*, u.username 
            FROM posts p 
            JOIN users u ON p.user_id = u.id 
            WHERE p.id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return null;
        }
        
        return $result->fetch_assoc();
    }

?>