<?php
$q_pengumuman = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM pengumuman");
$pengumuman   = mysqli_fetch_assoc($q_pengumuman)['total'];

$q_beasiswa = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM beasiswa");
$beasiswa   = mysqli_fetch_assoc($q_beasiswa)['total'];

$user_id = $_SESSION['user']['id'];
$q_kuliah = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total 
    FROM jadwalkuliah 
    WHERE user_id = '$user_id'
");
$jadwal_kuliah = mysqli_fetch_assoc($q_kuliah)['total'];

$q_ujian = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total 
    FROM jadwalujian 
    WHERE user_id = '$user_id'
");
$jadwal_ujian = mysqli_fetch_assoc($q_ujian)['total'];

$q_pengumuman_terbaru = mysqli_query($koneksi, "
    SELECT id, judul, deskripsi, tanggal
    FROM pengumuman
    ORDER BY id DESC
    LIMIT 5
");

?>

<div class="container">
    <div class="page-inner">

        <!-- HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-1">Dashboard</h3>
                <p class="text-muted mb-0">
                    Selamat datang, <?= htmlspecialchars($_SESSION['user']['nama']); ?>
                </p>
            </div>
        </div>

        <!-- STAT CARDS -->
        <div class="row g-4">

            <!-- PENGUMUMAN -->
            <div class="col-xl-3 col-md-6">
                <div class="card card-stats card-round h-100 shadow-sm">
                    <div class="card-body py-4">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                style="width:60px; height:60px;">
                                <i class="fa fa-bullhorn fa-2x"></i>
                            </div>
                            <div class="ms-3">
                                <p class="card-category mb-1 fw-semibold">Pengumuman</p>
                                <h3 class="fw-bold mb-0"><?= $pengumuman; ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- JADWAL UJIAN -->
            <div class="col-xl-3 col-md-6">
                <div class="card card-stats card-round h-100 shadow-sm">
                    <div class="card-body py-4">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center"
                                style="width:60px; height:60px;">
                                <i class="fa fa-calendar-alt fa-2x"></i>
                            </div>
                            <div class="ms-3">
                                <p class="card-category mb-1 fw-semibold">Jadwal Ujian</p>
                                <h3 class="fw-bold mb-0"><?= $jadwal_ujian; ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- JADWAL KULIAH -->
            <div class="col-xl-3 col-md-6">
                <div class="card card-stats card-round h-100 shadow-sm">
                    <div class="card-body py-4">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-info text-white d-flex align-items-center justify-content-center"
                                style="width:60px; height:60px;">
                                <i class="fa fa-clock fa-2x"></i>
                            </div>
                            <div class="ms-3">
                                <p class="card-category mb-1 fw-semibold">Jadwal Kuliah</p>
                                <h3 class="fw-bold mb-0"><?= $jadwal_kuliah; ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BEASISWA -->
            <div class="col-xl-3 col-md-6">
                <div class="card card-stats card-round h-100 shadow-sm">
                    <div class="card-body py-4">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center"
                                style="width:60px; height:60px;">
                                <i class="fa fa-graduation-cap fa-2x"></i>
                            </div>
                            <div class="ms-3">
                                <p class="card-category mb-1 fw-semibold">Beasiswa</p>
                                <h3 class="fw-bold mb-0"><?= $beasiswa; ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- PENGUMUMAN TERBARU -->
        <div class="row mt-5">
            <div class="col-12">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="fw-bold mb-0">
                        <i class="fa fa-bullhorn me-2 text-primary"></i>
                        Pengumuman Terbaru
                    </h4>
                    <a href="index.php?page=pengumuman" class="btn btn-sm btn-outline-primary">
                        Lihat Semua
                    </a>
                </div>

                <?php if (mysqli_num_rows($q_pengumuman_terbaru) > 0): ?>
                    <?php while ($p = mysqli_fetch_assoc($q_pengumuman_terbaru)): ?>
                        <div class="card mb-3 shadow-sm">
                            <div class="card-body">

                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h5 class="fw-bold mb-1">
                                            <?= htmlspecialchars($p['judul']); ?>
                                        </h5>
                                        <small class="text-muted">
                                            <i class="fa fa-calendar-alt me-1"></i>
                                            <?= date('d M Y', strtotime($p['tanggal'])); ?>
                                        </small>
                                    </div>
                                </div>

                                <hr class="my-2">

                                <p class="mb-3 text-muted">
                                    <?= limit_text(strip_tags($p['deskripsi']), 150); ?>
                                </p>

                                <a href="index.php?page=pengumumandetail&id=<?= $p['id']; ?>"
                                    class="btn btn-sm btn-primary">
                                    <i class="fa fa-eye me-1"></i> Lihat Detail
                                </a>

                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="alert alert-info">
                        Belum ada pengumuman.
                    </div>
                <?php endif; ?>

            </div>
        </div>


    </div>
</div>