<?php
if (!isset($_SESSION)) session_start();
$user = $_SESSION['user'] ?? null;
?>
<nav class="bg-blue-700 text-white px-6 py-4 sticky top-0">
  <div class="flex justify-between items-center">
    <a href="<?= $user['role'] === 'admin' ? 'admin-dashboard.php' : 'dashboard.php' ?>" class="text-lg font-bold">
      📝 Question Manager
    </a>
    <button id="menuToggle" class="md:hidden text-xl focus:outline-none">☰</button>
  </div>

  <div id="menu" class="hidden flex-col space-y-2 mt-4 md:flex md:flex-row md:space-y-0 md:space-x-6 md:mt-0">
    <?php if ($user && $user['role'] === 'admin'): ?>
      <a href="admin-dashboard.php" class="hover:underline block">Dashboard</a>
      <a href="admin-deadlines.php" class="hover:underline block">Set Deadlines</a>
      <a href="admin-subjects.php" class="hover:underline block">Manage Subjects & Classes</a>
      <a href="zip-download.php" class="hover:underline block">Download Submissions</a>
    <?php elseif ($user && $user['role'] === 'teacher'): ?>
      <a href="dashboard.php" class="hover:underline block">Dashboard</a>
      <a href="submit.php" class="hover:underline block">Submit Question</a>
    <?php endif; ?>
    <a href="logout.php" class="hover:underline block text-red-300">Logout</a>
  </div>

  <script>
    document.getElementById('menuToggle').onclick = () => {
      const menu = document.getElementById('menu');
      menu.classList.toggle('hidden');
    };
  </script>
</nav>