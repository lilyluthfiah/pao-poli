<?php
session_start();
require_once '../koneksi.php';
if (!isset($_SESSION['user'])) {
    echo "<script>alert('Silahkan login terlebih dahulu'); window.location='../login.php';</script>";
    exit;
}

function limit_text($html, $limit = 120)
{
    $text = strip_tags($html);

    $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');

    if (strlen($text) <= $limit) {
        return $text;
    }

    return substr($text, 0, $limit) . '...';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Pengumuman Akademik Online</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="../assets/foto/logo1.png" type="image/x-icon" />

    <!-- Fonts and icons -->
    <script src="../assets/panel/assets/js/plugin/webfont/webfont.min.js"></script>
    <script>
        WebFont.load({
            google: {
                families: ["Public Sans:300,400,500,600,700"]
            },
            custom: {
                families: [
                    "Font Awesome 5 Solid",
                    "Font Awesome 5 Regular",
                    "Font Awesome 5 Brands",
                    "simple-line-icons",
                ],
                urls: ["../assets/panel/assets/css/fonts.min.css"],
            },
            active: function() {
                sessionStorage.fonts = true;
            },
        });
    </script>

    <!-- CSS Files -->
    <link rel="stylesheet" href="../assets/panel/assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../assets/panel/assets/css/plugins.min.css" />
    <link rel="stylesheet" href="../assets/panel/assets/css/kaiadmin.min.css" />

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

    <script src="../assets/ckeditor/ckeditor.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <?php if ($_SESSION['user']['role'] == 'Admin'): ?>
        <style>
            .sidebar,
            .sidebar[data-background-color=light] {
                background: #ffffff !important;
                color: #343a40 !important;
                box-shadow: 4px 0 10px rgba(0, 0, 0, .08);
            }

            .sidebar .nav a {
                color: #495057 !important;
                font-weight: 500;
                display: flex;
                align-items: center;
                padding: 10px 15px;
                border-radius: 8px;
                transition: all .25s ease;
            }

            .sidebar .nav i {
                color: #6c757d !important;
                margin-right: 10px;
                width: 20px;
                text-align: center;
            }

            .sidebar .nav a:hover {
                background: rgba(65, 147, 212, .12) !important;
                color: #4193d4 !important;
            }

            .sidebar .nav a:hover i {
                color: #4193d4 !important;
            }

            .sidebar .nav-item.active>a {
                background: #4193d4 !important;
                color: #ffffff !important;
                font-weight: 600;
                box-shadow: inset 4px 0 0 #2c7ec1;
            }

            .sidebar .nav-item.active>a i {
                color: #ffffff !important;
            }

            .sidebar .text-section {
                color: #adb5bd !important;
                text-transform: uppercase;
                font-size: .75rem;
                letter-spacing: .6px;
                margin: 15px 0 5px;
            }

            .sidebar-wrapper.scrollbar-inner::-webkit-scrollbar {
                width: 6px;
            }

            .sidebar-wrapper.scrollbar-inner::-webkit-scrollbar-thumb {
                background: rgba(0, 0, 0, .2);
                border-radius: 3px;
            }


            .logo-header[data-background-color=light] {
                background-color: #4193d4 !important;
                border-bottom: 1px solid rgba(0, 0, 0, .1);
            }

            .logo-header .logo,
            .logo-header .logo h4 {
                color: #1a1a1a !important;
                font-weight: 700;
            }

            .logo-header .btn-toggle,
            .logo-header .topbar-toggler.more {
                background: transparent !important;
                border: none;
            }

            .logo-header .btn-toggle i,
            .logo-header .topbar-toggler.more i {
                color: #1a1a1a !important;
                font-size: 18px;
            }

            .logo-header .btn-toggle:hover i,
            .logo-header .topbar-toggler.more:hover i {
                color: #ef6c00 !important;
            }

            .main-header {
                background: #4193d4 !important;
                min-height: 60px;
                width: calc(100% - 250px);
                position: fixed;
                z-index: 1001;
            }

            .avatar {
                width: 48px;
                height: 48px;
                background: #4193d4;
                color: #fff;
            }
        </style>

        <style>
            .notif-link {
                color: #ffffff !important;
            }

            .notif-link i {
                font-size: 18px;
            }

            .notification {
                background: #ff4d4f;
                color: #fff;
                font-size: 11px;
                font-weight: bold;
                border-radius: 50%;
                padding: 2px 6px;
                position: absolute;
                top: 6px;
                right: 6px;
            }

            .notif-box {
                width: 320px;
                border-radius: 10px;
                overflow: hidden;
            }

            .dropdown-title {
                background: #4292d4;
                color: #fff;
                font-weight: 600;
                padding: 12px;
            }

            .notif-center a {
                display: flex;
                align-items: center;
                padding: 10px 15px;
                transition: background 0.2s;
            }

            .notif-center a:hover {
                background: #f4f6f9;
            }

            .notif-icon {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                background: #4292d4;
                color: #fff;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-right: 12px;
            }

            .notif-content .block {
                font-weight: 600;
                color: #333;
            }

            .notif-content .time {
                font-size: 12px;
                color: #888;
            }

            .see-all {
                display: block;
                text-align: center;
                padding: 10px;
                font-weight: 600;
                color: #4292d4;
                background: #f8f9fa;
            }

            .see-all:hover {
                background: #e9ecef;
            }
        </style>
    <?php else: ?>
        <style>
            .sidebar,
            .sidebar[data-background-color=light] {
                background: #ffffff !important;
                color: #343a40 !important;
                box-shadow: 4px 0 10px rgba(0, 0, 0, .08);
            }

            .sidebar .nav a {
                color: #495057 !important;
                font-weight: 500;
                display: flex;
                align-items: center;
                padding: 10px 15px;
                border-radius: 8px;
                transition: all .25s ease;
            }

            .sidebar .nav i {
                color: #6c757d !important;
                margin-right: 10px;
                width: 20px;
                text-align: center;
            }

            .sidebar .nav a:hover {
                background: rgba(65, 147, 212, .12) !important;
                color: #a9a28c !important;
            }

            .sidebar .nav a:hover i {
                color: #a9a28c !important;
            }

            .sidebar .nav-item.active>a {
                background: #a9a28c !important;
                color: #ffffff !important;
                font-weight: 600;
                box-shadow: inset 4px 0 0 #6f6a55;
                /* olive gelap */
            }

            .sidebar .nav-item.active>a i {
                color: #ffffff !important;
            }

            .sidebar .text-section {
                color: #adb5bd !important;
                text-transform: uppercase;
                font-size: .75rem;
                letter-spacing: .6px;
                margin: 15px 0 5px;
            }

            .sidebar-wrapper.scrollbar-inner::-webkit-scrollbar {
                width: 6px;
            }

            .sidebar-wrapper.scrollbar-inner::-webkit-scrollbar-thumb {
                background: rgba(0, 0, 0, .2);
                border-radius: 3px;
            }


            .logo-header[data-background-color=light] {
                background-color: #a9a28c !important;
                border-bottom: 1px solid rgba(0, 0, 0, .1);
            }

            .logo-header .logo,
            .logo-header .logo h4 {
                color: #1a1a1a !important;
                font-weight: 700;
            }

            .logo-header .btn-toggle,
            .logo-header .topbar-toggler.more {
                background: transparent !important;
                border: none;
            }

            .logo-header .btn-toggle i,
            .logo-header .topbar-toggler.more i {
                color: #1a1a1a !important;
                font-size: 18px;
            }

            .logo-header .btn-toggle:hover i,
            .logo-header .topbar-toggler.more:hover i {
                color: #ef6c00 !important;
            }

            .main-header {
                background: #a9a28c !important;
                min-height: 60px;
                width: calc(100% - 250px);
                position: fixed;
                z-index: 1001;
            }

            .avatar {
                width: 48px;
                height: 48px;
                background: #a9a28c;
                color: #fff;
            }
        </style>

        <style>
            .notif-link {
                color: #ffffff !important;
            }

            .notif-link i {
                font-size: 18px;
            }

            .notification {
                background: #ff4d4f;
                color: #fff;
                font-size: 11px;
                font-weight: bold;
                border-radius: 50%;
                padding: 2px 6px;
                position: absolute;
                top: 6px;
                right: 6px;
            }

            .notif-box {
                width: 320px;
                border-radius: 10px;
                overflow: hidden;
            }

            .dropdown-title {
                background: #a9a28c;
                color: #fff;
                font-weight: 600;
                padding: 12px;
            }

            .notif-center a {
                display: flex;
                align-items: center;
                padding: 10px 15px;
                transition: background 0.2s;
            }

            .notif-center a:hover {
                background: #f4f6f9;
            }

            .notif-icon {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                background: #a9a28c;
                color: #fff;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-right: 12px;
            }

            .notif-content .block {
                font-weight: 600;
                color: #333;
            }

            .notif-content .time {
                font-size: 12px;
                color: #888;
            }

            .see-all {
                display: block;
                text-align: center;
                padding: 10px;
                font-weight: 600;
                color: #a9a28c;
                background: #f8f9fa;
            }

            .see-all:hover {
                background: #e9ecef;
            }
        </style>
    <?php endif; ?>

    <style>
        .container {
            background-image: url('../assets/foto/bg.jpg');
            background-size: cover;
        }
    </style>

</head>

<body>
    <div class="wrapper">
        <!-- Sidebar -->

        <div class="sidebar" data-background-color="light">
            <div class="sidebar-logo">
                <!-- Logo Header -->
                <div class="logo-header" data-background-color="light">
                    <!-- <img src="../assets/panel/assets/img/kaiadmin/logo_light.svg" alt="navbar brand"
                            class="navbar-brand" height="20" />  -->
                    <a href="index.php" class="logo d-flex flex-column">
                        <h4 class="mb-0 text-black fw-bold mt-1">Pengumuman Akademik Online</h4>
                    </a>

                    <div class="nav-toggle">
                        <button class="btn btn-toggle toggle-sidebar">
                            <i class="gg-menu-right"></i>
                        </button>
                        <button class="btn btn-toggle sidenav-toggler">
                            <i class="gg-menu-left"></i>
                        </button>
                    </div>

                    <button class="topbar-toggler more">
                        <i class="gg-more-vertical-alt"></i>
                    </button>
                </div>

                <!-- End Logo Header -->
            </div>
            <div class="sidebar-wrapper scrollbar scrollbar-inner">
                <div class="sidebar-content">
                    <!-- User Profile -->
                    <div class="sidebar-user px-3 py-3 mb-3">
                        <div class="d-flex align-items-center">

                            <!-- Avatar -->
                            <div class="avatar avatar-lg rounded-circle d-flex align-items-center justify-content-center fw-bold me-3">
                                <?= strtoupper(substr($_SESSION['user']['nama'], 0, 1)); ?>
                            </div>

                            <!-- User Info -->
                            <div class="user-info">
                                <div class="fw-semibold text-dark">
                                    <?= $_SESSION['user']['nama']; ?>
                                </div>
                                <small class="text-muted text-capitalize">
                                    <?= $_SESSION['user']['role']; ?>
                                </small>
                            </div>



                        </div>
                    </div>

                    <!-- End User Profile -->

                    <hr class="my-2" style="border-color: white">

                    <?php
                    $page = $_GET['page'] ?? 'dashboard';

                    function isActive($pages, $current)
                    {
                        if (is_array($pages)) {
                            return in_array($current, $pages) ? 'active' : '';
                        }
                        return ($current === $pages) ? 'active' : '';
                    }
                    ?>

                    <ul class="nav nav-secondary">

                        <!-- DASHBOARD -->
                        <li class="nav-item <?= isActive(['dashboard', 'index'], $page) ?>">
                            <a href="index.php?page=dashboard">
                                <i class="fas fa-home"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>

                        <li class="nav-section">
                            <h4 class="text-section">Master Data</h4>
                        </li>

                        <?php if ($_SESSION['user']['role'] == 'Admin'): ?>
                            <!-- MAHASISWA -->
                            <li class="nav-item <?= isActive(['mahasiswa', 'mahasiswaedit', 'mahasiswadetail'], $page) ?>">
                                <a href="index.php?page=mahasiswa">
                                    <i class="fas fa-users"></i>
                                    <p>Mahasiswa</p>
                                </a>
                            </li>
                        <?php endif; ?>

                        <!-- PENGUMUMAN -->
                        <li class="nav-item <?= isActive(['pengumuman', 'pengumumanedit', 'pengumumandetail'], $page) ?>">
                            <a href="index.php?page=pengumuman">
                                <i class="fas fa-bullhorn"></i>
                                <p>Pengumuman</p>
                            </a>
                        </li>

                        <!-- JADWAL UJIAN -->
                        <li class="nav-item <?= isActive(['jadwalujian', 'jadwalujianedit'], $page) ?>">
                            <a href="index.php?page=jadwalujian">
                                <i class="fas fa-calendar-alt"></i>
                                <p>Jadwal Ujian</p>
                            </a>
                        </li>

                        <!-- JADWAL KULIAH -->
                        <li class="nav-item <?= isActive(['jadwalkuliah', 'jadwalkuliahedit'], $page) ?>">
                            <a href="index.php?page=jadwalkuliah">
                                <i class="fas fa-clock"></i>
                                <p>Jadwal Kuliah</p>
                            </a>
                        </li>

                        <!-- BEASISWA -->
                        <li class="nav-item <?= isActive([
                                                'beasiswa',
                                                'beasiswaedit',
                                                'pendaftaran',
                                                'pendaftaranedit',
                                                'pendaftarandetail'
                                            ], $page) ?>">
                            <a href="index.php?page=beasiswa">
                                <i class="fas fa-graduation-cap"></i>
                                <p>Beasiswa</p>
                            </a>
                        </li>

                    </ul>


                </div>
            </div>
        </div>
        <!-- End Sidebar -->

        <div class="main-panel">
            <div class="main-header">
                <div class="main-header-logo">
                    <!-- Logo Header -->
                    <div class="logo-header" data-background-color="light">
                        <a href="panel" class="logo">
                            <!-- <img src="../assets/panel/assets/img/kaiadmin/logo_light.svg"
                                alt="navbar brand" class="navbar-brand" height="20" /> -->
                            <h4 class=" text-black">Pengumuman Akademik Online</h4>
                        </a>
                        <div class="nav-toggle">
                            <button class="btn btn-toggle toggle-sidebar">
                                <i class="gg-menu-right"></i>
                            </button>
                            <button class="btn btn-toggle sidenav-toggler">
                                <i class="gg-menu-left"></i>
                            </button>
                        </div>
                        <button class="topbar-toggler more">
                            <i class="gg-more-vertical-alt"></i>
                        </button>
                    </div>
                    <!-- End Logo Header -->
                </div>
                <!-- Navbar Header -->
                <nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
                    <div class="container-fluid">

                        <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">

                            <?php
                            $userId = $_SESSION['user']['id'];

                            $qCount = mysqli_query($koneksi, "
                                    SELECT COUNT(*) AS total 
                                    FROM notifikasi 
                                    WHERE user_id = '$userId' AND status = 'Belum Dibaca'
                                ");
                            $countNotif = mysqli_fetch_assoc($qCount)['total'];

                            $qNotif = mysqli_query($koneksi, "
                                    SELECT * FROM notifikasi
                                    WHERE user_id = '$userId'
                                    AND status = 'Belum Dibaca'
                                    ORDER BY created_at DESC
                                    LIMIT 8
                                ");
                            ?>
                            <li class="nav-item topbar-icon dropdown hidden-caret">
                                <a class="nav-link dropdown-toggle notif-link" href="#"
                                    id="notifDropdown" role="button" data-bs-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">

                                    <i class="fa fa-bell"></i>

                                    <?php if ($countNotif > 0): ?>
                                        <span class="notification"><?= $countNotif; ?></span>
                                    <?php endif; ?>
                                </a>

                                <ul class="dropdown-menu notif-box animated fadeIn"
                                    aria-labelledby="notifDropdown">

                                    <li>
                                        <div class="dropdown-title">
                                            Notifikasi Terbaru
                                        </div>
                                    </li>

                                    <li>
                                        <div class="notif-scroll scrollbar-outer">
                                            <div class="notif-center">

                                                <?php if (mysqli_num_rows($qNotif) > 0): ?>
                                                    <?php while ($n = mysqli_fetch_assoc($qNotif)): ?>
                                                        <a href="#"
                                                            class="<?= $n['status'] == 'Belum Dibaca' ? 'fw-bold' : ''; ?>">
                                                            <div class="notif-icon notif-primary">
                                                                <i class="fa fa-info-circle"></i>
                                                            </div>
                                                            <div class="notif-content">
                                                                <span class="block"><?= htmlspecialchars($n['judul']); ?></span>
                                                                <span class="time">
                                                                    <?= date('d M Y H:i', strtotime($n['created_at'])); ?>
                                                                </span>
                                                            </div>
                                                        </a>
                                                    <?php endwhile; ?>
                                                <?php else: ?>
                                                    <div class="text-center text-muted py-3">
                                                        Tidak ada notifikasi
                                                    </div>
                                                <?php endif; ?>

                                            </div>
                                        </div>
                                    </li>

                                    <li>
                                        <a class="see-all" href="index.php?page=notif_read_all">
                                            Tandai semua telah dibaca
                                            <i class="fa fa-check"></i>
                                        </a>
                                    </li>
                                </ul>
                            </li>



                            <li class="nav-item topbar-user dropdown hidden-caret">
                                <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#"
                                    aria-expanded="false">
                                    <div class="avatar-sm">
                                        <img src="../assets/foto/avatar.png" alt="..."
                                            class="avatar-img rounded-circle" />
                                    </div>
                                    <span class="profile-username">
                                        <span class="op-7">Hai,</span>
                                        <span class="fw-bold"><?= $_SESSION['user']['nama']; ?></span>
                                    </span>
                                </a>
                                <ul class="dropdown-menu dropdown-user animated fadeIn">
                                    <div class="dropdown-user-scroll scrollbar-outer">
                                        <li>
                                            <div class="user-box">
                                                <div class="avatar-lg">
                                                    <img src="../assets/foto/avatar.png"
                                                        alt="image profile" class="avatar-img rounded" />
                                                </div>
                                                <div class="u-text">
                                                    <h4><?= $_SESSION['user']['nama']; ?></h4>
                                                    <p class="text-muted"><?= $_SESSION['user']['email']; ?></p>
                                                    <a href="index.php?page=profile"
                                                        class="btn btn-xs btn-secondary btn-sm">Lihat Profil</a>
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="index.php?page=profile">Profil Saya</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="../logout.php"
                                                onclick="return confirm('Yakin ingin keluar?')">Keluar</a>
                                        </li>
                                    </div>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>
                <!-- End Navbar -->
            </div>


            <?php
            if (isset($_GET['page'])) {
                if ($_GET['page'] == 'dashboard') {
                    include 'dashboard.php';
                } elseif ($_GET['page'] == 'mahasiswa') {
                    include 'mahasiswa.php';
                } elseif ($_GET['page'] == 'mahasiswaedit') {
                    include 'mahasiswaedit.php';
                } elseif ($_GET['page'] == 'mahasiswahapus') {
                    include 'mahasiswahapus.php';
                } elseif ($_GET['page'] == 'pengumuman') {
                    include 'pengumuman.php';
                } elseif ($_GET['page'] == 'pengumumanedit') {
                    include 'pengumumanedit.php';
                } elseif ($_GET['page'] == 'pengumumandetail') {
                    include 'pengumumandetail.php';
                } elseif ($_GET['page'] == 'pengumumanhapus') {
                    include 'pengumumanhapus.php';
                } elseif ($_GET['page'] == 'jadwalujian') {
                    include 'jadwalujian.php';
                } elseif ($_GET['page'] == 'jadwalujianedit') {
                    include 'jadwalujianedit.php';
                } elseif ($_GET['page'] == 'jadwalujianhapus') {
                    include 'jadwalujianhapus.php';
                } elseif ($_GET['page'] == 'jadwalkuliah') {
                    include 'jadwalkuliah.php';
                } elseif ($_GET['page'] == 'jadwalkuliahedit') {
                    include 'jadwalkuliahedit.php';
                } elseif ($_GET['page'] == 'jadwalkuliahhapus') {
                    include 'jadwalkuliahhapus.php';
                } elseif ($_GET['page'] == 'beasiswa') {
                    include 'beasiswa.php';
                } elseif ($_GET['page'] == 'beasiswaedit') {
                    include 'beasiswaedit.php';
                } elseif ($_GET['page'] == 'beasiswahapus') {
                    include 'beasiswahapus.php';
                } elseif ($_GET['page'] == 'profile') {
                    include 'profile.php';
                } elseif ($_GET['page'] == 'beasiswadetail') {
                    include 'beasiswadetail.php';
                } elseif ($_GET['page'] == 'pendaftaran') {
                    include 'pendaftaran.php';
                } elseif ($_GET['page'] == 'pendaftaranedit') {
                    include 'pendaftaranedit.php';
                } elseif ($_GET['page'] == 'pendaftarandetail') {
                    include 'pendaftarandetail.php';
                } elseif ($_GET['page'] == 'pendaftaranhapus') {
                    include 'pendaftaranhapus.php';
                } elseif ($_GET['page'] == 'notif_read_all') {
                    include 'notif_read_all.php';
                }
            } else {
                include 'dashboard.php';
            }
            ?>


            <footer class="footer">
                <div class="container-fluid d-flex justify-content-between align-items-center">
                    <div class="copyright">
                        <?= date('Y'); ?>, dibuat oleh
                        <a href="index.php">Pengumuman Akademik Online</a>
                    </div>
                    <div>
                        <a href="#" data-bs-toggle="modal" data-bs-target="#modalPrivasi" class="me-3">Kebijakan Privasi</a>
                        <a href="#" data-bs-toggle="modal" data-bs-target="#modalKontak">Kontak</a>
                    </div>
                </div>
            </footer>

            <!-- Modal Kebijakan Privasi -->
            <div class="modal fade" id="modalPrivasi" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">Kebijakan Privasi</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>
                                <!-- Masukkan isi kebijakan privasi di sini -->
                                Sistem Pengumuman Akademik Online menghargai dan melindungi privasi setiap pengguna. Informasi pribadi yang dikumpulkan melalui sistem ini digunakan semata-mata untuk keperluan pengelolaan layanan akademik dan penyampaian informasi resmi.
                                <br>
                                Data pengguna disimpan dan dikelola secara aman serta tidak dibagikan kepada pihak ketiga tanpa persetujuan pengguna, kecuali diwajibkan oleh ketentuan hukum yang berlaku. Pengguna bertanggung jawab menjaga kerahasiaan akun masing-masing.
                                <br>
                                Dengan menggunakan sistem ini, pengguna dianggap telah menyetujui kebijakan privasi yang berlaku.
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Kontak -->
            <div class="modal fade" id="modalKontak" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title">Kontak</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>Hubungi kami melalui:</p>
                            <ul>
                                <li>Email: <a href="mailto:info@pengumumanakademik.com">info@pengumumanakademik.com</a></li>
                                <li>Telepon: +62 812 3456 7890</li>
                                <li>Alamat: Jl. Pendidikan No.123, Kota Contoh</li>
                            </ul>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!--   Core JS Files   -->
    <script src="../assets/panel/assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/panel/assets/js/core/popper.min.js"></script>
    <script src="../assets/panel/assets/js/core/bootstrap.min.js"></script>

    <!-- jQuery Scrollbar -->
    <script src="../assets/panel/assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>

    <!-- Chart JS -->
    <script src="../assets/panel/assets/js/plugin/chart.js/chart.min.js"></script>

    <!-- jQuery Sparkline -->
    <script src="../assets/panel/assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js"></script>

    <!-- Chart Circle -->
    <script src="../assets/panel/assets/js/plugin/chart-circle/circles.min.js"></script>

    <!-- Datatables -->
    <script src="../assets/panel/assets/js/plugin/datatables/datatables.min.js"></script>

    <!-- Bootstrap Notify -->
    <script src="../assets/panel/assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js"></script>

    <!-- jQuery Vector Maps -->
    <script src="../assets/panel/assets/js/plugin/jsvectormap/jsvectormap.min.js"></script>
    <script src="../assets/panel/assets/js/plugin/jsvectormap/world.js"></script>

    <!-- Sweet Alert -->
    <script src="../assets/panel/assets/js/plugin/sweetalert/sweetalert.min.js"></script>

    <!-- Kaiadmin JS -->
    <script src="../assets/panel/assets/js/kaiadmin.min.js"></script>


    <!-- Kaiadmin JS -->
    <script src="../assets/panel/assets/js/kaiadmin.min.js"></script>
    <!-- Kaiadmin DEMO methods, don't include it in your project! -->
    <script src="../assets/panel/assets/js/setting-demo2.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#datatable').DataTable({
                responsive: true,
                // language: {
                //     url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
                // }
            });
        });
    </script>


    <script>
        $(" #lineChart").sparkline([102, 109, 120, 99, 110, 105, 115], {
            type: "line",
            height: "70",
            width: "100%",
            lineWidth: "2",
            lineColor: "#177dff",
            fillColor: "rgba(23, 125, 255, 0.14)",
        });

        $("#lineChart2").sparkline([99, 125, 122, 105, 110, 124, 115], {
            type: "line",
            height: "70",
            width: "100%",
            lineWidth: "2",
            lineColor: "#f3545d",
            fillColor: "rgba(243, 84, 93, .14)",
        });

        $("#lineChart3").sparkline([105, 103, 123, 100, 95, 105, 115], {
            type: "line",
            height: "70",
            width: "100%",
            lineWidth: "2",
            lineColor: "#ffa534",
            fillColor: "rgba(255, 165, 52, .14)",
        });
    </script>

    <script>
        $('.modal').on('shown.bs.modal', function() {
            $(this).find('.select2modal').select2({
                dropdownParent: $('.modal'),
                width: '100%'
            });
        });

        $(document).ready(function() {
            $('.select2').select2({
                width: '100%'
            });
        });
    </script>

</body>

</html>