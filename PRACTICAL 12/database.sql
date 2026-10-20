CREATE DATABASE IF NOT EXISTS practical12;
USE practical12;

CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    event_date DATE NOT NULL,
    category VARCHAR(50) NOT NULL,
    description TEXT,
    status ENUM('Open','Closed') DEFAULT 'Open',
    poster VARCHAR(255)
);

INSERT INTO events (title, event_date, category, description, status, poster) VALUES
('Coding Competition', '2026-11-10', 'Technical', 'Programming competition for students', 'Open', NULL),
('Annual Day', '2026-11-15', 'Cultural', 'Annual college celebration', 'Open', NULL),
('Web Development Workshop', '2026-11-20', 'Workshop', 'Learn basic web development', 'Closed', NULL);

CREATE TABLE IF NOT EXISTS event_audit (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    action_name VARCHAR(20) NOT NULL,
    details VARCHAR(255) NOT NULL,
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);