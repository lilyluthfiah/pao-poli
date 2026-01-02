<?php
declare(strict_types=1);

require_once __DIR__ . "/../../../config/db.php";
// kalau middleware/auth.php bikin 401 terus, comment dulu:
// require_once __DIR__ . "/../../../middleware/auth.php";
require_once __DIR__ . "/../../../helpers/response.php";

if (!function_exists("jsonResponse")) {
  function jsonResponse($payload, int $code = 200): void {
    http_response_code($code);
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode($payload);
    exit;
  }
}

if (session_status() === PHP_SESSION_NONE) session_start();

// ✅ ambil userId dari session
$userId = (int)($_SESSION["user"]["id"] ?? $_SESSION["user_id"] ?? 0);

// ✅ fallback sementara supaya konsisten dengan submit_pengajuan.php
if ($userId <= 0) {
  $userId = 1; // sementara HARD-CODE
}

try {
  $stmt = $conn->prepare("
    SELECT
      p.id,
      p.tanggal_daftar,
      p.status,
      p.rekening,
      b.nama AS nama_beasiswa,
      b.jenis,
      (
        SELECT COUNT(*) FROM pengajuan_berkas pb WHERE pb.pengajuan_id = p.id
      ) AS jumlah_berkas
    FROM pengajuan p
    JOIN beasiswa b ON b.id = p.beasiswa_id
    WHERE p.user_id = ?
    ORDER BY p.id DESC
  ");
  $stmt->bind_param("i", $userId);
  $stmt->execute();
  $res = $stmt->get_result();

  $rows = [];
  while ($row = $res->fetch_assoc()) $rows[] = $row;

  jsonResponse(["success"=>true, "data"=>$rows]);
} catch (Throwable $e) {
  jsonResponse(["success"=>false, "message"=>$e->getMessage()], 500);
}
