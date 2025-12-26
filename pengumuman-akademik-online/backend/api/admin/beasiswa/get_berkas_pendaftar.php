<?php
require "../config.php";
header("Content-Type: application/json; charset=UTF-8");

$pendaftar_id = intval($_GET['pendaftar_id'] ?? 0);
if ($pendaftar_id <= 0) {
  echo json_encode([]);
  exit;
}

$stmt = mysqli_prepare($conn, "
  SELECT id, nama_berkas, file_url
  FROM pendaftar_berkas
  WHERE pendaftar_id = ?
  ORDER BY id ASC
");
mysqli_stmt_bind_param($stmt, "i", $pendaftar_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data = [];

while ($row = mysqli_fetch_assoc($result)) {
  $data[] = $row;
}

echo json_encode($data, JSON_UNESCAPED_UNICODE);
