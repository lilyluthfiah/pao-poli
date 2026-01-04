<?php
session_start();

require_once 'koneksi.php';

if (isset($_SESSION['user'])) {
    header("Location: panel");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Login - Pengumuman Akademik Online</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="assets/foto/logo1.png" type="image/x-icon" />

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="min-vh-100 d-flex align-items-center justify-content-center"
    style="background-image: url('assets/foto/bg.jpg'); background-size: cover;">

    <div class="card shadow-lg border-0 rounded-4 p-4" style="max-width: 400px; width: 100%;">

        <!-- Icon -->
        <!-- <div class="text-center mb-3 d-flex justify-content-center align-items-center gap-3">
            <img src="assets/foto/logo1.png" alt="Logo 1" height="70">
            <img src="assets/foto/logo2.png" alt="Logo 2" height="70">
        </div> -->


        <!-- Title -->
        <h4 class="text-center fw-bold text-primary mb-0">Pengumuman Akademik Online</h4>
        <p class="text-center text-muted small mb-4">
            Sistem Pengumuman Akademik Online
        </p>


        <!-- Form -->
        <form method="POST">
            <div class="mb-3">
                <label class="form-label small">Email</label>
                <input type="email" name="email" class="form-control form-control-lg" placeholder="Masukkan Email">
            </div>

            <div class="mb-4">
                <label class="form-label small">Kata Sandi</label>
                <input type="password" name="password" class="form-control form-control-lg" placeholder="Masukkan Kata Sandi">
            </div>

            <button type="submit" name="login" class="btn btn-primary btn-lg w-100 fw-semibold">
                Masuk
            </button>
        </form>
        <!-- <div class="text-center mt-3">
            <span class="small text-muted">Belum punya akun?</span>
            <a href="register.php" class="fw-semibold text-decoration-none">
                Daftar
            </a>
        </div> -->


        <?php

        if (isset($_POST['login'])) {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $user = mysqli_query($koneksi, "SELECT * FROM users WHERE email='$email'");
            $cek = mysqli_num_rows($user);

            if ($cek > 0) {
                $row = mysqli_fetch_assoc($user);

                if (password_verify($password, $row['password'])) {
                    $_SESSION['user'] = $row;
                    echo "<script>alert('Login Berhasil'); window.location='panel/index.php';</script>";
                } else {
                    echo "<script>alert('Email atau Password salah'); window.location='login.php';</script>";
                }
            } else {
                echo "<script>alert('Email atau Password salah'); window.location='login.php';</script>";
            }
        }
        ?>

    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>

</html>