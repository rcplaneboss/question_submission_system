<?php
require_once '../config/Database.php';
require_once '../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$db = new Database();
$conn = $db->getConnection();

$name = "Admin";
$email = "admin@school.com";
$password = password_hash("yourpassword", PASSWORD_BCRYPT);
$role = "admin";

$stmt = $conn->prepare("INSERT INTO users (name, email, password, role, section) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$name, $email, $password, $role, "Western"]);

echo "Admin created!";


?>