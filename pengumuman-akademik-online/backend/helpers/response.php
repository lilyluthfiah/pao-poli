<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

function jsonInput(): array {
  $raw = file_get_contents("php://input");
  $data = json_decode($raw, true);
  return is_array($data) ? $data : [];
}

function success(array $payload = [], int $code = 200): void {
  http_response_code($code);
  header("Content-Type: application/json; charset=utf-8");
  echo json_encode(array_merge(["success" => true], $payload), JSON_UNESCAPED_UNICODE);
  exit;
}

function error(string $message, int $code = 400, array $extra = []): void {
  http_response_code($code);
  header("Content-Type: application/json; charset=utf-8");
  echo json_encode(array_merge(["success" => false, "message" => $message], $extra), JSON_UNESCAPED_UNICODE);
  exit;
}
