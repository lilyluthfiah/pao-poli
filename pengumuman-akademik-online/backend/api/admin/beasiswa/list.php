<?php
// backend/api/admin/beasiswa/list.php
declare(strict_types=1);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/../../../config/database.php"; // ✅ sesuaikan kalau beda

function out(array $arr, int $code = 200): void {
  http_response_code($code);
  echo json_encode($arr);
  exit;
}

try {
  $conn->set_charset("utf8mb4");

  $sql = "SELECT id, nama, jenis, penyelenggara, tanggal_akhir, pdf_path
          FROM beasiswa
          ORDER BY id DESC";
  $res = $conn->query($sql);

  $rows = [];
  while ($row = $res->fetch_assoc()) $rows[] = $row;

  out(["ok" => true, "data" => $rows]);

} catch (Throwable $e) {
  out(["ok" => false, "message" => $e->getMessage()], 500);
}
