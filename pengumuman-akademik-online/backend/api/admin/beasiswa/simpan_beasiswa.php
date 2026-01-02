<?php
// backend/api/admin/beasiswa/simpan_beasiswa.php
declare(strict_types=1);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/../../../config/database.php"; // ✅ sesuaikan kalau path config DB kamu beda

function out(array $arr, int $code = 200): void {
  http_response_code($code);
  echo json_encode($arr);
  exit;
}

if (($_SERVER["REQUEST_METHOD"] ?? "") !== "POST") {
  out(["ok" => false, "message" => "Method not allowed"], 405);
}

// ====== ambil field dari multipart/form-data (FormData JS) ======
$nama          = trim((string)($_POST["nama"] ?? ""));
$jenis         = trim((string)($_POST["jenis"] ?? ""));
$penyelenggara = trim((string)($_POST["penyelenggara"] ?? ""));
$deskripsi     = trim((string)($_POST["deskripsi"] ?? ""));
$tgl_mulai     = trim((string)($_POST["tanggal_mulai"] ?? ""));
$tgl_akhir     = trim((string)($_POST["tanggal_akhir"] ?? ""));

if ($nama === "" || $jenis === "" || $penyelenggara === "" || $tgl_mulai === "" || $tgl_akhir === "") {
  out(["ok" => false, "message" => "Nama, Jenis, Penyelenggara, Tanggal Mulai, dan Tanggal Akhir wajib diisi."], 422);
}

if ($tgl_akhir < $tgl_mulai) {
  out(["ok" => false, "message" => "Tanggal Akhir tidak boleh lebih kecil dari Tanggal Mulai."], 422);
}

// ====== validasi file pdf ======
if (!isset($_FILES["pdf"]) || $_FILES["pdf"]["error"] !== UPLOAD_ERR_OK) {
  out(["ok" => false, "message" => "PDF detail wajib diupload."], 422);
}

$pdf = $_FILES["pdf"];
$maxBytes = 10 * 1024 * 1024; // 10MB
if (($pdf["size"] ?? 0) > $maxBytes) {
  out(["ok" => false, "message" => "Ukuran PDF maksimal 10MB."], 422);
}

$ext = strtolower(pathinfo($pdf["name"] ?? "", PATHINFO_EXTENSION));
if ($ext !== "pdf") {
  out(["ok" => false, "message" => "File harus PDF."], 422);
}

// cek MIME (opsional tapi bagus)
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $pdf["tmp_name"]);
finfo_close($finfo);
if ($mime !== "application/pdf") {
  out(["ok" => false, "message" => "File bukan PDF valid."], 422);
}

// ====== simpan file ======
$uploadDirAbs = __DIR__ . "/../../../uploads/beasiswa";
if (!is_dir($uploadDirAbs)) {
  mkdir($uploadDirAbs, 0775, true);
}

$filename = "beasiswa_" . date("Ymd_His") . "_" . bin2hex(random_bytes(4)) . ".pdf";
$destAbs = $uploadDirAbs . "/" . $filename;

// path yang disimpan ke DB (relatif dari root project)
$pdfPathDb = "backend/uploads/beasiswa/" . $filename;

// NOTE: karena folder uploads kita taruh di backend/uploads/...
// pastikan foldernya sesuai:
$publicUploadAbs = __DIR__ . "/../../../uploads/beasiswa";
if (!move_uploaded_file($pdf["tmp_name"], $destAbs)) {
  out(["ok" => false, "message" => "Gagal upload PDF."], 500);
}

// ====== insert ke DB ======
try {
  $conn->set_charset("utf8mb4");

  // created_by opsional (kalau ada session user)
  $createdBy = null;
  if (session_status() === PHP_SESSION_NONE) session_start();
  if (isset($_SESSION["user"]["id"])) $createdBy = (int)$_SESSION["user"]["id"];

  $sql = "INSERT INTO beasiswa (nama, jenis, penyelenggara, deskripsi, tanggal_mulai, tanggal_akhir, pdf_path, created_by)
          VALUES (?,?,?,?,?,?,?,?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param(
    "sssssssi",
    $nama, $jenis, $penyelenggara, $deskripsi, $tgl_mulai, $tgl_akhir, $pdfPathDb, $createdBy
  );
  $stmt->execute();

  out([
    "ok" => true,
    "message" => "Beasiswa berhasil disimpan.",
    "id" => $conn->insert_id,
    "pdf_path" => $pdfPathDb
  ]);

} catch (Throwable $e) {
  // rollback upload file jika insert gagal
  if (file_exists($destAbs)) @unlink($destAbs);
  out(["ok" => false, "message" => "Gagal menyimpan beasiswa: " . $e->getMessage()], 500);
}
