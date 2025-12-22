<?php
require "../config.php";
header("Content-Type: application/json; charset=UTF-8");

$beasiswa_id = intval($_GET['beasiswa_id'] ?? 0);
if ($beasiswa_id <= 0) {
  echo json_encode([]);
  exit;
}

$stmt = mysqli_prepare($conn, "
  SELECT id, nama, nim, prodi, status
  FROM pendaftar_beasiswa
  WHERE beasiswa_id = ?
  ORDER BY created_at DESC
");
mysqli_stmt_bind_param($stmt, "i", $beasiswa_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data = [];

while ($row = mysqli_fetch_assoc($result)) {
  $data[] = $row;
}

echo json_encode($data, JSON_UNESCAPED_UNICODE);
