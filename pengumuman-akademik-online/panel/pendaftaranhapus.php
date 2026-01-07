<?php
if (!isset($_GET['id'])) {
    echo "<script>alert('ID tidak ditemukan'); window.history.back();</script>";
    exit;
}

$id = $_GET['id'];

$data = mysqli_query($koneksi, "
    SELECT * FROM pendaftaran 
    WHERE id = '$id'
");

if (mysqli_num_rows($data) == 0) {
    echo "<script>alert('Data tidak ditemukan'); window.history.back();</script>";
    exit;
}

$row = mysqli_fetch_assoc($data);
$beasiswa_id = $row['beasiswa_id'];
$file = $row['file'];

$path = "../uploads/" . $file;
if (file_exists($path)) {
    unlink($path);
}

mysqli_query($koneksi, "
    DELETE FROM pendaftaran 
    WHERE id = '$id'
");

echo "<script>
    alert('Data pendaftaran berhasil dihapus');
    window.location = 'index.php?page=pendaftaran&id=$beasiswa_id';
</script>";
exit;
