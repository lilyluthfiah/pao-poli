<?php
declare(strict_types=1);

require_once __DIR__ . "/../../../config/db.php";
require_once __DIR__ . "/../../../middleware/auth.php";
require_once __DIR__ . "/../../../helpers/response.php";

if (!function_exists("jsonResponse")) {
  function jsonResponse($payload, int $code = 200): void {
    http_response_code($code);
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode($payload);
    exit;
  }
}

$id = (int)($_GET["id"] ?? 0);
if ($id <= 0) jsonResponse(["success"=>false,"message"=>"ID beasiswa tidak valid"], 422);

try {
  $stmt = $conn->prepare("
    SELECT id, nama, jenis, penyelenggara, deskripsi, tanggal_mulai, tanggal_akhir, pdf_path
    FROM beasiswa
    WHERE id = ?
    LIMIT 1
  ");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $beasiswa = $stmt->get_result()->fetch_assoc();
  if (!$beasiswa) jsonResponse(["success"=>false,"message"=>"Beasiswa tidak ditemukan"], 404);

  $stmt2 = $conn->prepare("
    SELECT id, nama_berkas, tipe_file, max_size_mb, wajib
    FROM beasiswa_berkas
    WHERE beasiswa_id = ?
    ORDER BY id ASC
  ");
  $stmt2->bind_param("i", $id);
  $stmt2->execute();
  $berkas = [];
  $r2 = $stmt2->get_result();
  while ($row = $r2->fetch_assoc()) $berkas[] = $row;

  jsonResponse(["success" => true, "data" => ["beasiswa" => $beasiswa, "berkas" => $berkas]]);
} catch (Throwable $e) {
  jsonResponse(["success" => false, "message" => $e->getMessage()], 500);
}
