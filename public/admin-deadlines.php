<?php
session_start();
include '../partials/navbar.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require_once '../config/Database.php';
$db = new Database();
$conn = $db->getConnection();

$stmt = $conn->query("SELECT * FROM deadlines ORDER BY deadline DESC");
$deadlines = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Manage Deadlines</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<style>

.montserrat-sans {
  font-family: "Montserrat", sans-serif;
  font-optical-sizing: auto;
  /* font-weight: <weight>; */
  font-style: normal;
}
</style>
</head>
<body class="bg-gray-100 min-h-screen p-6 montserrat-sans">
  <h2 class="text-2xl font-bold mb-4">Set Submission Deadlines</h2>

  <form method="POST" action="../app/controllers/AdminController.php" class="mb-6 space-y-4 bg-white p-6 rounded shadow max-w-md">
    <select name="type" class="w-full border px-2 py-2 rounded" required>
      <option value="">Select Type</option>
      <option value="Test">Test</option>
      <option value="Exam">Exam</option>
    </select>

    <select name="section" class="w-full border px-2 py-2 rounded" required>
      <option value="">Select Section</option>
      <option value="Arabic">Arabic</option>
      <option value="Western">Western</option>
    </select>

    <input type="datetime-local" name="deadline" class="w-full border px-3 py-2 rounded" required />

    <input type="hidden" name="action" value="set_deadline" />
    <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Set Deadline</button>
  </form>

  <h3 class="text-xl font-semibold mb-2">Current Deadlines</h3>
  <table class="bg-white rounded shadow w-full text-sm">
    <thead class="bg-gray-200">
      <tr>
        <th class="px-3 py-2">Type</th>
        <th>Section</th>
        <th>Deadline</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($deadlines as $d): ?>
        <tr class="border-t text-center">
          <td class="px-3 py-2"><?= $d['type'] ?></td>
          <td><?= $d['section'] ?></td>
          <td><?= $d['deadline'] ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</body>
</html>
