    <?php
    $id = $_GET['id'];

    $data = mysqli_query($koneksi, "
        SELECT id FROM beasiswa 
        WHERE id = '$id'
    ");

    if (mysqli_num_rows($data) == 0) {
        echo "<script>alert('Data tidak ditemukan'); window.location='index.php?page=beasiswa';</script>";
        exit;
    }

    mysqli_query($koneksi, "
        DELETE FROM beasiswa 
        WHERE id = '$id'
    ");

    echo "<script>alert('Data berhasil dihapus'); window.location='index.php?page=beasiswa';</script>";
    exit;
