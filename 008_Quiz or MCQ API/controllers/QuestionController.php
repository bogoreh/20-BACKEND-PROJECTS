<?php
// controllers/QuestionController.php
require_once '../includes/Database.php';

class QuestionController {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function getQuestion($id) {
        header('Content-Type: application/json');
        
        try {
            $this->db->query('SELECT * FROM questions WHERE id = :id');
            $this->db->bind(':id', $id);
            $question = $this->db->single();
            
            if ($question) {
                echo json_encode(['success' => true, 'data' => $question]);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Question not found']);
            }
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }
    
    public function getQuizQuestions($quizId) {
        header('Content-Type: application/json');
        
        try {
            // Check if quiz exists
            $this->db->query('SELECT id FROM quizzes WHERE id = :quiz_id');
            $this->db->bind(':quiz_id', $quizId);
            $quizExists = $this->db->single();
            
            if (!$quizExists) {
                http_response_code(404);
                echo json_encode(['error' => 'Quiz not found']);
                return;
            }
            
            $this->db->query('SELECT * FROM questions WHERE quiz_id = :quiz_id ORDER BY id');
            $this->db->bind(':quiz_id', $quizId);
            $questions = $this->db->resultSet();
            
            // For security, don't include correct answer unless specified
            if (!isset($_GET['with_answers']) || $_GET['with_answers'] !== 'true') {
                foreach ($questions as &$question) {
                    unset($question['correct_answer']);
                }
            }
            
            echo json_encode([
                'success' => true,
                'data' => $questions,
                'count' => count($questions)
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }
    
    public function createQuestion() {
        header('Content-Type: application/json');
        
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Required fields
            $required = ['quiz_id', 'question_text', 'option_a', 'option_b', 'correct_answer'];
            foreach ($required as $field) {
                if (!isset($data[$field]) || empty(trim($data[$field]))) {
                    http_response_code(400);
                    echo json_encode(['error' => $field . ' is required']);
                    return;
                }
            }
            
            // Validate correct answer
            $validAnswers = ['a', 'b', 'c', 'd'];
            if (!in_array(strtolower($data['correct_answer']), $validAnswers)) {
                http_response_code(400);
                echo json_encode(['error' => 'correct_answer must be one of: a, b, c, d']);
                return;
            }
            
            // Check if quiz exists
            $this->db->query('SELECT id FROM quizzes WHERE id = :quiz_id');
            $this->db->bind(':quiz_id', $data['quiz_id']);
            $quizExists = $this->db->single();
            
            if (!$quizExists) {
                http_response_code(404);
                echo json_encode(['error' => 'Quiz not found']);
                return;
            }
            
            $this->db->query(
                'INSERT INTO questions 
                (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_answer, image_url) 
                VALUES (:quiz_id, :question_text, :option_a, :option_b, :option_c, :option_d, :correct_answer, :image_url)'
            );
            
            $this->db->bind(':quiz_id', $data['quiz_id']);
            $this->db->bind(':question_text', trim($data['question_text']));
            $this->db->bind(':option_a', trim($data['option_a']));
            $this->db->bind(':option_b', trim($data['option_b']));
            $this->db->bind(':option_c', isset($data['option_c']) ? trim($data['option_c']) : null);
            $this->db->bind(':option_d', isset($data['option_d']) ? trim($data['option_d']) : null);
            $this->db->bind(':correct_answer', strtolower($data['correct_answer']));
            $this->db->bind(':image_url', isset($data['image_url']) ? trim($data['image_url']) : null);
            
            if ($this->db->execute()) {
                $questionId = $this->db->lastInsertId();
                
                $this->db->query('SELECT * FROM questions WHERE id = :id');
                $this->db->bind(':id', $questionId);
                $question = $this->db->single();
                
                http_response_code(201);
                echo json_encode([
                    'success' => true,
                    'message' => 'Question added successfully',
                    'data' => $question
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to add question']);
            }
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }
    
    public function updateQuestion($id, $data) {
        header('Content-Type: application/json');
        
        try {
            $this->db->query('SELECT id FROM questions WHERE id = :id');
            $this->db->bind(':id', $id);
            $exists = $this->db->single();
            
            if (!$exists) {
                http_response_code(404);
                echo json_encode(['error' => 'Question not found']);
                return;
            }
            
            $fields = [];
            $params = [':id' => $id];
            
            $allowedFields = ['question_text', 'option_a', 'option_b', 'option_c', 'option_d', 'correct_answer', 'image_url'];
            
            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $fields[] = $field . ' = :' . $field;
                    $params[':' . $field] = trim($data[$field]);
                }
            }
            
            if (empty($fields)) {
                http_response_code(400);
                echo json_encode(['error' => 'No fields to update']);
                return;
            }
            
            $sql = 'UPDATE questions SET ' . implode(', ', $fields) . ' WHERE id = :id';
            $this->db->query($sql);
            
            foreach ($params as $key => $value) {
                $this->db->bind($key, $value);
            }
            
            if ($this->db->execute()) {
                $this->db->query('SELECT * FROM questions WHERE id = :id');
                $this->db->bind(':id', $id);
                $question = $this->db->single();
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Question updated successfully',
                    'data' => $question
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to update question']);
            }
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }
    
    public function deleteQuestion($id) {
        header('Content-Type: application/json');
        
        try {
            $this->db->query('SELECT id FROM questions WHERE id = :id');
            $this->db->bind(':id', $id);
            $exists = $this->db->single();
            
            if (!$exists) {
                http_response_code(404);
                echo json_encode(['error' => 'Question not found']);
                return;
            }
            
            $this->db->query('DELETE FROM questions WHERE id = :id');
            $this->db->bind(':id', $id);
            
            if ($this->db->execute()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Question deleted successfully'
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to delete question']);
            }
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }
    
    public function submitAnswers() {
        header('Content-Type: application/json');
        
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['quiz_id']) || !isset($data['answers'])) {
                http_response_code(400);
                echo json_encode(['error' => 'quiz_id and answers are required']);
                return;
            }
            
            $quizId = $data['quiz_id'];
            $userAnswers = $data['answers'];
            
            // Check if quiz exists
            $this->db->query('SELECT id FROM quizzes WHERE id = :quiz_id');
            $this->db->bind(':quiz_id', $quizId);
            $quizExists = $this->db->single();
            
            if (!$quizExists) {
                http_response_code(404);
                echo json_encode(['error' => 'Quiz not found']);
                return;
            }
            
            // Get correct answers
            $this->db->query('SELECT id, correct_answer FROM questions WHERE quiz_id = :quiz_id');
            $this->db->bind(':quiz_id', $quizId);
            $questions = $this->db->resultSet();
            
            $score = 0;
            $total = count($questions);
            $results = [];
            
            foreach ($questions as $question) {
                $questionId = $question['id'];
                $userAnswer = isset($userAnswers[$questionId]) ? strtolower($userAnswers[$questionId]) : null;
                $correctAnswer = strtolower($question['correct_answer']);
                $isCorrect = ($userAnswer === $correctAnswer);
                
                if ($isCorrect) {
                    $score++;
                }
                
                $results[] = [
                    'question_id' => $questionId,
                    'user_answer' => $userAnswer,
                    'correct_answer' => $correctAnswer,
                    'is_correct' => $isCorrect
                ];
            }
            
            $percentage = $total > 0 ? round(($score / $total) * 100, 2) : 0;
            
            // Determine grade
            if ($percentage >= 90) $grade = 'A';
            elseif ($percentage >= 80) $grade = 'B';
            elseif ($percentage >= 70) $grade = 'C';
            elseif ($percentage >= 60) $grade = 'D';
            else $grade = 'F';
            
            echo json_encode([
                'success' => true,
                'score' => $score,
                'total' => $total,
                'percentage' => $percentage,
                'grade' => $grade,
                'results' => $results,
                'message' => $score . ' out of ' . $total . ' correct (' . $percentage . '%)'
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }
}
?>