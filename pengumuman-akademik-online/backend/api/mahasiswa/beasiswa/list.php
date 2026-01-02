<?php
declare(strict_types=1);

header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . "/../../../config/database.php";

function jsonResponse(array $payload, int $code = 200): void {
  http_response_code($code);
  echo json_encode($payload);
  exit;
}

try {
  $sql = "
    SELECT
      id, nama, jenis, penyelenggara, deskripsi,
      tanggal_mulai, tanggal_akhir, pdf_path
    FROM beasiswa
    WHERE CURDATE() BETWEEN tanggal_mulai AND tanggal_akhir
    ORDER BY tanggal_akhir ASC, id DESC
  ";

  $res = $conn->query($sql);
  $rows = [];
  while ($row = $res->fetch_assoc()) {
    $rows[] = $row;
  }

  // 🔽 TARUH DI SINI (PENGGANTI jsonResponse LAMA)
  jsonResponse([
    "success" => true,
    "server_date" => date("Y-m-d"),
    "data" => $rows
  ]);

} catch (Throwable $e) {
  jsonResponse([
    "success" => false,
    "message" => $e->getMessage()
  ], 500);
}
