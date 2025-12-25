CREATE DATABASE IF NOT EXISTS contact_form_db;
USE contact_form_db;

CREATE TABLE IF NOT EXISTS contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Optional: Insert test data
INSERT INTO contacts (name, email, subject, message) 
VALUES ('John Doe', 'john@example.com', 'Test Subject', 'This is a test message');