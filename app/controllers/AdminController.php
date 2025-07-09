<?php
session_start();
require_once '../../vendor/autoload.php';
require_once '../../config/Database.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    echo "Unauthorized";
    exit;
}

$db = new Database();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    switch ($action) {
        case 'approve':
        case 'reject':
            $id = $_POST['id'];
            $status = $action === 'approve' ? 'Approved' : 'Rejected';

            $stmt = $conn->prepare("UPDATE submissions SET status = ? WHERE id = ?");
            $stmt->execute([$status, $id]);

            header("Location: ../../public/admin-dashboard.php");
            exit;

        case 'set_deadline':
            $type = $_POST['type'];
            $section = $_POST['section'];
            $deadline = $_POST['deadline'];

            $stmt = $conn->prepare("INSERT INTO deadlines (type, section, deadline) VALUES (?, ?, ?)");
            $stmt->execute([$type, $section, $deadline]);

            header("Location: ../../public/admin-deadlines.php");
            exit;

            case 'add_subject':
                $name = $_POST['name'];
                $section = $_POST['section'];
            
                $stmt = $conn->prepare("INSERT INTO subjects (name, section) VALUES (?, ?)");
                $stmt->execute([$name, $section]);
            
                header("Location: ../../public/admin-subjects.php");
                exit;
            
            case 'add_class':
                $name = $_POST['name'];
                $section = $_POST['section'];
            
                $stmt = $conn->prepare("INSERT INTO classes (name, section) VALUES (?, ?)");
                $stmt->execute([$name, $section]);
            
                header("Location: ../../public/admin-subjects.php");
                exit;
            

        default:
            exit("Invalid action");
    }
}
