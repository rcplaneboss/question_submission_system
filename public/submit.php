<?php
ob_start();
session_start();
// include '../partials/navbar.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<?php
include '../partials/navbar.php';
?>

<html>
<head>
  <title>Submit Question</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- ✅ TinyMCE with your API key -->
  <script src="https://cdn.tiny.cloud/1/pdk2i1evtcchu1eaweamhuw8c72aguxs539rqqwoiamu80mt/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
 
</head>

<style>
  .montserrat-sans {
    font-family: "Montserrat", sans-serif;
    font-optical-sizing: auto;
    font-style: normal;
  }
</style>

<body class="bg-gray-100 min-h-screen montserrat-sans">

  <div class="max-w-xl mx-auto p-6 bg-white mt-10 rounded shadow">
    <h2 class="text-xl font-bold mb-4">Submit Question</h2>

    <form action="../app/controllers/SubmissionController.php" method="POST" enctype="multipart/form-data" class="space-y-4">
      <?php
      require_once '../config/Database.php';
      $db = new Database();
      $conn = $db->getConnection();

      $user_section = $_SESSION['user']['section'];
      $subjects = $conn->prepare("SELECT * FROM subjects WHERE section = ?");
      $subjects->execute([$user_section]);

      $classes = $conn->prepare("SELECT * FROM classes WHERE section = ?");
      $classes->execute([$user_section]);
      ?>

      <select name="subject" required class="w-full border px-3 py-2 rounded">
        <option value="">Select Subject</option>
        <?php foreach ($subjects as $sub): ?>
          <option value="<?= htmlspecialchars($sub['name']) ?>"><?= htmlspecialchars($sub['name']) ?></option>
        <?php endforeach; ?>
      </select>

      <select name="class" required class="w-full border px-3 py-2 rounded">
        <option value="">Select Class</option>
        <?php foreach ($classes as $cls): ?>
          <option value="<?= htmlspecialchars($cls['name']) ?>"><?= htmlspecialchars($cls['name']) ?></option>
        <?php endforeach; ?>
      </select>

      <select name="type" required class="w-full border px-3 py-2 rounded">
        <option value="">-- Select Type --</option>
        <option value="Test">Test</option>
        <option value="Exam">Exam</option>
      </select>

      <label class="block font-semibold">Upload File (PDF or Word)</label>
      <input type="file" name="file" class="w-full border px-3 py-2 rounded" />

      <label class="block font-semibold">OR Paste Questions Below</label>
      <textarea name="questions" id="questions" rows="5" placeholder="Type your questions here..."></textarea>

      <input type="hidden" name="action" value="submit" />
      <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Submit</button>
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
<?php ob_end_flush(); ?>
</html>
