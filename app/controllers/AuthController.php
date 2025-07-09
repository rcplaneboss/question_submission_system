<?php
session_start();
require_once '../../vendor/autoload.php';
require_once '../../config/Database.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

$db = new Database();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'register') {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $section = $_POST['section'];

        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, section) VALUES (?, ?, ?, 'teacher', ?)");
        try {
            $stmt->execute([$name, $email, $password, $section]);
            $_SESSION['success'] = "Registration successful!";
            header("Location: ../../public/login.php");
        } catch (PDOException $e) {
            $_SESSION['error'] = "Email already exists.";
            header("Location: ../../public/register.php");
        }
        exit;
    }

    if ($_POST['action'] === 'login') {
        $email = $_POST['email'];
        $password = $_POST['password'];
    
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'role' => $user['role'],
                'section' => $user['section']
            ];
    
            header("Location: ../../public/" . ($user['role'] === 'admin' ? "admin-dashboard.php" : "dashboard.php"));
            exit;
        } else {
            $_SESSION['message'] = "Invalid email or password.";
            header("Location: ../../public/login.php");
            exit;
        }
    }
    


    if ($_POST['action'] === 'set_deadline') {
        $type = $_POST['type'];
        $section = $_POST['section'];
        $deadline = $_POST['deadline'];
    
        $stmt = $conn->prepare("INSERT INTO deadlines (type, section, deadline) VALUES (?, ?, ?)");
        $stmt->execute([$type, $section, $deadline]);
    
        header("Location: ../../public/admin-deadlines.php");
        exit;
    }
    
}
