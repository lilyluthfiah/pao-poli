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

if (($_SERVER["REQUEST_METHOD"] ?? "") !== "POST") {
  jsonResponse(["success"=>false,"message"=>"Method not allowed"], 405);
}

session_start();
$userId = (int)($_SESSION["user"]["id"] ?? $_SESSION["user_id"] ?? 0);
if ($userId <= 0) jsonResponse(["success"=>false,"message"=>"Unauthorized"], 401);

$id = (int)($_POST["id"] ?? 0);
if ($id <= 0) jsonResponse(["success"=>false,"message"=>"ID tidak valid"], 422);

try {
  // cek punya user + status
  $stmt = $conn->prepare("SELECT status FROM pengajuan WHERE id=? AND user_id=? LIMIT 1");
  $stmt->bind_param("ii", $id, $userId);
  $stmt->execute();
  $row = $stmt->get_result()->fetch_assoc();
  if (!$row) jsonResponse(["success"=>false,"message"=>"Data tidak ditemukan"], 404);

  if ($row["status"] !== "PROSES") {
    jsonResponse(["success"=>false,"message"=>"Pengajuan tidak bisa dihapus karena sudah diproses."], 422);
  }

  // ambil file paths untuk dihapus dari disk
  $stmtF = $conn->prepare("SELECT file_path FROM pengajuan_berkas WHERE pengajuan_id=?");
  $stmtF->bind_param("i", $id);
  $stmtF->execute();
  $resF = $stmtF->get_result();

  $paths = [];
  while ($f = $resF->fetch_assoc()) $paths[] = (string)$f["file_path"];

  // delete pengajuan (FK cascade akan hapus pengajuan_berkas)
  $stmtD = $conn->prepare("DELETE FROM pengajuan WHERE id=? AND user_id=?");
  $stmtD->bind_param("ii", $id, $userId);
  $stmtD->execute();

  // hapus file fisik (kalau path sesuai)
  foreach ($paths as $p) {
    // contoh p: backend/uploads/pengajuan/xxx.pdf
    $abs = realpath(__DIR__ . "/../../../..") . DIRECTORY_SEPARATOR . str_replace(["/","\\"], DIRECTORY_SEPARATOR, $p);
    if ($abs && file_exists($abs)) @unlink($abs);
  }

  jsonResponse(["success"=>true,"message"=>"Pengajuan berhasil dihapus"]);
} catch (Throwable $e) {
  jsonResponse(["success"=>false,"message"=>$e->getMessage()], 500);
}
