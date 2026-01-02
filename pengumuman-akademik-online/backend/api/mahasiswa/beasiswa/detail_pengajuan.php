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

session_start();
$userId = (int)($_SESSION["user"]["id"] ?? $_SESSION["user_id"] ?? 0);
if ($userId <= 0) jsonResponse(["success"=>false,"message"=>"Unauthorized"], 401);

$id = (int)($_GET["id"] ?? 0);
if ($id <= 0) jsonResponse(["success"=>false,"message"=>"ID tidak valid"], 422);

try {
  $stmt = $conn->prepare("
    SELECT
      p.id, p.tanggal_daftar, p.status, p.rekening, p.catatan_admin,
      b.nama AS nama_beasiswa, b.jenis, b.penyelenggara, b.pdf_path,
      b.tanggal_mulai, b.tanggal_akhir
    FROM pengajuan p
    JOIN beasiswa b ON b.id = p.beasiswa_id
    WHERE p.id = ? AND p.user_id = ?
    LIMIT 1
  ");
  $stmt->bind_param("ii", $id, $userId);
  $stmt->execute();
  $head = $stmt->get_result()->fetch_assoc();
  if (!$head) jsonResponse(["success"=>false,"message"=>"Data tidak ditemukan"], 404);

  $stmtB = $conn->prepare("
    SELECT id, nama_berkas, file_path, uploaded_at
    FROM pengajuan_berkas
    WHERE pengajuan_id = ?
    ORDER BY id ASC
  ");
  $stmtB->bind_param("i", $id);
  $stmtB->execute();
  $resB = $stmtB->get_result();
  $files = [];
  while ($r = $resB->fetch_assoc()) $files[] = $r;

  jsonResponse(["success"=>true, "data"=>["pengajuan"=>$head, "berkas"=>$files]]);
} catch (Throwable $e) {
  jsonResponse(["success"=>false,"message"=>$e->getMessage()], 500);
}
