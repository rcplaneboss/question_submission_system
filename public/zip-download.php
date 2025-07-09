<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    echo "Unauthorized.";
    exit;
}

require_once '../config/Database.php';
$db = new Database();
$conn = $db->getConnection();

// Get filters
$section = $_GET['section'] ?? '';
$class = $_GET['class'] ?? '';
$subject = $_GET['subject'] ?? '';
$type = $_GET['type'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';
$teacher_id = $_GET['teacher_id'] ?? '';
$trigger = $_GET['download'] ?? null;

// Get select options
$classes = $conn->query("SELECT DISTINCT name, section FROM classes")->fetchAll(PDO::FETCH_ASSOC);
$subjects = $conn->query("SELECT DISTINCT name, section FROM subjects")->fetchAll(PDO::FETCH_ASSOC);
$teachers = $conn->query("SELECT id, name FROM users WHERE role = 'teacher'")->fetchAll(PDO::FETCH_ASSOC);

// Build WHERE
$where = [];
$params = [];

if ($section) {
    $where[] = 'users.section = ?';
    $params[] = $section;
}
if ($class) {
    $where[] = 'class = ?';
    $params[] = $class;
}
if ($subject) {
    $where[] = 'subject = ?';
    $params[] = $subject;
}
if ($type) {
    $where[] = 'type = ?';
    $params[] = $type;
}
if ($date_from) {
    $where[] = 'submissions.created_at >= ?';
    $params[] = $date_from . ' 00:00:00';
}
if ($date_to) {
    $where[] = 'submissions.created_at <= ?';
    $params[] = $date_to . ' 23:59:59';
}
if ($teacher_id) {
    $where[] = 'submissions.teacher_id = ?';
    $params[] = $teacher_id;
}

$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$query = "
  SELECT submissions.*
  FROM submissions
  JOIN users ON submissions.teacher_id = users.id
  $whereSQL
";

$stmt = $conn->prepare($query);
$stmt->execute($params);
$submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

$fileCount = 0;
$uploadDir = __DIR__ . '/../uploads/';

// Zip process
if ($trigger && count($submissions)) {
    $zipFile = __DIR__ . '/../tmp/filtered_' . time() . '.zip';
    if (!file_exists(dirname($zipFile))) {
        mkdir(dirname($zipFile), 0777, true);
    }

    $zip = new ZipArchive();
    if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
        foreach ($submissions as $s) {
            if ($s['file']) {
                $filePath = $uploadDir . $s['file'];
                if (file_exists($filePath)) {
                    $zip->addFile($filePath, basename($filePath));
                    $fileCount++;
                }
            }
        }
        $zip->close();

        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . basename($zipFile) . '"');
        header('Content-Length: ' . filesize($zipFile));
        readfile($zipFile);
        unlink($zipFile);
        exit;
    } else {
        die("Failed to create ZIP.");
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Filter & Download Submissions</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
  <h2 class="text-xl font-bold mb-4">Filter and Download Submissions</h2>

  <form method="GET" class="bg-white p-6 rounded shadow max-w-3xl space-y-4 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <select name="section" class="border px-3 py-2 rounded" required>
        <option value="">Select Section</option>
        <option value="Western" <?= $section === 'Western' ? 'selected' : '' ?>>Western</option>
        <option value="Arabic" <?= $section === 'Arabic' ? 'selected' : '' ?>>Arabic</option>
      </select>

      <select name="class" class="border px-3 py-2 rounded">
        <option value="">Select Class</option>
        <?php foreach ($classes as $c): ?>
          <?php if ($section === '' || $section === $c['section']): ?>
            <option value="<?= $c['name'] ?>" <?= $class === $c['name'] ? 'selected' : '' ?>><?= $c['name'] ?></option>
          <?php endif; ?>
        <?php endforeach; ?>
      </select>

      <select name="subject" class="border px-3 py-2 rounded">
        <option value="">Select Subject</option>
        <?php foreach ($subjects as $s): ?>
          <?php if ($section === '' || $section === $s['section']): ?>
            <option value="<?= $s['name'] ?>" <?= $subject === $s['name'] ? 'selected' : '' ?>><?= $s['name'] ?></option>
          <?php endif; ?>
        <?php endforeach; ?>
      </select>

      <select name="type" class="border px-3 py-2 rounded">
        <option value="">Select Type</option>
        <option value="Test" <?= $type === 'Test' ? 'selected' : '' ?>>Test</option>
        <option value="Exam" <?= $type === 'Exam' ? 'selected' : '' ?>>Exam</option>
      </select>

      <input type="date" name="date_from" value="<?= $date_from ?>" class="border px-3 py-2 rounded" />
      <input type="date" name="date_to" value="<?= $date_to ?>" class="border px-3 py-2 rounded" />

      <select name="teacher_id" class="border px-3 py-2 rounded">
        <option value="">All Teachers</option>
        <?php foreach ($teachers as $t): ?>
          <option value="<?= $t['id'] ?>" <?= $teacher_id == $t['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($t['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="flex justify-between items-center mt-4">
      <span class="text-sm text-gray-600">
        <?= count($submissions) ?> file<?= count($submissions) !== 1 ? 's' : '' ?> matched.
      </span>

      <button type="submit" name="download" value="1"
        class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Download ZIP</button>
    </div>
  </form>
</body>
</html>
