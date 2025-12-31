<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . "/../../config/database.php";
require_once __DIR__ . "/../../helpers/response.php";

if (($_SERVER["REQUEST_METHOD"] ?? "") !== "POST") {
  error("Method not allowed", 405);
}

try {
  // Pastikan koneksi DB benar-benar ada
  if (!isset($conn) || !($conn instanceof mysqli)) {
    error("Koneksi database tidak tersedia. Cek config/database.php", 500);
  }
  if ($conn->connect_errno) {
    error("Gagal terhubung ke database: " . $conn->connect_error, 500);
  }

  $data = jsonInput();

  $username = trim((string)($data["username"] ?? ""));
  $password = (string)($data["password"] ?? "");
  $rolePick = strtolower(trim((string)($data["role"] ?? ""))); // admin / mahasiswa

  if ($username === "" || $password === "" || $rolePick === "") {
    error("Username, password, dan role wajib diisi.", 422);
  }

  $roleExpected = ($rolePick === "mahasiswa") ? "user" : $rolePick;
  if (!in_array($roleExpected, ["admin", "user"], true)) {
    error("Role tidak valid.", 422);
  }

  $sql = "SELECT id, username, password, role, is_active
          FROM users
          WHERE TRIM(LOWER(username)) = TRIM(LOWER(?))
          ORDER BY id DESC
          LIMIT 1";

  $stmt = $conn->prepare($sql);
  if (!$stmt) {
    error("Query gagal diproses: " . $conn->error, 500);
  }

  $stmt->bind_param("s", $username);
  $stmt->execute();

  $result = $stmt->get_result();
  $user = $result ? $result->fetch_assoc() : null;

  if (!$user) error("Username atau password salah", 401);
  if ((int)$user["is_active"] !== 1) error("Akun tidak aktif", 403);

  if (!password_verify($password, (string)$user["password"])) {
    error("Username atau password salah", 401);
  }

  if ((string)$user["role"] !== $roleExpected) {
    error("Role yang dipilih tidak sesuai dengan akun.", 403);
  }

  session_regenerate_id(true);
  $_SESSION["user_id"] = (int)$user["id"];
  $_SESSION["username"] = (string)$user["username"];
  $_SESSION["role"] = (string)$user["role"];

  $roleForFrontend = ((string)$user["role"] === "user") ? "mahasiswa" : "admin";

  success([
    "message" => "Login berhasil",
    "role" => $roleForFrontend
  ]);
} catch (Throwable $e) {
  // Biar kalau SQL/table error keliatan jelas
  error("Server error: " . $e->getMessage(), 500);
}
