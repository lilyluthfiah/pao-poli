<?php

$id = $_GET['id'] ?? 0;

$data = mysqli_query($koneksi, "SELECT * FROM pengumuman WHERE id='$id'");
$row  = mysqli_fetch_assoc($data);

if (!$row) {
    echo "<script>alert('Data tidak ditemukan'); window.location='index.php?page=pengumuman';</script>";
    exit;
}

?>

<div class="container">
    <div class="page-inner">

        <div class="page-header mb-4">
            <h3 class="fw-bold">Edit Pengumuman</h3>
        </div>

        <div class="card">
            <div class="card-body">

                <form method="POST">
                    <div class="mb-3">
                        <label class="fw-bold">Judul</label>
                        <input type="text" name="judul" class="form-control"
                            value="<?= htmlspecialchars($row['judul']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Deskripsi</label>
                        <textarea name="deskripsi" id="deskripsi" class="form-control"
                            rows="5" required><?= htmlspecialchars($row['deskripsi']); ?></textarea>
                        <script>
                            CKEDITOR.replace('deskripsi');
                        </script>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control"
                            value="<?= $row['tanggal']; ?>" required>
                    </div>

                    <div class="d-flex gap-2">
                        <button class="btn btn-primary" name="update_pengumuman">
                            <i class="fa fa-save me-1"></i> Update
                        </button>
                        <a href="index.php?page=pengumuman" class="btn btn-secondary">
                            Kembali
                        </a>
                    </div>
                </form>

            </div>
        </div>

    </div>
</div>

<?php
if (isset($_POST['update_pengumuman'])) {
    $judul     = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $tanggal   = $_POST['tanggal'];

    mysqli_query($koneksi, "
        UPDATE pengumuman SET
            judul='$judul',
            deskripsi='$deskripsi',
            tanggal='$tanggal'
        WHERE id='$id'
    ");

    echo "<script>alert('Data berhasil diupdate'); window.location='index.php?page=pengumuman';</script>";
    exit;
}
?>