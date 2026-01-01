<?php
header("Content-Type: application/json");
require_once "../../../config/database.php";

try {
    $result = $conn->query("
        SELECT id, tanggal, mata_kuliah, ruang, waktu, dosen
        FROM jadwal_kuliah
        ORDER BY tanggal ASC
    ");

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode([
        "status" => "success",
        "data" => $data
    ]);
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Gagal mengambil jadwal"
    ]);
}
