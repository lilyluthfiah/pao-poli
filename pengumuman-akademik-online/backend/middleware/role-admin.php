<?php
require_once __DIR__ . "/../helpers/response.php";

if (session_status() === PHP_SESSION_NONE) session_start();

if (($_SESSION["user"]["role"] ?? "") !== "admin") {
  jsonResponse(["status" => "error", "message" => "Forbidden. Hanya admin yang dapat mengakses."], 403);
}
