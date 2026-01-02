<?php
header("Content-Type: application/json");
session_start();
require_once "../../../config/database.php";

try {
    $required = ['tanggal','mata_kuliah','ruang','waktu','dosen'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Field $field wajib diisi");
        }
    }

    $tanggal     = $_POST['tanggal'];
    $mataKuliah  = $_POST['mata_kuliah'];
    $ruang       = $_POST['ruang'];
    $waktu       = $_POST['waktu'];
    $dosen       = $_POST['dosen'];
    $nama        = $_SESSION['nama'] ?? 'admin';

    $conn->query("
        INSERT INTO jadwal_kuliah
        (tanggal, mata_kuliah, ruang, waktu, dosen, created_at)
        VALUES
        ('$tanggal','$mataKuliah','$ruang','$waktu','$dosen',NOW())
    ");

    echo json_encode([
        "status" => "success",
        "message" => "Jadwal berhasil disimpan"
    ]);
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
