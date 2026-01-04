<?php
session_start();
require_once 'koneksi.php';

// Kalau sudah login, lempar ke panel
if (isset($_SESSION['user'])) {
    header("Location: panel");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Register - Pengumuman Akademik Online</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="min-vh-100 d-flex align-items-center justify-content-center"
    style="background-image: url('assets/foto/bg.jpg'); background-size: cover;">

    <div class="card shadow-lg border-0 rounded-4 p-4 p-md-5" style="max-width: 900px; width: 100%;">

        <h4 class="text-center fw-bold text-primary mb-1">Register Akun</h4>
        <p class="text-center text-muted small mb-4">
            Pengumuman Akademik Online
        </p>

        <form method="POST">
            <div class="row">

                <!-- KIRI -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label small">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control form-control-lg" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small">Email</label>
                        <input type="email" name="email" class="form-control form-control-lg" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small">No HP</label>
                        <input type="number" name="nohp" class="form-control form-control-lg">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="3"></textarea>
                    </div>
                </div>

                <!-- KANAN -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label small">Role</label>
                        <select name="role" id="role" class="form-select form-select-lg" required onchange="toggleRole()">
                            <option value="">-- Pilih Role --</option>
                            <option value="Mahasiswa">Mahasiswa</option>
                            <option value="Admin">Admin</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small" id="label_nim">NIM</label>
                        <input type="number" name="nimataunidn" class="form-control form-control-lg" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small">Prodi</label>
                        <input type="text" name="prodi" class="form-control form-control-lg">
                    </div>

                    <div class="mb-3" id="kelasField">
                        <label class="form-label small">Kelas</label>
                        <input type="text" name="kelas" class="form-control form-control-lg" placeholder="Contoh: IF-5A">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small">Password</label>
                        <input type="password" name="password" class="form-control form-control-lg" required>
                    </div>
                </div>

            </div>

            <div class="mt-4">
                <button type="submit" name="register" class="btn btn-primary btn-lg w-100 fw-semibold">
                    Daftar
                </button>
            </div>
        </form>
        <div class="text-center mt-3">
            <span class="small text-muted">Sudah punya akun?</span>
            <a href="login.php" class="fw-semibold text-decoration-none">
                Masuk
            </a>
        </div>


        <?php
        if (isset($_POST['register'])) {

            $nama   = mysqli_real_escape_string($koneksi, $_POST['nama']);
            $email  = mysqli_real_escape_string($koneksi, $_POST['email']);
            $nohp   = mysqli_real_escape_string($koneksi, $_POST['nohp']);
            $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
            $role   = mysqli_real_escape_string($koneksi, $_POST['role']);
            $nimataunidn = mysqli_real_escape_string($koneksi, $_POST['nimataunidn']);
            $prodi  = mysqli_real_escape_string($koneksi, $_POST['prodi']);
            $kelas  = ($role === 'mahasiswa')
                ? mysqli_real_escape_string($koneksi, $_POST['kelas'])
                : null;

            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            // Cek email
            $cek = mysqli_query($koneksi, "SELECT id FROM users WHERE email='$email'");
            if (mysqli_num_rows($cek) > 0) {
                echo "<script>alert('Email sudah terdaftar');</script>";
                return;
            }

            $query = "
                    INSERT INTO users
                    (nama, email, password, nimataunidn, nohp, alamat, prodi, kelas, role)
                    VALUES
                    ('$nama', '$email', '$password', '$nimataunidn', '$nohp', '$alamat', '$prodi', " . ($kelas ? "'$kelas'" : "NULL") . ", '$role')
                ";

            if (mysqli_query($koneksi, $query)) {
                echo "<script>
                alert('Registrasi berhasil');
                window.location='login.php';
              </script>";
            } else {
                echo "<script>alert('Registrasi gagal');</script>";
            }
        }
        ?>

        <script>
            function toggleRole() {
                const role = document.getElementById('role').value;
                const kelas = document.getElementById('kelasField');
                const labelNim = document.getElementById('label_nim');

                if (role === 'Admin') {
                    labelNim.innerText = 'NIDN';
                    kelas.style.display = 'none';
                } else if (role === 'Mahasiswa') {
                    labelNim.innerText = 'NIM';
                    kelas.style.display = 'block';
                } else {
                    kelas.style.display = 'none';
                }
            }
        </script>


    </div>

</body>

</html>