<?php
session_start();
require "koneksi.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode(["ok" => false, "msg" => "Method not allowed"]);
  exit;
}

$nama = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');
$role = $_POST['role'] ?? '';

$stmt = mysqli_prepare($conn, "SELECT nama, password, role FROM users WHERE nama=? AND role=? LIMIT 1");
mysqli_stmt_bind_param($stmt, "ss", $nama, $role);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result && mysqli_num_rows($result) === 1) {
  $user = mysqli_fetch_assoc($result);

  // password plaintext (sesuai tabel kamu sekarang)
  if ($password === $user['password']) {
    $_SESSION['login'] = true;
    $_SESSION['nama'] = $user['nama'];
    $_SESSION['role'] = $user['role'];

    if ($user['role'] === "dosen") {
      header("Location: dashboard-dosen.php");
    } else {
      header("Location: dashboard-mahasiswa.php");
    }
    exit;
  }
}

header("Location: ../login.html?error=1");
exit;