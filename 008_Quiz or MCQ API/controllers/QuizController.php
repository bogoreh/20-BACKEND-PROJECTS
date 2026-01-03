<?php
// controllers/QuizController.php
require_once '../includes/Database.php';

class QuizController {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function getAllQuizzes() {
        header('Content-Type: application/json');
        
        try {
            $this->db->query('SELECT * FROM quizzes ORDER BY created_at DESC');
            $quizzes = $this->db->resultSet();
            
            // Get question count for each quiz
            foreach ($quizzes as &$quiz) {
                $this->db->query('SELECT COUNT(*) as question_count FROM questions WHERE quiz_id = :quiz_id');
                $this->db->bind(':quiz_id', $quiz['id']);
                $countResult = $this->db->single();
                $quiz['question_count'] = $countResult['question_count'];
            }
            
            echo json_encode([
                'success' => true,
                'data' => $quizzes,
                'count' => count($quizzes)
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }
    
    public function getQuiz($id) {
        header('Content-Type: application/json');
        
        try {
            $this->db->query('SELECT * FROM quizzes WHERE id = :id');
            $this->db->bind(':id', $id);
            $quiz = $this->db->single();
            
            if ($quiz) {
                // Get questions for this quiz
                $this->db->query('SELECT * FROM questions WHERE quiz_id = :quiz_id ORDER BY id');
                $this->db->bind(':quiz_id', $id);
                $questions = $this->db->resultSet();
                
                $quiz['questions'] = $questions;
                $quiz['question_count'] = count($questions);
                
                echo json_encode(['success' => true, 'data' => $quiz]);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Quiz not found']);
            }
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }
    
    public function createQuiz() {
        header('Content-Type: application/json');
        
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data || !isset($data['title']) || empty(trim($data['title']))) {
                http_response_code(400);
                echo json_encode(['error' => 'Quiz title is required']);
                return;
            }
            
            $title = trim($data['title']);
            $description = isset($data['description']) ? trim($data['description']) : '';
            
            $this->db->query('INSERT INTO quizzes (title, description) VALUES (:title, :description)');
            $this->db->bind(':title', $title);
            $this->db->bind(':description', $description);
            
            if ($this->db->execute()) {
                $quizId = $this->db->lastInsertId();
                
                $this->db->query('SELECT * FROM quizzes WHERE id = :id');
                $this->db->bind(':id', $quizId);
                $quiz = $this->db->single();
                
                http_response_code(201);
                echo json_encode([
                    'success' => true,
                    'message' => 'Quiz created successfully',
                    'data' => $quiz
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to create quiz']);
            }
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }
    
    public function updateQuiz($id, $data) {
        header('Content-Type: application/json');
        
        try {
            $this->db->query('SELECT id FROM quizzes WHERE id = :id');
            $this->db->bind(':id', $id);
            $exists = $this->db->single();
            
            if (!$exists) {
                http_response_code(404);
                echo json_encode(['error' => 'Quiz not found']);
                return;
            }
            
            $fields = [];
            $params = [':id' => $id];
            
            if (isset($data['title'])) {
                $fields[] = 'title = :title';
                $params[':title'] = trim($data['title']);
            }
            
            if (isset($data['description'])) {
                $fields[] = 'description = :description';
                $params[':description'] = trim($data['description']);
            }
            
            if (empty($fields)) {
                http_response_code(400);
                echo json_encode(['error' => 'No fields to update']);
                return;
            }
            
            $sql = 'UPDATE quizzes SET ' . implode(', ', $fields) . ', updated_at = NOW() WHERE id = :id';
            $this->db->query($sql);
            
            foreach ($params as $key => $value) {
                $this->db->bind($key, $value);
            }
            
            if ($this->db->execute()) {
                $this->db->query('SELECT * FROM quizzes WHERE id = :id');
                $this->db->bind(':id', $id);
                $quiz = $this->db->single();
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Quiz updated successfully',
                    'data' => $quiz
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to update quiz']);
            }
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }
    
    public function deleteQuiz($id) {
        header('Content-Type: application/json');
        
        try {
            $this->db->query('SELECT id FROM quizzes WHERE id = :id');
            $this->db->bind(':id', $id);
            $exists = $this->db->single();
            
            if (!$exists) {
                http_response_code(404);
                echo json_encode(['error' => 'Quiz not found']);
                return;
            }
            
            $this->db->query('DELETE FROM quizzes WHERE id = :id');
            $this->db->bind(':id', $id);
            
            if ($this->db->execute()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Quiz deleted successfully'
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to delete quiz']);
            }
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }
    
    public function getStats() {
        header('Content-Type: application/json');
        
        try {
            // Total quizzes
            $this->db->query('SELECT COUNT(*) as total FROM quizzes');
            $quizCount = $this->db->single()['total'];
            
            // Total questions
            $this->db->query('SELECT COUNT(*) as total FROM questions');
            $questionCount = $this->db->single()['total'];
            
            // Total uploaded files
            $this->db->query('SELECT COUNT(*) as total FROM uploaded_files');
            $fileCount = $this->db->single()['total'];
            
            // Recent quizzes
            $this->db->query('SELECT * FROM quizzes ORDER BY created_at DESC LIMIT 5');
            $recentQuizzes = $this->db->resultSet();
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'quiz_count' => $quizCount,
                    'question_count' => $questionCount,
                    'file_count' => $fileCount,
                    'recent_quizzes' => $recentQuizzes
                ]
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }
}
?>