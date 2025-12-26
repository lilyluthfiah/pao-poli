<?php
function handleUpload(string $field, string $targetDir, array $allowedExt = ['pdf','jpg','jpeg','png']): ?string {
  if (!isset($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) return null;

  if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

  $name = $_FILES[$field]['name'];
  $tmp  = $_FILES[$field]['tmp_name'];

  $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
  if (!in_array($ext, $allowedExt, true)) {
    throw new RuntimeException("Tipe file tidak didukung.");
  }

  $safeName = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', pathinfo($name, PATHINFO_FILENAME));
  $final = $safeName . "_" . date("Ymd_His") . "_" . bin2hex(random_bytes(4)) . "." . $ext;

  $dest = rtrim($targetDir, "/\\") . DIRECTORY_SEPARATOR . $final;
  if (!move_uploaded_file($tmp, $dest)) {
    throw new RuntimeException("Gagal upload file.");
  }

  return $final;
}
