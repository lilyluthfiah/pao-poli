<?php
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../helpers/response.php";

if (($_SERVER["REQUEST_METHOD"] ?? "") !== "POST") {
  error("Method not allowed", 405);
}

$data = jsonInput();

$username = trim((string)($data["username"] ?? ""));
$password = (string)($data["password"] ?? "");
$rolePick = strtolower(trim((string)($data["role"] ?? ""))); // admin / mahasiswa

if ($username === "" || $password === "" || $rolePick === "") {
  error("Username, password, dan role wajib diisi.", 422);
}

if (mb_strlen($username) < 3) {
  error("Username minimal 3 karakter.", 422);
}

if (mb_strlen($password) < 6) {
  error("Password minimal 6 karakter.", 422);
}

// mapping role dropdown -> role database
$roleDb = ($rolePick === "mahasiswa") ? "user" : $rolePick;

if (!in_array($roleDb, ["admin", "user"], true)) {
  error("Role tidak valid.", 422);
}

// cek username sudah ada (case-insensitive)
$check = $conn->prepare("SELECT id FROM users WHERE TRIM(LOWER(username)) = TRIM(LOWER(?)) LIMIT 1");
$check->bind_param("s", $username);
$check->execute();
$exists = $check->get_result()->fetch_assoc();

if ($exists) {
  error("Username sudah terdaftar. Silakan pakai username lain.", 409);
}

// hash password
$hash = password_hash($password, PASSWORD_BCRYPT);
if (!$hash) {
  error("Gagal memproses password.", 500);
}

// insert user
$stmt = $conn->prepare("INSERT INTO users (username, password, role, is_active) VALUES (?, ?, ?, 1)");
$stmt->bind_param("sss", $username, $hash, $roleDb);
$stmt->execute();

success([
  "message" => "Registrasi berhasil. Silakan login.",
  "role" => ($roleDb === "user") ? "mahasiswa" : "admin"
], 201);
