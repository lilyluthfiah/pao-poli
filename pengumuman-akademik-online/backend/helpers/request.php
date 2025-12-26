<?php
function getJsonInput(): array {
  $raw = file_get_contents("php://input");
  if (!$raw) return [];
  $data = json_decode($raw, true);
  return is_array($data) ? $data : [];
}

function post($key, $default = null) {
  return $_POST[$key] ?? $default;
}
