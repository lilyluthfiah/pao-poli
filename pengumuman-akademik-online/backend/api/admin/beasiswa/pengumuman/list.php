<?php
declare(strict_types=1);

require_once __DIR__ . "/../../../../config/database.php";

header("Content-Type: application/json; charset=utf-8");

function out(array $payload, int $code = 200): void {
  http_response_code($code);
  echo json_encode($payload);
  exit;
}

if (($_SERVER["REQUEST_METHOD"] ?? "") !== "GET") {
  out(["success" => false, "message" => "Method not allowed"], 405);
}

try {
  $sql = "SELECT id, judul, deskripsi, created_at
          FROM pengumuman
          ORDER BY id DESC";

  $res = $conn->query($sql);

  $rows = [];
  while ($row = $res->fetch_assoc()) {
    $rows[] = [
      "id" => $row["id"],
      "judul" => $row["judul"],
      "deskripsi" => $row["deskripsi"],
      "tanggal" => $row["created_at"] ?? "-"
    ];
  }

  out(["success" => true, "data" => $rows]);

} catch (Throwable $e) {
  out(["success" => false, "message" => "Server error: " . $e->getMessage()], 500);
}
