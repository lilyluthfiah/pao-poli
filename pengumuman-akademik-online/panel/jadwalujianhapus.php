<?php
$id = $_GET['id'] ?? 0;

if ($id) {
    mysqli_query($koneksi, "DELETE FROM jadwalujian WHERE id='$id'");
}

echo "<script>alert('Data berhasil dihapus'); window.location='index.php?page=jadwalujian';</script>";
exit;
