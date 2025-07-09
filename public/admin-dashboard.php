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

// Filtering (optional)
$where = [];
$params = [];

if (!empty($_GET['section'])) {
  $where[] = 'u.section = ?';
  $params[] = $_GET['section'];
}
if (!empty($_GET['class'])) {
  $where[] = 's.class = ?';
  $params[] = $_GET['class'];
}
if (!empty($_GET['subject'])) {
  $where[] = 's.subject = ?';
  $params[] = $_GET['subject'];
}

$whereSQL = count($where) ? 'WHERE ' . implode(' AND ', $where) : '';

$sql = "SELECT s.*, u.name as teacher_name, u.section 
        FROM submissions s
        JOIN users u ON s.teacher_id = u.id
        $whereSQL
        ORDER BY s.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html>

<head>
  <title>Admin Dashboard</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
    rel="stylesheet">
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

  <h2 class="text-2xl font-bold mb-4 py-5">Admin Dashboard</h2>

  <form method="GET" class="flex gap-3 mb-6 max-md:flex-col">
    <select name="section" class="border px-2 py-1 rounded">
      <option value="">All Sections</option>
      <option value="Arabic">Arabic</option>
      <option value="Western">Western</option>
    </select>
    <input type="text" name="class" placeholder="Class" class="border px-2 py-1 rounded" />
    <input type="text" name="subject" placeholder="Subject" class="border px-2 py-1 rounded" />
    <button type="submit" class="bg-blue-600 text-white px-4 py-1 rounded">Filter</button>
  </form>

  <div class="max-md:overflow-x-scroll">
    <table class="w-full bg-white rounded shadow ">
      <thead class="bg-gray-200">
        <tr>
          <th class="px-3 py-2">Teacher</th>
          <th class="px-3 py-2">Section</th>
          <th class="px-3 py-2">Class</th>
          <th class="px-3 py-2">Subject</th>
          <th class="px-3 py-2">Type</th>
          <th class="px-3 py-2">File</th>
          <th class="px-3 py-2">Status</th>
          <th class="px-3 py-2">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($submissions as $sub): ?>
          <tr class="border-t text-sm text-center">
            <td class="px-3 py-2"><?= htmlspecialchars($sub['teacher_name']) ?></td>
            <td><?= $sub['section'] ?></td>
            <td><?= $sub['class'] ?></td>
            <td><?= $sub['subject'] ?></td>
            <td><?= $sub['type'] ?></td>
            <td class="space-x-2">
              <?php if ($sub['file_path']): ?>
                <a href="view-file.php?id=<?= $sub['id'] ?>" class="text-blue-600 underline">View File</a>
              <?php endif; ?>


              <?php if ($sub['questions']): ?>
                <a href="view-questions.php?id=<?= $sub['id'] ?>" class="text-purple-600 underline">Text</a>
              <?php endif; ?>
            </td>
            <td class="font-bold <?=
              $sub['status'] === 'Approved' ? 'text-green-600' :
              ($sub['status'] === 'Rejected' ? 'text-red-600' : 'text-yellow-600') ?>">
              <?= $sub['status'] ?>
            </td>
            <td class="flex text-white gap-3 justify-center items-center">
              <form action="../app/controllers/AdminController.php" method="POST" class="flex gap-2">
                <input type="hidden" name="id" value="<?= $sub['id'] ?>" />
                <input type="hidden" name="action" value="approve" />
                <button class="text-white font-medium rounded-md bg-green-500 px-3 py-2" type="submit">Approve</button>
              </form>
              <form action="../app/controllers/AdminController.php" method="POST" class="mt-1">
                <input type="hidden" name="id" value="<?= $sub['id'] ?>" />
                <input type="hidden" name="action" value="reject" />
                <button class="text-white font-medium  rounded-md bg-red-500 px-3 py-2" type="submit">Reject</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</body>

</html>