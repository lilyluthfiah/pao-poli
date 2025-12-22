<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_beasiswa";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
  http_response_code(500);
  die("Koneksi gagal: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
