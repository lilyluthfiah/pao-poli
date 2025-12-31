<?php
declare(strict_types=1);

require_once __DIR__ . "/../../../config/database.php";
header("Content-Type: application/json; charset=utf-8");

if (session_status() === PHP_SESSION_NONE) session_start();

if (($_SERVER["REQUEST_METHOD"] ?? "") !== "POST") {
  http_response_code(405);
  echo json_encode(["success" => false, "message" => "Method not allowed"]);
  exit;
}

$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

if (!is_array($data)) {
  http_response_code(400);
  echo json_encode(["success" => false, "message" => "Invalid JSON body"]);
  exit;
}

$judul = trim((string)($data["judul"] ?? ""));
$deskripsi = trim((string)($data["deskripsi"] ?? ""));

if ($judul === "" || $deskripsi === "") {
  http_response_code(422);
  echo json_encode(["success" => false, "message" => "Judul dan deskripsi wajib diisi"]);
  exit;
}

try {
  $stmt = $conn->prepare("INSERT INTO pengumuman (judul, deskripsi) VALUES (?, ?)");
  $stmt->bind_param("ss", $judul, $deskripsi);
  $stmt->execute();

  echo json_encode(["success" => true, "id" => $stmt->insert_id]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
