<?php
session_start();
require_once '../vendor/autoload.php';
require_once '../config/Database.php';

use PhpOffice\PhpWord\IOFactory;

header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdn.tailwindcss.com");
header("X-Content-Type-Options: nosniff");

$db = new Database();
$conn = $db->getConnection();

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "No ID provided.";
    exit;
}

$stmt = $conn->prepare("SELECT * FROM submissions WHERE id = ?");
$stmt->execute([$id]);
$submission = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$submission) {
    echo "Submission not found.";
    exit;
}

$filePath = '../uploads/' . $submission['file_path'];
$extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

if (!file_exists($filePath)) {
    echo "<p class='text-red-600'>File does not exist on the server.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>View File</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/@tailwindcss/typography@0.5.2/dist/typography.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 min-h-screen p-8 montserrat-sans">
    <div class="max-w-3xl mx-auto bg-white p-6 rounded shadow space-y-6">
        <h2 class="text-xl font-bold">File Preview</h2>

        <?php if ($extension === 'pdf'): ?>
            <iframe src="<?= $filePath ?>" width="100%" height="600px" class="border rounded"></iframe>

        <?php elseif ($extension === 'docx'): ?>
            <div class="prose max-w-none">
                <?php
                try {
                    $phpWord = IOFactory::load($filePath);
                    $hasText = false;
                    foreach ($phpWord->getSections() as $section) {
                        foreach ($section->getElements() as $element) {
                            if (method_exists($element, 'getText')) {
                                echo '<p>' . nl2br(htmlspecialchars($element->getText())) . '</p>';
                                $hasText = true;
                            }
                        }
                    }
                    if (!$hasText) {
                        echo "<p class='text-gray-500'>No readable text found in this Word document.</p>";
                    }
                } catch (Exception $e) {
                    echo "<p class='text-red-600'>Unable to read document.</p>";
                }
                ?>
            </div>

        <?php else: ?>
            <p class="text-gray-600">Preview not supported for this file type.</p>
        <?php endif; ?>

        <a href="edit.php?id=<?= $submission['id'] ?>"
            class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Edit Submission</a>
    </div>
</body>

</html>