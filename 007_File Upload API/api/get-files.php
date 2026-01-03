<?php
require_once '../includes/config.php';
require_once '../includes/helpers.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$files = UploadHelper::getUploadedFiles();

echo json_encode([
    'success' => true,
    'files' => $files,
    'count' => count($files)
]);
?>