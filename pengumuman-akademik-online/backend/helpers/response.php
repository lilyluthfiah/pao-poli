<?php
// backend/helpers/response.php
header("Content-Type: application/json; charset=utf-8");

function json_ok($data = null, string $message = "OK", int $code = 200): void {
  http_response_code($code);
  echo json_encode([
    "status"  => "success",
    "message" => $message,
    "data"    => $data
  ], JSON_UNESCAPED_UNICODE);
  exit;
}

function json_fail(string $message = "Error", int $code = 400, $data = null): void {
  http_response_code($code);
  echo json_encode([
    "status"  => "error",
    "message" => $message,
    "data"    => $data
  ], JSON_UNESCAPED_UNICODE);
  exit;
}

function read_json_body(): array {
  $raw = file_get_contents("php://input");
  $data = json_decode($raw, true);
  return is_array($data) ? $data : [];
}

function jsonResponse($data, int $code = 200) {
  http_response_code($code);
  header("Content-Type: application/json; charset=utf-8");
  echo json_encode($data, JSON_UNESCAPED_UNICODE);
  exit;
}
