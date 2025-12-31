<?php
require_once __DIR__ . "/../../../config/database.php";
header("Content-Type: application/json; charset=utf-8");
if (session_status() === PHP_SESSION_NONE) session_start();

$role = strtolower((string)($_SESSION["user"]["role"] ?? $_SESSION["role"] ?? ""));
if ($role !== "admin" && $role !== "dosen") {
  http_response_code(401);
  echo json_encode(["success"=>false,"message"=>"Unauthorized"]);
  exit;
}

$data = json_decode(file_get_contents("php://input"), true) ?: [];
$id = (int)($data["id"] ?? 0);
if ($id <= 0) {
  http_response_code(422);
  echo json_encode(["success"=>false,"message"=>"ID tidak valid"]);
  exit;
}

$stmt = $conn->prepare("DELETE FROM pengumuman WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();

echo json_encode(["success"=>true]);
