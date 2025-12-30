<?php
require_once __DIR__ . "/../../helpers/response.php";

if (empty($_SESSION["user_id"])) {
  error("Unauthorized", 401);
}

success([
  "user" => [
    "id" => (int)$_SESSION["user_id"],
    "username" => (string)($_SESSION["username"] ?? ""),
    "role" => (string)($_SESSION["role"] ?? "") // admin / user
  ]
]);
