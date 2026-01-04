<?php
$id = $_GET['id'] ?? null;

if (!$id) {
    echo "<script>alert('ID tidak ditemukan'); window.location='index.php?page=jadwalkuliah';</script>";
    exit;
}

$data = mysqli_query($koneksi, "
    SELECT * FROM jadwalkuliah 
    WHERE id = '$id'
");

$row = mysqli_fetch_assoc($data);

if (!$row) {
    echo "<script>alert('Data tidak ditemukan'); window.location='index.php?page=jadwalkuliah';</script>";
    exit;
}
?>

<div class="container">
    <div class="page-inner">

        <div class="page-header mb-4">
            <h3 class="fw-bold">Edit Jadwal Kuliah</h3>
        </div>

        <div class="card">
            <div class="card-body">

                <form method="POST">

                    <div class="mb-3">
                        <label class="fw-bold">Mata Kuliah</label>
                        <input type="text" name="matakuliah" class="form-control"
                            value="<?= htmlspecialchars($row['matakuliah']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Waktu</label>
                        <input type="text" name="waktu" class="form-control"
                            value="<?= htmlspecialchars($row['waktu']); ?>"
                            placeholder="Contoh: Senin 08:00 - 10:00" required>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Ruang</label>
                        <input type="text" name="ruang" class="form-control"
                            value="<?= htmlspecialchars($row['ruang']); ?>" required>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="index.php?page=jadwalkuliah" class="btn btn-secondary">
                            Kembali
                        </a>

                        <button type="submit" name="update_jadwalkuliah" class="btn btn-primary">
                            Simpan Perubahan
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </div>
</div>

<?php
if (isset($_POST['update_jadwalkuliah'])) {

    $matakuliah = mysqli_real_escape_string($koneksi, $_POST['matakuliah']);
    $waktu      = mysqli_real_escape_string($koneksi, $_POST['waktu']);
    $ruang      = mysqli_real_escape_string($koneksi, $_POST['ruang']);

    mysqli_query($koneksi, "
        UPDATE jadwalkuliah SET
            matakuliah = '$matakuliah',
            waktu      = '$waktu',
            ruang      = '$ruang'
        WHERE id = '$id'
    ");

    echo "<script>alert('Data berhasil diupdate'); window.location='index.php?page=jadwalkuliah';</script>";
    exit;
}
?>