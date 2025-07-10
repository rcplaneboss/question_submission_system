<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Welcome to the Question Submission System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white shadow-lg rounded p-8 text-center max-w-md">
        <h1 class="text-2xl font-bold mb-4 text-blue-700">Welcome to the Question Submission System</h1>
        <p class="text-gray-700 mb-6">Submit and manage questions easily as a Teacher or Admin.</p>

        <div class="flex flex-col gap-4">
            <a href="public/login.php" class="bg-green-600 text-white py-2 px-4 rounded hover:bg-green-700">Login as
                Teacher</a>
            <a href="public/admin-login.php" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">Login as
                Admin</a>
        </div>
    </div>
</body>

</html>