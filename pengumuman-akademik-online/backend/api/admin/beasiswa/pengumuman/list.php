<?php
declare(strict_types=1);

require_once __DIR__ . "/../../../config/database.php";
header("Content-Type: application/json; charset=utf-8");
if (session_status() === PHP_SESSION_NONE) session_start();

try {
  $sql = "SELECT id, judul, deskripsi, created_at FROM pengumuman ORDER BY id DESC";
  $res = $conn->query($sql);

  $data = [];
  while ($row = $res->fetch_assoc()) {
    $data[] = [
      "id" => (int)$row["id"],
      "judul" => (string)$row["judul"],
      "deskripsi" => (string)$row["deskripsi"],
      "tanggal" => date("Y-m-d H:i", strtotime($row["created_at"]))
    ];
  }

  echo json_encode(["success" => true, "data" => $data]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
