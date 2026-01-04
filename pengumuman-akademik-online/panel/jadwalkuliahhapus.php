<?php
$id = $_GET['id'] ?? null;

if (!$id) {
    echo "<script>alert('ID tidak ditemukan'); window.location='index.php?page=jadwalkuliah';</script>";
    exit;
}

if ($_SESSION['user']['role'] == 'Admin') {
    $user_id = $_SESSION['user']['id'];

    $cek = mysqli_query($koneksi, "
        SELECT id FROM jadwalkuliah 
        WHERE id = '$id' AND user_id = '$user_id'
    ");

    if (mysqli_num_rows($cek) == 0) {
        echo "<script>alert('Akses ditolak'); window.location='index.php?page=jadwalkuliah';</script>";
        exit;
    }
}

mysqli_query($koneksi, "
    DELETE FROM jadwalkuliah 
    WHERE id = '$id'
");

echo "<script>alert('Data berhasil dihapus'); window.location='index.php?page=jadwalkuliah';</script>";
exit;
