<?php
$id = $_GET['id'] ?? null;

if (!$id) {
    echo "<script>alert('ID tidak ditemukan'); window.location='index.php?page=mahasiswa';</script>";
    exit;
}

$data = mysqli_query($koneksi, "
    SELECT * FROM users 
    WHERE id = '$id' AND role = 'Mahasiswa'
");

$row = mysqli_fetch_assoc($data);

if (!$row) {
    echo "<script>alert('Data mahasiswa tidak ditemukan'); window.location='index.php?page=mahasiswa';</script>";
    exit;
}
?>

<div class="container">
    <div class="page-inner">

        <div class="page-header mb-4">
            <h3 class="fw-bold">Edit Data Mahasiswa</h3>
        </div>

        <div class="card">
            <div class="card-body">

                <form method="POST">

                    <div class="mb-3">
                        <label class="fw-bold">Nama</label>
                        <input type="text" name="nama" class="form-control"
                            value="<?= htmlspecialchars($row['nama']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">NIM</label>
                        <input type="text" name="nimataunidn" class="form-control"
                            value="<?= htmlspecialchars($row['nimataunidn']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Email</label>
                        <input type="email" name="email" class="form-control"
                            value="<?= htmlspecialchars($row['email']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">No HP</label>
                        <input type="text" name="nohp" class="form-control"
                            value="<?= htmlspecialchars($row['nohp']); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Alamat</label>
                        <textarea name="alamat" class="form-control"><?= htmlspecialchars($row['alamat']); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Prodi</label>
                        <input type="text" name="prodi" class="form-control"
                            value="<?= htmlspecialchars($row['prodi']); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Kelas</label>
                        <input type="text" name="kelas" class="form-control"
                            value="<?= htmlspecialchars($row['kelas']); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Password (opsional)</label>
                        <input type="password" name="password" class="form-control"
                            placeholder="Kosongkan jika tidak diubah">
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="index.php?page=mahasiswa" class="btn btn-secondary">
                            Kembali
                        </a>

                        <button type="submit" name="update_mahasiswa" class="btn btn-primary">
                            Simpan Perubahan
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </div>
</div>

<?php
if (isset($_POST['update_mahasiswa'])) {

    $nama   = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $nim    = mysqli_real_escape_string($koneksi, $_POST['nimataunidn']);
    $email  = mysqli_real_escape_string($koneksi, $_POST['email']);
    $nohp   = mysqli_real_escape_string($koneksi, $_POST['nohp']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $prodi  = mysqli_real_escape_string($koneksi, $_POST['prodi']);
    $kelas  = mysqli_real_escape_string($koneksi, $_POST['kelas']);

    // Jika password diisi
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        mysqli_query($koneksi, "
            UPDATE users SET
                nama = '$nama',
                nimataunidn = '$nim',
                email = '$email',
                nohp = '$nohp',
                alamat = '$alamat',
                prodi = '$prodi',
                kelas = '$kelas',
                password = '$password'
            WHERE id = '$id'
        ");
    } else {
        mysqli_query($koneksi, "
            UPDATE users SET
                nama = '$nama',
                nimataunidn = '$nim',
                email = '$email',
                nohp = '$nohp',
                alamat = '$alamat',
                prodi = '$prodi',
                kelas = '$kelas'
            WHERE id = '$id'
        ");
    }

    echo "<script>alert('Data mahasiswa berhasil diperbarui'); window.location='index.php?page=mahasiswa';</script>";
    exit;
}
?>