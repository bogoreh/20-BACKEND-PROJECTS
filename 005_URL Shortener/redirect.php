<?php
require_once 'includes/functions.php';

$requestUri = $_SERVER['REQUEST_URI'];
$shortCode = ltrim($requestUri, '/');

$shortCode = strtok($shortCode, '?');

if (!empty($shortCode) && !preg_match('/\.(php|css|js|jpg|png|gif)$/i', $shortCode)) {
    $originalUrl = getOriginalUrl($shortCode);
    
    if ($originalUrl) {
        header("Location: " . $originalUrl);
        exit();
    }
}

header("Location: " . BASE_URL);
exit();
?>