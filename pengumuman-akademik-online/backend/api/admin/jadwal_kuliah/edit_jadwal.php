<?php
header("Content-Type: application/json");
session_start();
require_once "../../../config/database.php";

try {
    $required = ['id','tanggal','mata_kuliah','ruang','waktu','dosen'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Field $field wajib diisi");
        }
    }

    $id         = $_POST['id'];
    $tanggal    = $_POST['tanggal'];
    $mataKuliah = $_POST['mata_kuliah'];
    $ruang      = $_POST['ruang'];
    $waktu      = $_POST['waktu'];
    $dosen      = $_POST['dosen'];
    $nama       = $_SESSION['nama'] ?? 'admin';

    // update jadwal
    $conn->query("
        UPDATE jadwal_kuliah SET
            tanggal='$tanggal',
            mata_kuliah='$mataKuliah',
            ruang='$ruang',
            waktu='$waktu',
            dosen='$dosen'
        WHERE id='$id'
    ");

    // notif
    $notif = "✏️ Jadwal diperbarui: $mataKuliah ($tanggal - $waktu)";
    $conn->query("
        INSERT INTO notifikasi (isi, dibuat_oleh, created_at)
        VALUES ('$notif','$nama',NOW())
    ");

    echo json_encode([
        "status" => "success",
        "message" => "Jadwal berhasil diperbarui"
    ]);
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
