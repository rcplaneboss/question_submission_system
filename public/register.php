<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Teacher Registration</title>
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
<body class="bg-gray-100 flex justify-center items-center min-h-screen montserrat-sans">
  <form action="../app/controllers/AuthController.php" method="POST" class="bg-white p-8 rounded shadow-md w-full max-w-md space-y-4">
    <h2 class="text-xl font-bold">Register as Teacher</h2>

    <input type="text" name="name" placeholder="Full Name" required class="w-full border px-3 py-2 rounded" />
    <input type="email" name="email" placeholder="Email" required class="w-full border px-3 py-2 rounded" />
    <input type="password" name="password" placeholder="Password" required class="w-full border px-3 py-2 rounded" />

    <select name="section" required class="w-full border px-3 py-2 rounded">
      <option value="">-- Select Section --</option>
      <option value="Arabic">Arabic</option>
      <option value="Western">Western</option>
    </select>

    <input type="hidden" name="action" value="register" />
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 w-full">Register</button>

    <p class="text-sm mt-2">Already have an account? <a href="login.php" class="text-blue-600">Login here</a></p>
  </form>
</body>
</html>
