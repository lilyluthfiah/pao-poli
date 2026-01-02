<?php
declare(strict_types=1);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_beasiswa" ;

$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset("utf8mb4");

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
