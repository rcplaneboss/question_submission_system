<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

require_once '../config/Database.php';
$db = new Database();
$conn = $db->getConnection();

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "No submission ID provided.";
    exit;
}

$user = $_SESSION['user'];
$query = "SELECT * FROM submissions WHERE id = ?";
$params = [$id];

if ($user['role'] === 'teacher') {
    $query .= " AND teacher_id = ?";
    $params[] = $user['id'];
}

$stmt = $conn->prepare($query);
$stmt->execute($params);
$submission = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$submission) {
    echo "Submission not found or unauthorized.";
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>View Questions</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-8">
    <div class="bg-white p-6 rounded shadow max-w-3xl mx-auto">
        <h2 class="text-xl font-bold mb-4">Submitted Questions</h2>
        <div class="prose max-w-none">
            <?= $submission['questions'] ?>
        </div>
        <a href="<?= $user['role'] === 'admin' ? 'admin-dashboard.php' : 'dashboard.php' ?>"
            class="inline-block mt-6 text-blue-600 underline">← Back to Dashboard</a>
    </div>
</body>

</html>