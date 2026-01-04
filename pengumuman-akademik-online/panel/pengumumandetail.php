<?php
$id = $_GET['id'] ?? null;

if (!$id) {
    echo "<script>alert('ID tidak ditemukan'); history.back();</script>";
    exit;
}

$data = mysqli_query($koneksi, "
    SELECT * FROM pengumuman 
    WHERE id = '$id'
");

$row = mysqli_fetch_assoc($data);

if (!$row) {
    echo "<script>alert('Data tidak ditemukan'); history.back();</script>";
    exit;
}
?>

<div class="container">
    <div class="page-inner">

        <!-- HEADER -->
        <div class="page-header d-flex justify-content-between align-items-center">
            <h3 class="fw-bold">Detail Pengumuman</h3>
            <a href="index.php?page=pengumuman" class="btn btn-secondary">
                <i class="fa fa-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <!-- CARD DETAIL -->
        <div class="card mt-4">
            <div class="card-body">

                <h4 class="fw-bold mb-2">
                    <?= htmlspecialchars($row['judul']); ?>
                </h4>

                <div class="text-muted mb-3">
                    <i class="fa fa-calendar"></i>
                    <?= date('d M Y', strtotime($row['tanggal'])); ?>
                </div>

                <hr>

                <div class="pengumuman-content">
                    <?= $row['deskripsi']; ?>
                </div>

            </div>

            <?php if ($_SESSION['user']['role'] == 'Admin'): ?>
                <div class="card-footer d-flex justify-content-end gap-2">
                    <a href="index.php?page=pengumumanedit&id=<?= $row['id']; ?>"
                        class="btn btn-warning">
                        <i class="fa fa-edit me-1"></i> Edit
                    </a>

                    <a href="index.php?page=pengumumanhapus&id=<?= $row['id']; ?>"
                        class="btn btn-danger"
                        onclick="return confirm('Yakin ingin menghapus pengumuman ini?')">
                        <i class="fa fa-trash me-1"></i> Hapus
                    </a>
                </div>
            <?php endif; ?>

        </div>

    </div>
</div>