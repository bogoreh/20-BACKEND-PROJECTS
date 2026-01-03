<?php
// api/upload.php
require_once '../controllers/FileController.php';

$fileController = new FileController();

// Get the request method
$method = $_SERVER['REQUEST_METHOD'];

// Handle preflight requests
if ($method === 'OPTIONS') {
    header('HTTP/1.1 200 OK');
    exit();
}

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $fileController->getFile($_GET['id']);
        } elseif (isset($_GET['search'])) {
            $fileController->searchFiles();
        } elseif (isset($_GET['stats'])) {
            $fileController->getStorageStats();
        } elseif (isset($_GET['cleanup'])) {
            $fileController->cleanupOrphanedFiles();
        } else {
            $fileController->getAllFiles();
        }
        break;
        
    case 'POST':
        $fileController->uploadImage();
        break;
        
    case 'DELETE':
        // Parse URL to get ID
        $requestUri = $_SERVER['REQUEST_URI'];
        $pathParts = explode('/', $requestUri);
        $id = end($pathParts);
        
        if (is_numeric($id)) {
            $fileController->deleteImage($id);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid file ID']);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
?>