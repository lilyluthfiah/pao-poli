<?php

$id = $_GET['id'] ?? 0;

if ($id) {
    mysqli_query($koneksi, "DELETE FROM pengumuman WHERE id='$id'");
}

echo "<script>alert('Data berhasil dihapus'); window.location='index.php?page=pengumuman';</script>";
exit;
