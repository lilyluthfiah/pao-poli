<?php
$id = $_GET['id'];

$data = mysqli_query($koneksi, "SELECT * FROM beasiswa WHERE id = '$id'");
$row = mysqli_fetch_assoc($data);

if (!$row) {
    echo "<script>alert('Data tidak ditemukan'); window.location='index.php?page=beasiswa';</script>";
    exit;
}

// Fetch requirements
$qSyarat = mysqli_query($koneksi, "SELECT * FROM beasiswa_syarat WHERE beasiswa_id = '$id' ORDER BY tahap DESC, id ASC");
$syarat_pendaftaran = [];
$syarat_lulus = [];
while ($s = mysqli_fetch_assoc($qSyarat)) {
    if ($s['tahap'] == 'Pendaftaran') {
        $syarat_pendaftaran[] = $s['nama_syarat'];
    } else {
        $syarat_lulus[] = $s['nama_syarat'];
    }
}
?>

<div class="container">
    <div class="page-inner">

        <div class="page-header d-flex justify-content-between align-items-center">
            <h3 class="fw-bold">Detail Beasiswa</h3>
            <a href="index.php?page=beasiswa" class="btn btn-secondary btn-sm">Kembali</a>
        </div>

        <div class="card mt-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h2 class="fw-bold text-primary"><?= htmlspecialchars($row['nama']); ?></h2>
                        <p class="text-muted">
                            <i class="fa fa-calendar me-1"></i> Periode:
                            <?= date('d M Y', strtotime($row['tanggal'])); ?> s/d <?= date('d M Y', strtotime($row['tanggal_akhir'])); ?>
                        </p>

                        <div class="mb-4">
                            <h5 class="fw-bold border-bottom pb-2">Deskripsi</h5>
                            <div class="text-justify">
                                <?= $row['deskripsi']; ?>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h5 class="fw-bold border-bottom pb-2">Persyaratan Umum</h5>
                            <p><?= nl2br(htmlspecialchars($row['syarat'])); ?></p>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5 class="fw-bold mb-3">Dokumen Persyaratan</h5>

                                <h6 class="fw-bold text-primary mt-3">Saat Pendaftaran:</h6>
                                <?php if (!empty($syarat_pendaftaran)): ?>
                                    <ul class="list-group list-group-flush mb-3">
                                        <?php foreach ($syarat_pendaftaran as $sp): ?>
                                            <li class="list-group-item bg-light border-0 ps-0">
                                                <i class="fa fa-check-circle text-success me-2"></i> <?= $sp; ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <p class="text-muted small mb-3">Tidak ada dokumen khusus.</p>
                                <?php endif; ?>

                                <h6 class="fw-bold text-info mt-3">Saat Lulus (Kelengkapan):</h6>
                                <?php if (!empty($syarat_lulus)): ?>
                                    <ul class="list-group list-group-flush">
                                        <?php foreach ($syarat_lulus as $sl): ?>
                                            <li class="list-group-item bg-light border-0 ps-0">
                                                <i class="fa fa-check-circle text-info me-2"></i> <?= $sl; ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <p class="text-muted small">Tidak ada dokumen kelengkapan.</p>
                                <?php endif; ?>

                                <?php if (!empty($row['file_beasiswa'])): ?>
                                    <hr>
                                    <h6 class="fw-bold">Panduan Beasiswa:</h6>
                                    <a href="../uploads/<?= $row['file_beasiswa']; ?>" target="_blank" class="btn btn-outline-primary btn-sm w-100">
                                        <i class="fa fa-download me-1"></i> Download Panduan PDF
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if ($_SESSION['user']['role'] == 'Mahasiswa'): ?>
                            <div class="d-grid mt-3">
                                <a href="index.php?page=pendaftaran&id=<?= $id; ?>" class="btn btn-primary btn-lg fw-bold">
                                    Daftar Sekarang
                                </a>
                            </div>
                        <?php endif; ?>

                        <?php if ($_SESSION['user']['role'] == 'Admin'): // Admin/Admin controls 
                        ?>
                            <div class="d-grid mt-3 gap-2">
                                <a href="index.php?page=beasiswaedit&id=<?= $id; ?>" class="btn btn-warning fw-bold">Edit Beasiswa</a>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>