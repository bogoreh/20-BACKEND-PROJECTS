<?php
// models/Question.php
class Question {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function findByQuizId($quizId) {
        $this->db->query('SELECT * FROM questions WHERE quiz_id = :quiz_id ORDER BY id');
        $this->db->bind(':quiz_id', $quizId);
        return $this->db->resultSet();
    }
    
    public function findById($id) {
        $this->db->query('SELECT * FROM questions WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
    
    public function create($data) {
        $this->db->query(
            'INSERT INTO questions (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_answer, image_url) 
             VALUES (:quiz_id, :question_text, :option_a, :option_b, :option_c, :option_d, :correct_answer, :image_url)'
        );
        
        $this->db->bind(':quiz_id', $data['quiz_id']);
        $this->db->bind(':question_text', $data['question_text']);
        $this->db->bind(':option_a', $data['option_a']);
        $this->db->bind(':option_b', $data['option_b']);
        $this->db->bind(':option_c', $data['option_c'] ?? null);
        $this->db->bind(':option_d', $data['option_d'] ?? null);
        $this->db->bind(':correct_answer', $data['correct_answer']);
        $this->db->bind(':image_url', $data['image_url'] ?? null);
        
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }
    
    public function update($id, $data) {
        $fields = [];
        $params = [':id' => $id];
        
        $allowedFields = ['question_text', 'option_a', 'option_b', 'option_c', 'option_d', 'correct_answer', 'image_url'];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fields[] = $field . ' = :' . $field;
                $params[':' . $field] = $data[$field];
            }
        }
        
        if (empty($fields)) return false;
        
        $sql = 'UPDATE questions SET ' . implode(', ', $fields) . ' WHERE id = :id';
        $this->db->query($sql);
        
        foreach ($params as $key => $value) {
            $this->db->bind($key, $value);
        }
        
        return $this->db->execute();
    }
    
    public function delete($id) {
        $this->db->query('DELETE FROM questions WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
?>