<?php
header("Content-Type: application/json");
session_start();
require_once "../../../config/database.php";

try {
    if (empty($_POST['id'])) {
        throw new Exception("ID jadwal tidak ditemukan");
    }

    $id   = $_POST['id'];
    $nama = $_SESSION['nama'] ?? 'admin';

    // ambil data dulu (buat notifikasi)
    $q = $conn->query("SELECT mata_kuliah, tanggal, waktu FROM jadwal_kuliah WHERE id='$id'");
    if ($q->num_rows === 0) {
        throw new Exception("Data tidak ditemukan");
    }
    $row = $q->fetch_assoc();

    // hapus
    $conn->query("DELETE FROM jadwal_kuliah WHERE id='$id'");

    // notif
    $notif = "🗑️ Jadwal dihapus: {$row['mata_kuliah']} ({$row['tanggal']} - {$row['waktu']})";
    $conn->query("
        INSERT INTO notifikasi (isi, dibuat_oleh, created_at)
        VALUES ('$notif','$nama',NOW())
    ");

    echo json_encode([
        "status" => "success",
        "message" => "Jadwal berhasil dihapus"
    ]);
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
