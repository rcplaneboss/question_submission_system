<?php
session_start();
include '../partials/navbar.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') {
  header("Location: login.php");
  exit;
}

$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html>

<head>
  <title>Teacher Dashboard</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
    rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      plugins: [tailwindcss.typography],
    }
  </script>
</head>

<style>
  .montserrat-sans {
    font-family: "Montserrat", sans-serif;
  }
</style>

<body class="bg-gray-100 min-h-screen montserrat-sans">

  <div class="text-xl px-5 py-5">Welcome, <?= htmlspecialchars($user['name']) ?></div>

  <?php if (!empty($_SESSION['error'])): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 max-w-3xl mx-auto">
      <?= $_SESSION['error'];
      unset($_SESSION['error']); ?>
    </div>
  <?php endif; ?>

  <div class="p-6">
    <h2 class="text-xl font-bold mb-4">My Submissions</h2>

    <?php
    require_once '../config/Database.php';
    $db = new Database();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("SELECT * FROM submissions WHERE teacher_id = ?");
    $stmt->execute([$user['id']]);
    $submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <div class="max-md:overflow-x-scroll">
      <table class="min-w-[800px] bg-white shadow-md rounded border">
        <thead class="bg-gray-200">
          <tr>
            <th class="py-2 px-3">Subject</th>
            <th>Class</th>
            <th>Type</th>
            <th>Status</th>
            <th>File</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($submissions as $sub): ?>
            <tr class="border-t text-center">
              <td class="py-2 px-3"><?= htmlspecialchars($sub['subject']) ?></td>
              <td><?= htmlspecialchars($sub['class']) ?></td>
              <td><?= htmlspecialchars($sub['type']) ?></td>
              <td
                class="font-semibold <?= $sub['status'] === 'Approved' ? 'text-green-600' : ($sub['status'] === 'Rejected' ? 'text-red-600' : 'text-yellow-600') ?>">
                <?= $sub['status'] ?>
              </td>
              <td>
                <?php if ($sub['file_path']): ?>
                  <a href="view-file.php?id=<?= $sub['id'] ?>" class="text-blue-600 underline">View</a>
                <?php endif; ?>
              </td>
              <td class="space-y-1 flex gap-3 mx-3 justify-center items-center">
                <a href="edit.php?id=<?= $sub['id'] ?>" class="text-blue-500 underline">Edit</a>
                <form action="../app/controllers/SubmissionController.php" method="POST" class="inline"
                  onsubmit="return confirm('Are you sure you want to delete this submission?');">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?= $sub['id'] ?>">
                  <button type="submit" class="text-red-600 underline">Delete</button>
                </form>

                <?php if (!empty($sub['questions'])): ?>
                  <a href="view-questions.php?id=<?= $sub['id'] ?>" class="text-purple-600 underline ml-2">View</a>
                <?php endif; ?>
              </td>
            </tr>

            <!-- Modal for each submission -->
            <?php if (!empty($sub['questions'])): ?>
              <div id="modal-<?= $sub['id'] ?>"
                class="hidden fixed inset-0 bg-black bg-opacity-40 z-50 flex items-center justify-center">
                <div class="bg-white max-w-2xl w-full mx-4 p-6 rounded shadow-lg overflow-y-auto max-h-[90vh]">
                  <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-bold">Submitted Questions</h2>
                    <button onclick="closeModal(<?= $sub['id'] ?>)" class="text-red-600 font-bold text-xl">&times;</button>
                  </div>
                  <div class="prose max-w-none">
                    <?= $sub['questions'] ?>
                  </div>
                </div>
              </div>
            <?php endif; ?>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <script>
    function openModal(id) {
      document.getElementById('modal-' + id).classList.remove('hidden');
    }
    function closeModal(id) {
      document.getElementById('modal-' + id).classList.add('hidden');
    }
  </script>

</body>

</html>