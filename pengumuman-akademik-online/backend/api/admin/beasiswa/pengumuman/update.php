<?php
require_once __DIR__ . "/../../../../config/database.php";
require_once __DIR__ . "/../../../../helpers/response.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  error("Method not allowed", 405);
}

$data = jsonInput();

$id = (int)($data["id"] ?? 0);
$judul = trim($data["judul"] ?? "");
$deskripsi = trim($data["deskripsi"] ?? "");

if ($id <= 0) error("ID tidak valid.", 422);
if ($judul === "" || $deskripsi === "") {
  error("Judul dan deskripsi wajib diisi.", 422);
}

try {
  $stmt = $conn->prepare(
    "UPDATE pengumuman
     SET judul = ?, deskripsi = ?
     WHERE id = ?"
  );
  $stmt->bind_param("ssi", $judul, $deskripsi, $id);
  $stmt->execute();

  success([], "Pengumuman berhasil diperbarui");
} catch (Throwable $e) {
  error("Gagal update pengumuman: " . $e->getMessage(), 500);
}
