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

$data = json_decode(file_get_contents("php://input"), true) ?? [];
$id = (int)($data["id"] ?? 0);

if ($id <= 0) out(["success" => false, "message" => "ID tidak valid"], 422);

$stmt = $conn->prepare("DELETE FROM pengumuman WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

out(["success" => true, "message" => "Berhasil dihapus"]);
