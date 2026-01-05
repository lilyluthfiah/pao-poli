<?php
$id = $_GET['id'] ?? null;

if (!$id) {
    echo "<script>alert('ID tidak ditemukan'); window.location='index.php?page=mahasiswa';</script>";
    exit;
}

// Hanya Admin yang boleh menghapus mahasiswa
if ($_SESSION['user']['role'] !== 'Admin') {
    echo "<script>alert('Akses ditolak'); window.location='index.php?page=mahasiswa';</script>";
    exit;
}

// Pastikan yang dihapus adalah Mahasiswa
$cek = mysqli_query($koneksi, "
    SELECT id FROM users 
    WHERE id = '$id' AND role = 'Mahasiswa'
");

if (mysqli_num_rows($cek) == 0) {
    echo "<script>alert('Data mahasiswa tidak ditemukan'); window.location='index.php?page=mahasiswa';</script>";
    exit;
}

// Hapus mahasiswa
mysqli_query($koneksi, "
    DELETE FROM users 
    WHERE id = '$id' AND role = 'Mahasiswa'
");

echo "<script>alert('Data mahasiswa berhasil dihapus'); window.location='index.php?page=mahasiswa';</script>";
exit;
