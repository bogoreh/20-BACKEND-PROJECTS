<?php
require_once 'config/database.php';

// Sanitize input data
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Validate email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Save contact to database
function saveContact($name, $email, $subject, $message) {
    $conn = getConnection();
    
    // Prepare SQL statement
    $stmt = $conn->prepare("INSERT INTO contacts (name, email, subject, message) VALUES (?, ?, ?, ?)");
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    // Bind parameters
    $stmt->bind_param("ssss", $name, $email, $subject, $message);
    
    // Execute query
    $result = $stmt->execute();
    
    // Close statement and connection
    $stmt->close();
    $conn->close();
    
    return $result;
}

// Get all contacts (for admin view if needed)
function getAllContacts() {
    $conn = getConnection();
    $result = $conn->query("SELECT * FROM contacts ORDER BY created_at DESC");
    $contacts = [];
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $contacts[] = $row;
        }
    }
    
    $conn->close();
    return $contacts;
}
?>