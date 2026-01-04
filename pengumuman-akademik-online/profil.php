<?php
// Ambil data user login
$id_user = $_SESSION['user']['id'];

$data = mysqli_query($koneksi, "
    SELECT * FROM users 
    WHERE id = '$id_user'
");
$user = mysqli_fetch_assoc($data);
?>

<div class="container">
    <div class="page-inner">

        <div class="page-header d-flex justify-content-between align-items-center">
            <h3 class="fw-bold">Edit Profile</h3>

            <a href="index.php?page=dashboard" class="btn btn-secondary">
                <i class="fa fa-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <div class="card mt-3">
            <div class="card-body">

                <form method="POST">

                    <div class="row g-3">

                        <div class="col-12">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control"
                                value="<?= htmlspecialchars($user['nama']); ?>" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">
                                <?= ($user['role'] == 'Admin') ? 'NIDN' : 'NIM'; ?>
                            </label>
                            <input type="text" name="nimataunidn" class="form-control"
                                value="<?= htmlspecialchars($user['nimataunidn']); ?>" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control"
                                value="<?= htmlspecialchars($user['email']); ?>" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">No HP</label>
                            <input type="text" name="nohp" class="form-control"
                                value="<?= htmlspecialchars($user['nohp']); ?>">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="3"><?= htmlspecialchars($user['alamat']); ?></textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Program Studi</label>
                            <input type="text" name="prodi" class="form-control"
                                value="<?= htmlspecialchars($user['prodi']); ?>">
                        </div>

                        <?php if ($user['role'] != 'Admin'): ?>
                            <div class="col-12">
                                <label class="form-label">Kelas</label>
                                <input type="text" name="kelas" class="form-control"
                                    value="<?= htmlspecialchars($user['kelas']); ?>">
                            </div>
                        <?php endif; ?>

                        <div class="col-12">
                            <label class="form-label">Password Baru (Opsional)</label>
                            <input type="password" name="password" class="form-control"
                                placeholder="Kosongkan jika tidak ingin mengubah password">
                        </div>

                    </div>

                    <div class="mt-4">
                        <button type="submit" name="update_profile" class="btn btn-primary">
                            <i class="fa fa-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </div>
</div>
<?php
if (isset($_POST['update_profile'])) {

    $nama         = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $email        = mysqli_real_escape_string($koneksi, $_POST['email']);
    $nimataunidn  = mysqli_real_escape_string($koneksi, $_POST['nimataunidn']);
    $nohp         = mysqli_real_escape_string($koneksi, $_POST['nohp']);
    $alamat       = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $prodi        = mysqli_real_escape_string($koneksi, $_POST['prodi']);
    $kelas        = $_POST['kelas'] ?? null;
    $password     = $_POST['password'];

    // Update tanpa password
    if (empty($password)) {

        $query = "
            UPDATE users SET
                nama = '$nama',
                email = '$email',
                nimataunidn = '$nimataunidn',
                nohp = '$nohp',
                alamat = '$alamat',
                prodi = '$prodi'
        ";

        if ($_SESSION['user']['role'] != 'Admin') {
            $query .= ", kelas = '$kelas'";
        }

        $query .= " WHERE id = '$id_user'";

        mysqli_query($koneksi, $query);
    } else {
        // Update dengan password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $query = "
            UPDATE users SET
                nama = '$nama',
                email = '$email',
                nimataunidn = '$nimataunidn',
                nohp = '$nohp',
                alamat = '$alamat',
                prodi = '$prodi',
                password = '$password_hash'
        ";

        if ($_SESSION['user']['role'] != 'Admin') {
            $query .= ", kelas = '$kelas'";
        }

        $query .= " WHERE id = '$id_user'";

        mysqli_query($koneksi, $query);
    }

    // Update session biar langsung berubah
    $_SESSION['user']['nama'] = $nama;
    $_SESSION['user']['email'] = $email;

    echo "<script>alert('Profil berhasil diperbarui'); window.location='index.php?page=dashboard';</script>";
    exit;
}
?>