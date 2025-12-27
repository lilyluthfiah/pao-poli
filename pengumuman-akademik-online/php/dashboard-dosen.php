<?php
session_start();

// ✅ proteksi: harus login
if (!isset($_SESSION['login'], $_SESSION['nama'], $_SESSION['role'])) {
  header("Location: ../auth/login.html");
  exit;
}

// ✅ proteksi role dosen
if ($_SESSION['role'] !== 'dosen') {
  header("Location: ../auth/login.html");
  exit;
}

$nama = $_SESSION['nama'];
$role = $_SESSION['role'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard Dosen - PAO POLIBATAM</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <!-- CSS -->
  <link rel="stylesheet" href="../css/styledashboardDosen.css">
</head>

<body data-nama="<?= htmlspecialchars($nama) ?>" data-role="<?= htmlspecialchars($role) ?>">
  <div class="overlay-bg">
    <!-- 🔷 HEADER -->
    <header class="header d-flex justify-content-between align-items-center px-4 position-relative">
      <div>
        <h4 class="fw-bold mb-0" id="greetingNama">Halo, ...</h4>
        <h5 class="fw-bold" id="greetingRole">SELAMAT DATANG DI PAO-POLIBATAM</h5>
      </div>

      <div class="d-flex align-items-center gap-3 position-relative">
        <!-- 🔔 Notifikasi -->
        <i class="bi bi-bell fs-3 text-dark" id="notifIcon" title="Notifikasi"></i>
        <div class="notification-popup shadow" id="notifPopup">
          <h6 class="fw-bold mb-2">📢 Notifikasi</h6>
          <div id="notifPopupContent">
            <p class="text-muted small">Belum ada notifikasi.</p>
          </div>
        </div>

        <!-- 👤 Profile Dropdown -->
        <div class="position-relative">
          <i class="bi bi-person-circle fs-3 text-dark" id="profileIcon" title="Profile"></i>
          <div class="profile-dropdown shadow" id="profileDropdown">
            <h6>Settings</h6>
            <a href="../profile.html"><i class="bi bi-person me-2"></i>Profile</a>
            <a href="../change-password.html"><i class="bi bi-key me-2"></i>Change Password</a>
          </div>
        </div>

        <!-- 🚪 Logout -->
        <i class="bi bi-box-arrow-right fs-3 text-dark logout-btn" title="Logout"></i>
      </div>
    </header>

    <!-- 🔹 LAYOUT UTAMA -->
    <div class="d-flex flex-grow-1">
      <!-- SIDEBAR -->
      <aside class="sidebar p-3">
        <ul class="nav flex-column">
          <li class="nav-item"><a class="nav-link active" href="dashboard-dosen.php">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="../pengumuman-dosen.html">Pengumuman</a></li>
          <li class="nav-item"><a class="nav-link" href="../jadwalujian-dosen.html">Jadwal Ujian</a></li>
          <li class="nav-item"><a class="nav-link" href="../jadwalkuliah-dosen.html">Jadwal Kuliah</a></li>
          <li class="nav-item"><a class="nav-link" href="../beasiswadosen.html">Beasiswa</a></li>
        </ul>
      </aside>

      <!-- KONTEN UTAMA -->
      <main class="content p-4 flex-grow-1">
        <div class="hero-box text-center">
          <h2 class="fw-bold mb-3">Selamat Datang di PAO-POLIBATAM 🎓</h2>
          <p class="mb-3">Tahun Akademik 2026/2027</p>
          <a href="../jadwalkuliah-dosen.html" class="btn btn-primary btn-lg rounded-pill px-4">Jadwal Perkuliahan</a>
        </div>

        <div class="container mt-5">
          <h4 class="fw-bold mb-4">📊 Ringkasan Akademik</h4>
          <div class="row g-4">
            <div class="col-md-4">
              <div class="card shadow-sm border-0 text-center p-3">
                <i class="bi bi-megaphone text-primary fs-2 mb-2"></i>
                <h6 class="fw-bold">Total Pengumuman</h6>
                <p class="fs-4 fw-bold text-primary mb-0" id="totalPengumuman">3</p>
              </div>
            </div>
            <div class="col-md-4">
              <div class="card shadow-sm border-0 text-center p-3">
                <i class="bi bi-calendar-event text-success fs-2 mb-2"></i>
                <h6 class="fw-bold">Jadwal Ujian</h6>
                <p class="fs-4 fw-bold text-success mb-0" id="jadwalUjian">2</p>
              </div>
            </div>
            <div class="col-md-4">
              <div class="card shadow-sm border-0 text-center p-3">
                <i class="bi bi-award text-warning fs-2 mb-2"></i>
                <h6 class="fw-bold">Beasiswa Aktif</h6>
                <p class="fs-4 fw-bold text-warning mb-0" id="beasiswaAktif">1</p>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>

    <!-- FOOTER -->
    <footer class="footer text-center text-white py-2">
      © 2025 Pengumuman Akademik Online - POLIBATAM |
      <a href="#" class="text-dark text-decoration-none fw-semibold">Kebijakan Privasi</a> |
      <a href="#" class="text-dark text-decoration-none fw-semibold">Kontak</a>
    </footer>
  </div>

  <!-- SCRIPT -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../js/dashboard-dosen.js"></script>
</body>
</html>