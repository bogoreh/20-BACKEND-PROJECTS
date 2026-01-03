<?php
// api/quizzes.php
require_once '../controllers/QuizController.php';

$quizController = new QuizController();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $quizController->getQuiz($_GET['id']);
        } else {
            $quizController->getAllQuizzes();
        }
        break;
        
    case 'POST':
        $quizController->createQuiz();
        break;
        
    case 'PUT':
        // Parse JSON input for PUT requests
        parse_str(file_get_contents("php://input"), $data);
        if (isset($data['id'])) {
            $quizController->updateQuiz($data['id'], $data);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Quiz ID is required']);
        }
        break;
        
    case 'DELETE':
        parse_str(file_get_contents("php://input"), $data);
        if (isset($data['id'])) {
            $quizController->deleteQuiz($data['id']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Quiz ID is required']);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
?>