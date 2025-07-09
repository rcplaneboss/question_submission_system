<?php
session_start();
require_once '../../vendor/autoload.php';
require_once '../../config/Database.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

$db = new Database();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? null;

    // Common variables
    $userId = $_SESSION['user']['id'];
    $subject = $_POST['subject'] ?? '';
    $class = $_POST['class'] ?? '';
    $type = $_POST['type'] ?? '';
    $questions = $_POST['questions'] ?? '';

    // ===================
    // Handle submission
    // ===================
    if ($action === 'submit') {
        // ✅ Check deadline
        $stmt = $conn->prepare("SELECT deadline FROM deadlines WHERE type = ? AND section = ? ORDER BY deadline DESC LIMIT 1");
        $stmt->execute([$type, $_SESSION['user']['section']]);
        $deadline = $stmt->fetchColumn();

        if ($deadline && strtotime($deadline) < time()) {
            $_SESSION['error'] = "Submission deadline has passed for this type.";
            header("Location: ../../public/dashboard.php");
            exit;
        }

        // ✅ Prevent duplicate
        $check = $conn->prepare("SELECT COUNT(*) FROM submissions WHERE teacher_id = ? AND subject = ? AND class = ? AND type = ?");
        $check->execute([$userId, $subject, $class, $type]);
        if ($check->fetchColumn() > 0) {
            $_SESSION['error'] = "You’ve already submitted for this subject, class, and type.";
            header("Location: ../../public/dashboard.php");
            exit;
        }

        // ✅ Handle file upload
        $filePath = null;
        if (!empty($_FILES['file']['name'])) {
            $targetDir = '../../uploads/';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            $fileName = time() . '_' . basename($_FILES['file']['name']);
            $targetFilePath = $targetDir . $fileName;
            move_uploaded_file($_FILES['file']['tmp_name'], $targetFilePath);
            $filePath = $fileName;
        }

        // ✅ Insert submission
        $stmt = $conn->prepare("INSERT INTO submissions (teacher_id, subject, class, type, file_path, questions) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $subject, $class, $type, $filePath, $questions]);

        $_SESSION['success'] = "Submission successful!";
        header("Location: ../../public/dashboard.php");
        exit;
    }

    // ===================
    // Handle update
    // ===================
    if ($action === 'update') {
        $id = $_POST['id'];

        // ✅ Check ownership
        $stmt = $conn->prepare("SELECT * FROM submissions WHERE id = ? AND teacher_id = ?");
        $stmt->execute([$id, $userId]);
        $submission = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$submission) {
            echo "Unauthorized update.";
            exit;
        }

        $filePath = $submission['file_path'];

        if (!empty($_FILES['file']['name'])) {
            $targetDir = '../../uploads/';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            $fileName = time() . '_' . basename($_FILES['file']['name']);
            $targetFilePath = $targetDir . $fileName;
            move_uploaded_file($_FILES['file']['tmp_name'], $targetFilePath);
            $filePath = $fileName;
        }

        $stmt = $conn->prepare("UPDATE submissions SET subject = ?, class = ?, type = ?, file_path = ?, questions = ?, status = 'Pending' WHERE id = ?");
        $stmt->execute([$subject, $class, $type, $filePath, $questions, $id]);

        $_SESSION['success'] = "Submission updated!";
        header("Location: ../../public/dashboard.php");
        exit;
    }


    // ===================
    // Handle delete
    // ===================
    if ($action === 'delete') {
        $id = $_POST['id'];

        // Verify teacher owns the submission
        $stmt = $conn->prepare("SELECT * FROM submissions WHERE id = ? AND teacher_id = ?");
        $stmt->execute([$id, $userId]);
        $submission = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$submission) {
            $_SESSION['error'] = "Unauthorized deletion.";
            header("Location: ../../public/dashboard.php");
            exit;
        }

        // Delete the file if it exists
        if (!empty($submission['file_path'])) {
            $file = '../../uploads/' . $submission['file_path'];
            if (file_exists($file)) {
                unlink($file);
            }
        }

        // Delete submission record
        $stmt = $conn->prepare("DELETE FROM submissions WHERE id = ?");
        $stmt->execute([$id]);

        $_SESSION['success'] = "Submission deleted.";
        header("Location: ../../public/dashboard.php");
        exit;
    }

}
