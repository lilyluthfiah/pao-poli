<?php
require "../config.php";
header("Content-Type: application/json; charset=UTF-8");

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
  echo json_encode(["status" => "error", "message" => "Data tidak valid"]);
  exit;
}

$nama          = trim($data['nama'] ?? '');
$jenis         = trim($data['jenis'] ?? '');
$penyelenggara = trim($data['penyelenggara'] ?? '');
$deskripsi     = trim($data['deskripsi'] ?? '');

$tgl_mulai      = $data['tanggal_mulai'] ?? '';
$tgl_akhir      = $data['tanggal_akhir'] ?? '';
$tgl_seleksi    = $data['tanggal_seleksi'] ?? null;
$tgl_pengumuman = $data['tanggal_pengumuman'] ?? null;

$min_ipk      = isset($data['min_ipk']) && $data['min_ipk'] !== null ? floatval($data['min_ipk']) : 0;
$min_semester = isset($data['min_semester']) && $data['min_semester'] !== null ? intval($data['min_semester']) : 0;
$allowed_prodi = $data['allowed_prodi'] ?? '';

$persyaratan = $data['persyaratan'] ?? [];
$berkas      = $data['berkas'] ?? [];

if (!$nama || !$jenis || !$penyelenggara || !$tgl_mulai || !$tgl_akhir) {
  echo json_encode(["status" => "error", "message" => "Field wajib belum lengkap"]);
  exit;
}

// INSERT beasiswa
$sql = "INSERT INTO beasiswa
(nama, jenis, penyelenggara, deskripsi,
 tanggal_mulai, tanggal_akhir, tanggal_seleksi, tanggal_pengumuman,
 min_ipk, min_semester, allowed_prodi)
VALUES (?,?,?,?,?,?,?,?,?,?,?)";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param(
  $stmt,
  "ssssssssdis",
  $nama,
  $jenis,
  $penyelenggara,
  $deskripsi,
  $tgl_mulai,
  $tgl_akhir,
  $tgl_seleksi,
  $tgl_pengumuman,
  $min_ipk,
  $min_semester,
  $allowed_prodi
);

if (!mysqli_stmt_execute($stmt)) {
  echo json_encode(["status" => "error", "message" => "Gagal menyimpan beasiswa"]);
  exit;
}

$beasiswa_id = mysqli_insert_id($conn);

// INSERT persyaratan
if (!empty($persyaratan) && is_array($persyaratan)) {
  foreach ($persyaratan as $p) {
    $p = trim($p);
    if ($p === '') continue;
    $q = mysqli_prepare($conn, "INSERT INTO beasiswa_persyaratan (beasiswa_id, persyaratan) VALUES (?,?)");
    mysqli_stmt_bind_param($q, "is", $beasiswa_id, $p);
    mysqli_stmt_execute($q);
  }
}

// INSERT berkas
if (!empty($berkas) && is_array($berkas)) {
  foreach ($berkas as $b) {
    $nama_berkas = trim($b['nama'] ?? '');
    if ($nama_berkas === '') continue;

    $tipe_file = $b['tipe'] ?? 'pdf';
    $max_size  = isset($b['max']) && $b['max'] !== null ? intval($b['max']) : 0;
    $wajib     = ($b['wajib'] ?? 'wajib') === 'opsional' ? 'opsional' : 'wajib';

    $q = mysqli_prepare(
      $conn,
      "INSERT INTO beasiswa_berkas (beasiswa_id, nama_berkas, tipe_file, max_size_mb, wajib)
       VALUES (?,?,?,?,?)"
    );
    mysqli_stmt_bind_param($q, "issis", $beasiswa_id, $nama_berkas, $tipe_file, $max_size, $wajib);
    mysqli_stmt_execute($q);
  }
}

echo json_encode([
  "status" => "success",
  "message" => "Beasiswa berhasil disimpan",
  "beasiswa_id" => $beasiswa_id
]);
