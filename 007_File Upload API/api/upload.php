<?php
require_once '../includes/config.php';
require_once '../includes/validation.php';
require_once '../includes/helpers.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Check if files were uploaded
if (!isset($_FILES['files'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No files uploaded']);
    exit;
}

$files = $_FILES['files'];
$results = [];
$uploadCount = 0;

// Handle multiple files
for ($i = 0; $i < count($files['name']); $i++) {
    // Limit number of files per upload
    if ($i >= MAX_FILES_PER_UPLOAD) {
        break;
    }
    
    $file = [
        'name' => $files['name'][$i],
        'type' => $files['type'][$i],
        'tmp_name' => $files['tmp_name'][$i],
        'error' => $files['error'][$i],
        'size' => $files['size'][$i]
    ];
    
    $uploadResult = UploadHelper::handleUpload($file);
    
    if ($uploadResult['success']) {
        $uploadCount++;
    }
    
    $results[] = $uploadResult;
}

if ($uploadCount > 0) {
    echo json_encode([
        'success' => true,
        'message' => "Successfully uploaded $uploadCount file(s)",
        'results' => $results
    ]);
} else {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'No files were uploaded successfully',
        'results' => $results
    ]);
}
?>