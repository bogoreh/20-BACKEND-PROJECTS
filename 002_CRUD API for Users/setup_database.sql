-- Create database
CREATE DATABASE IF NOT EXISTS user_crud_db;
USE user_crud_db;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample data
INSERT INTO users (name, email, phone) VALUES
('John Doe', 'john.doe@example.com', '123-456-7890'),
('Jane Smith', 'jane.smith@example.com', '987-654-3210'),
('Robert Johnson', 'robert.j@example.com', '555-123-4567'),
('Emily Davis', 'emily.davis@example.com', '444-555-6666'),
('Michael Brown', 'michael.b@example.com', '777-888-9999');