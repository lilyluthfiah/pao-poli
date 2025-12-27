<?php
$conn = mysqli_connect("localhost", "root", "", "db_login");
if (!$conn) {
  die("Koneksi database gagal: " . mysqli_connect_error());
}