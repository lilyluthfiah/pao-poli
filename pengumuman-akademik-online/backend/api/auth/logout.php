<?php
declare(strict_types=1);

require_once __DIR__ . "/../../helpers/response.php";
header("Content-Type: application/json; charset=utf-8");

session_set_cookie_params([
  "path" => "/pao-poli/pengumuman-akademik-online/",
  "httponly" => true,
  "samesite" => "Lax",
]);

session_start();
session_unset();
session_destroy();

success(["message" => "Logout berhasil"]);
