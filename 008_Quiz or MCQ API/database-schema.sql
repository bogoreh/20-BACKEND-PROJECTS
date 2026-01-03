-- Database: quiz_api
CREATE DATABASE IF NOT EXISTS quiz_api CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE quiz_api;

-- Quizzes table
CREATE TABLE quizzes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    is_published BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_title (title),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Questions table
CREATE TABLE questions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    quiz_id INT NOT NULL,
    question_text TEXT NOT NULL,
    option_a VARCHAR(255) NOT NULL,
    option_b VARCHAR(255) NOT NULL,
    option_c VARCHAR(255),
    option_d VARCHAR(255),
    explanation TEXT,
    correct_answer ENUM('a', 'b', 'c', 'd') NOT NULL,
    image_url VARCHAR(500),
    points INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE,
    INDEX idx_quiz (quiz_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Uploaded files table
CREATE TABLE uploaded_files (
    id INT PRIMARY KEY AUTO_INCREMENT,
    filename VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size INT NOT NULL,
    file_type VARCHAR(100) NOT NULL,
    uploader_ip VARCHAR(45),
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_filename (filename),
    INDEX idx_uploaded (uploaded_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Quiz attempts table (optional for future use)
CREATE TABLE quiz_attempts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    quiz_id INT NOT NULL,
    user_id VARCHAR(100),
    score INT NOT NULL,
    total_questions INT NOT NULL,
    percentage DECIMAL(5,2) NOT NULL,
    started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE,
    INDEX idx_quiz (quiz_id),
    INDEX idx_completed (completed_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- API keys table (for authentication)
CREATE TABLE api_keys (
    id INT PRIMARY KEY AUTO_INCREMENT,
    api_key VARCHAR(64) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    permissions TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_used TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE,
    INDEX idx_key (api_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default API key (for testing)
INSERT INTO api_keys (api_key, name, permissions) VALUES 
('test_key_123', 'Test Client', 'read,write'),
('admin_key_456', 'Admin', 'read,write,delete');

-- Create a view for quiz statistics
CREATE VIEW quiz_stats AS
SELECT 
    q.id,
    q.title,
    q.created_at,
    COUNT(qu.id) as question_count,
    SUM(qu.points) as total_points
FROM quizzes q
LEFT JOIN questions qu ON q.id = qu.quiz_id
GROUP BY q.id, q.title, q.created_at;