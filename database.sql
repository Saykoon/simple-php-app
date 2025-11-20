CREATE DATABASE IF NOT EXISTS phpapp_db;
USE phpapp_db;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    age INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sample data
INSERT INTO users (name, email, age) VALUES 
('Jan Kowalski', 'jan.kowalski@example.com', 30),
('Anna Nowak', 'anna.nowak@example.com', 25),
('Piotr Wiśniewski', 'piotr.wisniewski@example.com', 35);