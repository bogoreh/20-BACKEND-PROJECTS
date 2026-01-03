<?php
// api/questions.php
require_once '../controllers/QuestionController.php';

$questionController = new QuestionController();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['quiz_id'])) {
            $questionController->getQuizQuestions($_GET['quiz_id']);
        } elseif (isset($_GET['id'])) {
            $questionController->getQuestion($_GET['id']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'quiz_id or id parameter is required']);
        }
        break;
        
    case 'POST':
        if (isset($_GET['submit'])) {
            $questionController->submitAnswers();
        } else {
            $questionController->createQuestion();
        }
        break;
        
    case 'PUT':
        parse_str(file_get_contents("php://input"), $data);
        if (isset($data['id'])) {
            $questionController->updateQuestion($data['id'], $data);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Question ID is required']);
        }
        break;
        
    case 'DELETE':
        parse_str(file_get_contents("php://input"), $data);
        if (isset($data['id'])) {
            $questionController->deleteQuestion($data['id']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Question ID is required']);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
?>