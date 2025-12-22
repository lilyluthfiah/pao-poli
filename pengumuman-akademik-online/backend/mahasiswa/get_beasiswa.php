<?php
require "../config.php";
header("Content-Type: application/json; charset=UTF-8");

$data = [];

$q = $conn->query("SELECT id, nama, jenis FROM beasiswa WHERE status_aktif = 1 ORDER BY nama ASC");
if (!$q) {
  http_response_code(500);
  echo json_encode(["error" => "Query beasiswa gagal", "detail" => $conn->error], JSON_UNESCAPED_UNICODE);
  exit;
}

while ($b = $q->fetch_assoc()) {
  $berkas = [];

  $qb = $conn->prepare("
    SELECT id, nama_berkas, tipe_file, wajib, max_size_mb
    FROM beasiswa_berkas
    WHERE beasiswa_id = ?
    ORDER BY id ASC
  ");
  $qb->bind_param("i", $b["id"]);
  $qb->execute();
  $resB = $qb->get_result();

  while ($bk = $resB->fetch_assoc()) {
    $berkas[] = [
      "id" => (int)$bk["id"],
      "nama" => $bk["nama_berkas"],
      "tipe" => $bk["tipe_file"],
      "wajib" => $bk["wajib"],
      "max" => (int)$bk["max_size_mb"],
    ];
  }

  $data[] = [
    "id" => (int)$b["id"],
    "nama" => $b["nama"],
    "jenis" => $b["jenis"],
    "berkas" => $berkas
  ];
}

echo json_encode($data, JSON_UNESCAPED_UNICODE);
