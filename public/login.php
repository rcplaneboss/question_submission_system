<?php
session_start();
$message = $_SESSION['message'] ?? null;
unset($_SESSION['message']);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .montserrat-sans {
      font-family: "Montserrat", sans-serif;
    }
  </style>
</head>
<body class="bg-gray-100 flex justify-center items-center min-h-screen montserrat-sans">

  <form action="../app/controllers/AuthController.php" method="POST"
    class="bg-white p-8 rounded shadow-md w-full max-w-md space-y-5">

    <h2 class="text-2xl font-bold text-center">Login</h2>

    <?php if ($message): ?>
      <div class="bg-red-100 text-red-700 p-3 rounded text-sm"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <input type="email" name="email" placeholder="Email"
      class="w-full border px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400" required autofocus />

    <input type="password" name="password" placeholder="Password"
      class="w-full border px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400" required />

    <input type="hidden" name="action" value="login" />
    <button type="submit"
      class="bg-green-600 hover:bg-green-700 transition-all duration-200 text-white font-semibold py-2 rounded w-full">
      Sign In
    </button>

    <p class="text-center text-sm mt-2">Don't have an account? 
      <a href="register.php" class="text-blue-600 underline">Register here</a>
    </p>
  </form>

</body>
</html>
