<?php
declare(strict_types=1);

header("Content-Type: application/json; charset=utf-8");
require_once __DIR__ . "/../../../config/db.php";

function jsonResponse(array $payload, int $code = 200): void {
  http_response_code($code);
  echo json_encode($payload);
  exit;
}

if (($_SERVER["REQUEST_METHOD"] ?? "") !== "POST") {
  jsonResponse(["success"=>false,"message"=>"Method not allowed"], 405);
}

if (session_status() === PHP_SESSION_NONE) session_start();

// ambil user id dari session (kalau auth belum jadi, fallback dulu)
$userId = (int)($_SESSION["user"]["id"] ?? $_SESSION["user_id"] ?? 0);
if ($userId <= 0) $userId = 1; // sementara

$beasiswaId = (int)($_POST["beasiswa_id"] ?? 0);
$rekening   = trim((string)($_POST["rekening"] ?? ""));

if ($beasiswaId <= 0) jsonResponse(["success"=>false,"message"=>"Beasiswa wajib dipilih"], 422);
if ($rekening === "") jsonResponse(["success"=>false,"message"=>"Rekening wajib diisi"], 422);

function ensureUploadDir(string $dir): void {
  if (!is_dir($dir)) mkdir($dir, 0775, true);
}

function safeName(string $name): string {
  $name = preg_replace('/[^a-zA-Z0-9_\.\-]+/', '_', $name);
  return trim($name, "_");
}

function moveOneFile(array $file, string $targetDir): array {
  $original = $file["name"] ?? "file";
  $tmp      = $file["tmp_name"] ?? "";
  $err      = (int)($file["error"] ?? UPLOAD_ERR_NO_FILE);

  if ($err !== UPLOAD_ERR_OK) return ["ok"=>false, "msg"=>"Upload error: ".$err];
  if (!is_uploaded_file($tmp)) return ["ok"=>false, "msg"=>"File tidak valid"];

  $ext   = pathinfo($original, PATHINFO_EXTENSION);
  $base  = safeName(pathinfo($original, PATHINFO_FILENAME));
  $final = $base . "_" . date("Ymd_His") . "_" . bin2hex(random_bytes(4)) . ($ext ? ".".$ext : "");

  $dest = rtrim($targetDir, "/\\") . DIRECTORY_SEPARATOR . $final;
  if (!move_uploaded_file($tmp, $dest)) return ["ok"=>false, "msg"=>"Gagal simpan file"];

  return ["ok"=>true, "stored"=>$final, "original"=>$original];
}

try {
  // cek beasiswa ada + aktif
  $stmt = $conn->prepare("SELECT id, tanggal_mulai, tanggal_akhir FROM beasiswa WHERE id=? LIMIT 1");
  $stmt->bind_param("i", $beasiswaId);
  $stmt->execute();
  $b = $stmt->get_result()->fetch_assoc();

  if (!$b) jsonResponse(["success"=>false,"message"=>"Beasiswa tidak ditemukan"], 404);

  $today = date("Y-m-d");
  if ($today < $b["tanggal_mulai"] || $today > $b["tanggal_akhir"]) {
    jsonResponse(["success"=>false,"message"=>"Beasiswa tidak sedang aktif"], 422);
  }

  // ✅ INSERT pengajuan (include updated_at agar aman)
  $stmtIns = $conn->prepare("
    INSERT INTO pengajuan (beasiswa_id, user_id, rekening, tanggal_daftar, status, updated_at)
    VALUES (?, ?, ?, NOW(), 'PROSES', NOW())
  ");
  $stmtIns->bind_param("iis", $beasiswaId, $userId, $rekening);

  try {
    $stmtIns->execute();
  } catch (Throwable $e) {
    if (($conn->errno ?? 0) === 1062) {
      jsonResponse(["success"=>false,"message"=>"Kamu sudah pernah mengajukan beasiswa ini."], 409);
    }
    throw $e;
  }

  $pengajuanId = (int)$conn->insert_id;

  // upload opsional
  $files = $_FILES["berkas"] ?? null;
  if (!$files) {
    jsonResponse([
      "success"=>true,
      "message"=>"Pengajuan berhasil dikirim (tanpa berkas).",
      "data"=>["pengajuan_id"=>$pengajuanId, "saved_files"=>0]
    ]);
  }

  $uploadDir = __DIR__ . "/../../../uploads/pengajuan/";
  ensureUploadDir($uploadDir);

  $names = $files["name"] ?? [];
  $tmps  = $files["tmp_name"] ?? [];
  $errs  = $files["error"] ?? [];

  $normalized = [];
  if (is_array($names)) {
    foreach ($names as $k => $v) {
      if (is_array($v)) continue;
      $normalized[] = [
        "key" => (string)$k,
        "name" => $names[$k],
        "tmp_name" => $tmps[$k],
        "error" => $errs[$k] ?? UPLOAD_ERR_NO_FILE
      ];
    }
  } else {
    $normalized[] = ["key"=>"0","name"=>$names,"tmp_name"=>$tmps,"error"=>$errs];
  }

  $stmtB = $conn->prepare("
    INSERT INTO pengajuan_berkas (pengajuan_id, nama_berkas, file_path)
    VALUES (?, ?, ?)
  ");

  $savedCount = 0;

  foreach ($normalized as $f) {
    if (($f["error"] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) continue;

    $move = moveOneFile($f, $uploadDir);
    if (!$move["ok"]) continue;

    // sesuaikan path publik project kamu
    $storedRelPath = "backend/uploads/pengajuan/" . $move["stored"];
    $label = $move["original"];

    $stmtB->bind_param("iss", $pengajuanId, $label, $storedRelPath);
    $stmtB->execute();
    $savedCount++;
  }

  jsonResponse([
    "success" => true,
    "message" => "Pengajuan berhasil dikirim.",
    "data" => ["pengajuan_id" => $pengajuanId, "saved_files" => $savedCount]
  ]);

} catch (Throwable $e) {
  jsonResponse([
    "success" => false,
    "message" => "Gagal menyimpan pengajuan",
    "debug" => $e->getMessage()
  ], 500);
}
