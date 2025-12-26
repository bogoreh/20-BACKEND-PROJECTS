<?php
require_once 'config/database.php';

function generateShortCode($length = 6) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    
    return $randomString;
}

function isValidUrl($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

function createShortUrl($originalUrl) {
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("SELECT short_code FROM urls WHERE original_url = ?");
    $stmt->bind_param("s", $originalUrl);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $stmt->close();
        $conn->close();
        return $row['short_code'];
    }
    
    $shortCode = generateShortCode();
    
    $stmt = $conn->prepare("SELECT id FROM urls WHERE short_code = ?");
    $stmt->bind_param("s", $shortCode);
    
    while (true) {
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            break;
        }
        
        $shortCode = generateShortCode();
    }
    
    $stmt = $conn->prepare("INSERT INTO urls (original_url, short_code) VALUES (?, ?)");
    $stmt->bind_param("ss", $originalUrl, $shortCode);
    
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        return $shortCode;
    } else {
        $stmt->close();
        $conn->close();
        return false;
    }
}

function getOriginalUrl($shortCode) {
    $conn = getDBConnection();
    
    // Update click count and get URL
    $stmt = $conn->prepare("UPDATE urls SET click_count = click_count + 1 WHERE short_code = ?");
    $stmt->bind_param("s", $shortCode);
    $stmt->execute();
    
    $stmt = $conn->prepare("SELECT original_url FROM urls WHERE short_code = ?");
    $stmt->bind_param("s", $shortCode);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $stmt->close();
        $conn->close();
        return $row['original_url'];
    }
    
    $stmt->close();
    $conn->close();
    return false;
}

function getUrlStats($shortCode) {
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("SELECT original_url, created_at, click_count FROM urls WHERE short_code = ?");
    $stmt->bind_param("s", $shortCode);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $stmt->close();
        $conn->close();
        return $row;
    }
    
    $stmt->close();
    $conn->close();
    return false;
}
?>