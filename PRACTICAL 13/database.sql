CREATE DATABASE IF NOT EXISTS practical13;
USE practical13;

CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    course VARCHAR(50) NOT NULL
);

INSERT IGNORE INTO students (name, email, course) VALUES
('Aarav Patel', 'aarav13@example.com', 'BCA'),
('Diya Shah', 'diya13@example.com', 'BSc IT'),
('Rohan Mehta', 'rohan13@example.com', 'BTech'),
('Priya Desai', 'priya13@example.com', 'BCA'),
('Karan Joshi', 'karan13@example.com', 'BSc IT');