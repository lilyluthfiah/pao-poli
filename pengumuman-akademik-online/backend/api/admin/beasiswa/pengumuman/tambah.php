<?php
declare(strict_types=1);

require_once __DIR__ . "/../../../../config/database.php";
header("Content-Type: application/json; charset=utf-8");

function out(array $payload, int $code = 200): void {
  http_response_code($code);
  echo json_encode($payload);
  exit;
}

if (($_SERVER["REQUEST_METHOD"] ?? "") !== "POST") {
  out(["success" => false, "message" => "Method not allowed"], 405);
}

try {
  $data = json_decode(file_get_contents("php://input"), true);

  if (!is_array($data)) {
    out(["success" => false, "message" => "Body bukan JSON valid"], 400);
  }

  $judul     = trim((string)($data["judul"] ?? ""));
  $deskripsi = trim((string)($data["deskripsi"] ?? ""));

  if ($judul === "" || $deskripsi === "") {
    out(["success" => false, "message" => "Judul dan deskripsi wajib diisi"], 422);
  }

  $sql = "INSERT INTO pengumuman (judul, deskripsi, created_at)
          VALUES (?, ?, NOW())";

  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ss", $judul, $deskripsi);
  $stmt->execute();

  out([
    "success" => true,
    "message" => "Pengumuman berhasil disimpan",
    "id" => $conn->insert_id
  ]);

} catch (Throwable $e) {
  out([
    "success" => false,
    "message" => "Server error",
    "error" => $e->getMessage()
  ], 500);
}
