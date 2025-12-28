<?php
session_start();
require "../../config/db.php";

if ($user['role'] === 'dosen') {
  header("Location: /admin/page/dashboard-dosen.html");
  exit;
} else {
  header("Location: ../dashboard-mahasiswa.html");
  exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$roleReq  = trim($_POST['role'] ?? ''); // role dari dropdown

if ($username === '' || $password === '' || $roleReq === '') {
  http_response_code(400);
  echo json_encode(["message" => "Username, password, dan role wajib diisi"]);
  exit;
}

// ambil user dari DB
$stmt = $conn->prepare("SELECT id, username, password, role, is_active FROM users WHERE username=? LIMIT 1");
$stmt->bind_param("s", $username);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
  http_response_code(401);
  echo json_encode(["message" => "User tidak ditemukan"]);
  exit;
}

if ((int)$user['is_active'] !== 1) {
  http_response_code(403);
  echo json_encode(["message" => "Akun tidak aktif"]);
  exit;
}

// ✅ validasi role: pilihan user harus sama dengan role di DB
if ($roleReq !== $user['role']) {
  http_response_code(403);
  echo json_encode(["message" => "Role anda salah"]);
  exit;
}

// cek password (hash)
$stored = $user['password'];

// 1) coba verify hash
$ok = password_verify($password, $stored);

// 2) fallback: kalau password di DB masih plain text
if (!$ok && hash_equals($stored, $password)) {
  $ok = true;

  // sekalian upgrade ke hash (biar aman)
  $newHash = password_hash($password, PASSWORD_DEFAULT);
  $up = $conn->prepare("UPDATE users SET password=? WHERE id=?");
  $up->bind_param("si", $newHash, $user['id']);
  $up->execute();
}

if (!$ok) {
  http_response_code(401);
  echo json_encode(["message" => "Password salah"]);
  exit;
}

// simpan session
$_SESSION['user'] = [
  "id" => (int)$user['id'],
  "username" => $user['username'],
  "role" => $user['role'],
];

// balikin JSON lengkap (WAJIB ADA role)
echo json_encode([
  "status" => "success",
  "role" => $user['role'],
  "username" => $user['username']
]);
