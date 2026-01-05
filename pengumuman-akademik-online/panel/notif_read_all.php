<?php
$userId = $_SESSION['user']['id'];

mysqli_query($koneksi, "
    UPDATE notifikasi 
    SET status = 'Dibaca'
    WHERE user_id = '$userId'
");

$back = $_SERVER['HTTP_REFERER'] ?? 'index.php';

echo "<script>
    window.location = '$back';
</script>";
exit;
