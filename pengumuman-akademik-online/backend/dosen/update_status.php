<?php
require "../config.php";
header("Content-Type: application/json; charset=UTF-8");

$data = json_decode(file_get_contents("php://input"), true);

$id = intval($data['id'] ?? 0);
$status = trim($data['status'] ?? "");

$allowed = ['Menunggu','Disetujui','Ditolak'];
if ($id <= 0 || !in_array($status, $allowed, true)) {
  http_response_code(400);
  echo json_encode(["status" => "error", "message" => "Invalid data"], JSON_UNESCAPED_UNICODE);
  exit;
}

$stmt = mysqli_prepare($conn, "UPDATE pendaftar_beasiswa SET status=? WHERE id=?");
mysqli_stmt_bind_param($stmt, "si", $status, $id);

if (!mysqli_stmt_execute($stmt)) {
  http_response_code(500);
  echo json_encode(["status" => "error", "message" => "Gagal update status"], JSON_UNESCAPED_UNICODE);
  exit;
}

echo json_encode(["status" => "success"], JSON_UNESCAPED_UNICODE);
