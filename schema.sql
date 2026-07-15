CREATE DATABASE IF NOT EXISTS todo_db;
USE todo_db;

CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    status ENUM('pending', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO tasks (title, description, status) VALUES
('Finish PHP Homework', 'Review OOP concepts and connect to database', 'pending'),
('Upload Code to GitHub', 'Push the restful-ecommerce-engine project online', 'completed');
