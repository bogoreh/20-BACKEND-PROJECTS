<?php
// models/Quiz.php
class Quiz {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function findAll() {
        $this->db->query('SELECT * FROM quizzes ORDER BY created_at DESC');
        return $this->db->resultSet();
    }
    
    public function findById($id) {
        $this->db->query('SELECT * FROM quizzes WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
    
    public function create($data) {
        $this->db->query('INSERT INTO quizzes (title, description) VALUES (:title, :description)');
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':description', $data['description'] ?? '');
        
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }
    
    public function update($id, $data) {
        $fields = [];
        $params = [':id' => $id];
        
        if (isset($data['title'])) {
            $fields[] = 'title = :title';
            $params[':title'] = $data['title'];
        }
        
        if (isset($data['description'])) {
            $fields[] = 'description = :description';
            $params[':description'] = $data['description'];
        }
        
        if (empty($fields)) return false;
        
        $sql = 'UPDATE quizzes SET ' . implode(', ', $fields) . ', updated_at = NOW() WHERE id = :id';
        $this->db->query($sql);
        
        foreach ($params as $key => $value) {
            $this->db->bind($key, $value);
        }
        
        return $this->db->execute();
    }
    
    public function delete($id) {
        $this->db->query('DELETE FROM quizzes WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
?>