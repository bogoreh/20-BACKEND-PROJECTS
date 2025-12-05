<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'auth_app');

// Choose authentication method: 'session' or 'jwt'
define('AUTH_METHOD', 'session');

// JWT Secret Key (only needed if using JWT)
define('JWT_SECRET', 'your-secret-key-change-this-in-production');
define('JWT_ALGO', 'HS256');

// Create database connection
function getDBConnection() {
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    
    return $conn;
}

// Create users table if it doesn't exist
function createUsersTable() {
    $conn = getDBConnection();
    
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (!mysqli_query($conn, $sql)) {
        die("Error creating table: " . mysqli_error($conn));
    }
    
    mysqli_close($conn);
}

// Call this function to ensure table exists
createUsersTable();
?>