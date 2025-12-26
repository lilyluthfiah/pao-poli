<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = "localhost";
$user = "root";
$pass = "";
$db   = "pengumuman_akademik";

try {
  $conn = new mysqli($host, $user, $pass, $db);
  $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
  http_response_code(500);
  header("Content-Type: application/json; charset=utf-8");
  echo json_encode(["status" => "error", "message" => "Gagal koneksi database."]);
  exit;
}
