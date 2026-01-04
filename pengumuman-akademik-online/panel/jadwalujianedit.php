<?php
$id = $_GET['id'] ?? 0;

$query = mysqli_query($koneksi, "SELECT * FROM jadwalujian WHERE id='$id'");
$data  = mysqli_fetch_assoc($query);

if (!$data) {
    echo "<script>alert('Data tidak ditemukan'); window.location='index.php?page=jadwalujian';</script>";
    exit;
}

?>

<div class="container">
    <div class="page-inner">

        <div class="page-header mb-3">
            <h3 class="fw-bold">Edit Jadwal Ujian</h3>
        </div>

        <div class="card">
            <div class="card-body">

                <form method="POST">

                    <div class="mb-3">
                        <label class="fw-bold">Mata Kuliah</label>
                        <input type="text" name="matakuliah" class="form-control"
                            value="<?= $data['matakuliah']; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control"
                            value="<?= $data['tanggal']; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Waktu</label>
                        <input type="text" name="waktu" class="form-control"
                            value="<?= $data['waktu']; ?>" placeholder="08:00-10:00" required>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Ruang</label>
                        <input type="text" name="ruang" class="form-control"
                            value="<?= $data['ruang']; ?>" required>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" name="update_jadwalujian" class="btn btn-primary">
                            Update
                        </button>
                        <a href="index.php?page=jadwalujian" class="btn btn-secondary">
                            Kembali
                        </a>
                    </div>

                </form>

            </div>
        </div>

    </div>
</div>
<?php

if (isset($_POST['update_jadwalujian'])) {

    $matakuliah = mysqli_real_escape_string($koneksi, $_POST['matakuliah']);
    $tanggal    = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
    $waktu      = mysqli_real_escape_string($koneksi, $_POST['waktu']);
    $ruang      = mysqli_real_escape_string($koneksi, $_POST['ruang']);

    mysqli_query($koneksi, "
        UPDATE jadwalujian SET
            matakuliah = '$matakuliah',
            tanggal    = '$tanggal',
            waktu      = '$waktu',
            ruang      = '$ruang'
        WHERE id = '$id'
    ");

    echo "<script>alert('Data berhasil diperbarui'); window.location='index.php?page=jadwalujian';</script>";
    exit;
}
?>