<?php
require "config.php";

$id = $_POST["id"] ?? 0;

if ($id) {
  $conn->query("DELETE FROM pengajuan WHERE id = $id");
}

echo json_encode(["status" => "success"]);
