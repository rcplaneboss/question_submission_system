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

$subjects = $conn->query("SELECT * FROM subjects ORDER BY section, name")->fetchAll(PDO::FETCH_ASSOC);
$classes = $conn->query("SELECT * FROM classes ORDER BY section, name")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Subject & Class Manager</title>
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
<body class="bg-gray-100 min-h-screen p-6 montserrat-sans">
  <h2 class="text-2xl font-bold mb-4">Manage Subjects & Classes</h2>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Subject Form -->
    <div class="bg-white p-4 rounded shadow">
      <h3 class="text-lg font-semibold mb-2">Add Subject</h3>
      <form action="../app/controllers/AdminController.php" method="POST" class="space-y-3">
        <input type="text" name="name" placeholder="Subject name" required class="w-full border px-3 py-2 rounded" />
        <select name="section" required class="w-full border px-3 py-2 rounded">
          <option value="Western">Western</option>
          <option value="Arabic">Arabic</option>
        </select>
        <input type="hidden" name="action" value="add_subject" />
        <button class="bg-blue-600 text-white px-4 py-2 rounded">Add Subject</button>
      </form>

      <h4 class="font-bold mt-6 mb-2">Subjects</h4>
      <ul class="list-disc pl-6 text-sm">
        <?php foreach ($subjects as $sub): ?>
          <li><?= htmlspecialchars($sub['name']) ?> (<?= $sub['section'] ?>)</li>
        <?php endforeach; ?>
      </ul>
    </div>

    <!-- Class Form -->
    <div class="bg-white p-4 rounded shadow">
      <h3 class="text-lg font-semibold mb-2">Add Class</h3>
      <form action="../app/controllers/AdminController.php" method="POST" class="space-y-3">
        <input type="text" name="name" placeholder="Class name (e.g. JSS 1)" required class="w-full border px-3 py-2 rounded" />
        <select name="section" required class="w-full border px-3 py-2 rounded">
          <option value="Western">Western</option>
          <option value="Arabic">Arabic</option>
        </select>
        <input type="hidden" name="action" value="add_class" />
        <button class="bg-blue-600 text-white px-4 py-2 rounded">Add Class</button>
      </form>

      <h4 class="font-bold mt-6 mb-2">Classes</h4>
      <ul class="list-disc pl-6 text-sm">
        <?php foreach ($classes as $cls): ?>
          <li><?= htmlspecialchars($cls['name']) ?> (<?= $cls['section'] ?>)</li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
</body>
</html>
