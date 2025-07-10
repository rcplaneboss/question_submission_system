# Question Submission System for Schools 🏫

A lightweight PHP web application that allows school teachers to submit their Test and Exam questions for approval. Designed for schools with Arabic and Western sections.

## ✨ Features

- ✅ Teacher login & registration
- ✅ Admin panel for managing deadlines and subjects
- ✅ Submit questions via file upload or rich text editor (TinyMCE)
- ✅ View submissions with filters (class, subject, type, date)
- ✅ Download filtered submissions as ZIP
- ✅ Admin approval system (Approve/Reject)
- ✅ Prevent duplicate submissions
- ✅ Teachers can view, edit, or delete their submissions

## 🛠️ Technologies Used

- PHP (Vanilla PHP)
- MySQL (Database)
- TailwindCSS (Styling)
- TinyMCE (Rich Text Editor)
- Dotenv (for loading .env configs)
- PhpSpreadsheet & PhpWord (for document previewing)
- Apache (XAMPP/Termux compatible)

## 📂 Folder Structure

```
├── app/
│   └── controllers/          # Auth and submission logic
├── config/
│   └── Database.php          # PDO DB connection
├── public/
│   ├── login.php             # Login page
│   ├── register.php          # Registration page
│   ├── dashboard.php         # Teacher dashboard
│   ├── admin-dashboard.php   # Admin dashboard
│   ├── zip-download.php      # ZIP filter/download page
│   ├── view-file.php         # Preview uploaded files
│   └── edit.php              # Edit a submission
├── uploads/                  # Uploaded question files
├── vendor/                   # Composer dependencies
├── .env                      # Environment variables
└── index.php                 # Optional redirect or homepage
```

## 🧪 How to Run

```bash
git clone https://github.com/rcplaneboss/question-submission-system.git
cd question-submission-system
composer install
```

1. Setup your `.env` file (see `.env.example`):

```
DB_HOST=localhost
DB_NAME=your_db_name
DB_USER=root
DB_PASS=
```

2. Import the SQL schema (`schema.sql`) to your MySQL database.

3. Start the server (via XAMPP or built-in PHP server):

```bash
php -S localhost:8000 -t public
```

4. Visit `http://localhost:8000` and register as a teacher/admin.

## 📌 Notes

- Admins are added manually from the database or via the admin registration form.
- Text-only questions are stored and previewed directly.
- Word and PDF files are previewed using PhpWord and embedded iframe.

## 🗃️ Database Schema

```sql
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
```

## 🧾 License

This project is open-source and free to use under the MIT License.