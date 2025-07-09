<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

require_once '../config/Database.php';
$db = new Database();
$conn = $db->getConnection();

$submission_id = $_GET['id'];
$teacher_id = $_SESSION['user']['id'];

$stmt = $conn->prepare("SELECT * FROM submissions WHERE id = ? AND teacher_id = ?");
$stmt->execute([$submission_id, $teacher_id]);
$submission = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$submission) {
    echo "Submission not found or you don't have permission.";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Edit Submission</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.tiny.cloud/1/pdk2i1evtcchu1eaweamhuw8c72aguxs539rqqwoiamu80mt/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
</head>
<body class="bg-gray-100 min-h-screen">
  <?php 
include '../partials/navbar.php';
?>
  <div class="max-w-xl mx-auto p-6 bg-white mt-10 rounded shadow">
    <h2 class="text-xl font-bold mb-4">Edit Submission</h2>

    <form action="../app/controllers/SubmissionController.php" method="POST" enctype="multipart/form-data" class="space-y-4">
      <input type="hidden" name="id" value="<?= $submission['id'] ?>" />
      <input type="text" name="subject" value="<?= htmlspecialchars($submission['subject']) ?>" required class="w-full border px-3 py-2 rounded" />
      <input type="text" name="class" value="<?= htmlspecialchars($submission['class']) ?>" required class="w-full border px-3 py-2 rounded" />

      <select name="type" required class="w-full border px-3 py-2 rounded">
        <option value="Test" <?= $submission['type'] === 'Test' ? 'selected' : '' ?>>Test</option>
        <option value="Exam" <?= $submission['type'] === 'Exam' ? 'selected' : '' ?>>Exam</option>
      </select>

      <label class="block">Replace File (optional)</label>
      <input type="file" name="file" class="w-full border px-3 py-2 rounded" />

      <label class="block">Edit Text Questions (optional)</label>
      <textarea name="questions" id='questions' rows="5" class="w-full border px-3 py-2 rounded"><?= htmlspecialchars($submission['questions']) ?></textarea>

      <input type="hidden" name="action" value="update" />
      <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Update Submission</button>
    </form>
  </div>



  <script>
    tinymce.init({
      selector: '#questions',
      height: 300,
      plugins: [
        'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 'image', 'link', 'lists', 'media',
        'searchreplace', 'table', 'visualblocks', 'wordcount',
        'checklist', 'mediaembed', 'casechange', 'formatpainter', 'pageembed', 'a11ychecker',
        'tinymcespellchecker', 'permanentpen', 'powerpaste', 'advtable', 'advcode', 'editimage',
        'advtemplate', 'ai', 'mentions', 'tinycomments', 'tableofcontents', 'footnotes', 'mergetags',
        'autocorrect', 'typography', 'inlinecss', 'markdown', 'importword', 'exportword', 'exportpdf'
      ],
      toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
      tinycomments_mode: 'embedded',
      tinycomments_author: 'Teacher',
      mergetags_list: [
        { value: 'First.Name', title: 'First Name' },
        { value: 'Email', title: 'Email' },
      ]
    });
  </script>
</body>
</html>
