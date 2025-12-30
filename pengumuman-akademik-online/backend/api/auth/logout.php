<?php
require_once __DIR__ . "/../../helpers/response.php";
header("Content-Type: application/json; charset=utf-8");

session_destroy();
success(["message" => "Logout berhasil"]);
