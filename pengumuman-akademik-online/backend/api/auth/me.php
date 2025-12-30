<?php
session_start();
require_once __DIR__ . "/../../helpers/response.php";

if (empty($_SESSION["user_id"])) {
  error("Unauthorized", 401);
}

$roleRaw = (string)($_SESSION["role"] ?? ""); // admin / user
$roleForFrontend = ($roleRaw === "user") ? "mahasiswa" : $roleRaw;

success([
  "loggedIn" => true,
  "user" => [
    "id" => (int)$_SESSION["user_id"],
    "username" => (string)($_SESSION["username"] ?? ""),
    "role" => $roleForFrontend
  ]
]);
