
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'teacher') DEFAULT 'teacher',
    section ENUM('Arabic', 'Western') DEFAULT 'Western',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    teacher_id INT NOT NULL,
    subject VARCHAR(100) NOT NULL,
    class VARCHAR(50) NOT NULL,
    type ENUM('Test', 'Exam') NOT NULL,
    file_path VARCHAR(255),
    questions TEXT,
    status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (teacher_id) REFERENCES users(id)
);



CREATE TABLE deadlines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('Test', 'Exam') NOT NULL,
    section ENUM('Arabic', 'Western') NOT NULL,
    deadline DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);



CREATE TABLE subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    section ENUM('Arabic', 'Western') NOT NULL
);

CREATE TABLE classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    section ENUM('Arabic', 'Western') NOT NULL
);
