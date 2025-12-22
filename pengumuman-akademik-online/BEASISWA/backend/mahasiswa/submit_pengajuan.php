<?php
require "config.php";

$mahasiswa_id = 1; // nanti dari session

$beasiswa_id = $_POST["pilihBeasiswa"] ?? "";
$rekening    = $_POST["rekening"] ?? "";

if (!$beasiswa_id || !$rekening) {
  echo json_encode([
    "status" => "error",
    "message" => "Data tidak lengkap"
  ]);
  exit;
}

// simpan pengajuan
$conn->query("
  INSERT INTO pengajuan (mahasiswa_id, beasiswa_id, rekening)
  VALUES ($mahasiswa_id, $beasiswa_id, '$rekening')
");

$pengajuan_id = $conn->insert_id;

// upload berkas
$uploadDir = "../upload/";

foreach ($_FILES["berkas"]["name"] as $i => $name) {

  if (!$name) continue;

  $tmp  = $_FILES["berkas"]["tmp_name"][$i];
  $nama = $_POST["nama_berkas"][$i];

  $fileName = time() . "_" . basename($name);
  $path = $uploadDir . $fileName;

  move_uploaded_file($tmp, $path);

  $conn->query("
    INSERT INTO pengajuan_berkas
    (pengajuan_id, nama_berkas, file_path)
    VALUES ($pengajuan_id, '$nama', '$fileName')
  ");
}

echo json_encode([
  "status" => "success"
]);
