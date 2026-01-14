CREATE DATABASE IF NOT EXISTS products_api;
USE products_api;

CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    category VARCHAR(100),
    price DECIMAL(10,2),
    stock INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample data
INSERT INTO products (name, description, category, price, stock) VALUES
('iPhone 13', 'Latest Apple smartphone', 'Electronics', 999.99, 50),
('Samsung Galaxy S22', 'Android flagship phone', 'Electronics', 899.99, 75),
('MacBook Pro', 'Apple laptop for professionals', 'Computers', 1999.99, 30),
('Dell XPS 13', 'Windows ultrabook', 'Computers', 1199.99, 45),
('Sony Headphones', 'Noise cancelling headphones', 'Audio', 299.99, 100),
('Nike Air Max', 'Running shoes', 'Footwear', 129.99, 200),
('Levi\'s Jeans', 'Blue denim jeans', 'Clothing', 79.99, 150),
('Kindle Paperwhite', 'E-book reader', 'Electronics', 129.99, 80),
('Instant Pot', 'Multi-cooker', 'Kitchen', 99.99, 120),
('Yoga Mat', 'Non-slip exercise mat', 'Fitness', 29.99, 300);