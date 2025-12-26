<?php
require_once __DIR__ . "/../helpers/response.php";

if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION["user"])) {
  jsonResponse(["status" => "error", "message" => "Unauthorized. Silakan login."], 401);
}
