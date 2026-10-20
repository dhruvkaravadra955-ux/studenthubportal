CREATE DATABASE IF NOT EXISTS practical14;
USE practical14;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','student') NOT NULL DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    course VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    event_date DATE NOT NULL,
    category VARCHAR(50) NOT NULL,
    status ENUM('Open','Closed') DEFAULT 'Open'
);

CREATE TABLE IF NOT EXISTS registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    student_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    event_name VARCHAR(100) NOT NULL,
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    username VARCHAR(100) NOT NULL,
    action VARCHAR(100) NOT NULL,
    details VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Demo account password for both users: password
INSERT IGNORE INTO users (name, email, password, role) VALUES
('Admin User', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Student User', 'student@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student');

INSERT IGNORE INTO students (name, email, course) VALUES
('Aarav Patel', 'aarav14@example.com', 'BCA'),
('Diya Shah', 'diya14@example.com', 'BSc IT'),
('Rohan Mehta', 'rohan14@example.com', 'BTech');

INSERT IGNORE INTO events (title, event_date, category, status) VALUES
('Coding Competition', '2026-11-10', 'Technical', 'Open'),
('Annual Day', '2026-11-15', 'Cultural', 'Open'),
('Web Workshop', '2026-11-20', 'Workshop', 'Closed');

INSERT INTO registrations (student_name, email, event_name) VALUES
('Aarav Patel', 'aarav14@example.com', 'Coding Competition'),
('Diya Shah', 'diya14@example.com', 'Annual Day'),
('Rohan Mehta', 'rohan14@example.com', 'Web Workshop');