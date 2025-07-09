<!DOCTYPE html>
<html>
<head>
  <title>Test TinyMCE</title>
  <script src="https://cdn.tiny.cloud/1/pdk2i1evtcchu1eaweamhuw8c72aguxs539rqqwoiamu80mt/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
</head>
<body>
  <textarea id="editor">Start typing...</textarea>

  <script>
    tinymce.init({
      selector: '#editor'
    });
  </script>
</body>
</html>
