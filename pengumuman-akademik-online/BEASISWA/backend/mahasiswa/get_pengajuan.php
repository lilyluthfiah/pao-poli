<?php
require "config.php";

$mahasiswa_id = 1;

$sql = "
SELECT
  p.id,
  DATE_FORMAT(p.created_at, '%d-%m-%Y %H:%i') AS created_at,
  b.nama AS nama_beasiswa,
  b.jenis,
  p.rekening,
  p.status,
  COUNT(pb.id) AS jumlah_berkas
FROM pengajuan p
JOIN beasiswa b ON b.id = p.beasiswa_id
LEFT JOIN pengajuan_berkas pb ON pb.pengajuan_id = p.id
WHERE p.mahasiswa_id = $mahasiswa_id
GROUP BY p.id
ORDER BY p.created_at DESC
";

$res = $conn->query($sql);

$data = [];
while ($r = $res->fetch_assoc()) {
  $data[] = $r;
}

echo json_encode($data);
