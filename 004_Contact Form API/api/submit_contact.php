<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../includes/functions.php';

$response = [
    'success' => false,
    'message' => '',
    'errors' => []
];

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Invalid request method';
    echo json_encode($response);
    exit;
}

// Get and sanitize input
$name = isset($_POST['name']) ? sanitizeInput($_POST['name']) : '';
$email = isset($_POST['email']) ? sanitizeInput($_POST['email']) : '';
$subject = isset($_POST['subject']) ? sanitizeInput($_POST['subject']) : '';
$message = isset($_POST['message']) ? sanitizeInput($_POST['message']) : '';

// Validate inputs
$errors = [];

if (empty($name)) {
    $errors['name'] = 'Name is required';
} elseif (strlen($name) < 2) {
    $errors['name'] = 'Name must be at least 2 characters';
}

if (empty($email)) {
    $errors['email'] = 'Email is required';
} elseif (!validateEmail($email)) {
    $errors['email'] = 'Invalid email format';
}

if (empty($subject)) {
    $errors['subject'] = 'Subject is required';
} elseif (strlen($subject) < 3) {
    $errors['subject'] = 'Subject must be at least 3 characters';
}

if (empty($message)) {
    $errors['message'] = 'Message is required';
} elseif (strlen($message) < 10) {
    $errors['message'] = 'Message must be at least 10 characters';
}

// If there are errors, return them
if (!empty($errors)) {
    $response['errors'] = $errors;
    $response['message'] = 'Please fix the errors below';
    echo json_encode($response);
    exit;
}

// Try to save the contact
try {
    $result = saveContact($name, $email, $subject, $message);
    
    if ($result) {
        $response['success'] = true;
        $response['message'] = 'Thank you! Your message has been sent successfully.';
        
        // Optional: Send email notification
        // sendEmailNotification($name, $email, $subject, $message);
        
    } else {
        $response['message'] = 'Sorry, there was an error sending your message. Please try again.';
    }
} catch (Exception $e) {
    $response['message'] = 'Database error: ' . $e->getMessage();
}

echo json_encode($response);
?>