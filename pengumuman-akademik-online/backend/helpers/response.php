<?php
declare(strict_types=1);

function jsonInput(): array {
  $raw = file_get_contents("php://input");
  $data = json_decode($raw ?: "{}", true);
  return is_array($data) ? $data : [];
}

function success(array $data = [], int $code = 200): void {
  http_response_code($code);
  header("Content-Type: application/json; charset=utf-8");
  echo json_encode(["success" => true] + $data);
  exit;
}

function error(string $message, int $code = 400): void {
  http_response_code($code);
  header("Content-Type: application/json; charset=utf-8");
  echo json_encode(["success" => false, "message" => $message]);
  exit;
}
