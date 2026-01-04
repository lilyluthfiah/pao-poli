<div class="container">
    <div class="page-inner">

        <div class="page-header d-flex justify-content-between align-items-center">
            <h3 class="fw-bold">Manajemen Pengumuman</h3>
            <?php if ($_SESSION['user']['role'] == 'Admin'): ?>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="fa fa-plus me-1"></i> Tambah Pengumuman
                </button>
            <?php endif; ?>
        </div>

        <div class="card mt-3 mb-3">
            <div class="card-body">
                <form method="GET" class="row g-2 align-items-end">
                    <input type="hidden" name="page" value="pengumuman">

                    <div class="col-md-4">
                        <label class="form-label">Cari Judul</label>
                        <input type="text" name="search" class="form-control"
                            value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Dari Tanggal</label>
                        <input type="date" name="from_date" class="form-control"
                            value="<?= isset($_GET['from_date']) ? $_GET['from_date'] : '' ?>">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Sampai Tanggal</label>
                        <input type="date" name="to_date" class="form-control"
                            value="<?= isset($_GET['to_date']) ? $_GET['to_date'] : '' ?>">
                    </div>

                    <div class="col-md-2 d-grid">
                        <button type="submit" class="btn btn-primary mt-1">
                            <i class="fa fa-filter me-1"></i> Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle" id="datatable">
                        <thead class="table-primary text-center">
                            <tr>
                                <th width="60">No</th>
                                <th>Judul</th>
                                <th>Deskripsi</th>
                                <th>Tanggal</th>
                                <th width="120">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $where = "1";

                            if (!empty($_GET['search'])) {
                                $search = mysqli_real_escape_string($koneksi, $_GET['search']);
                                $where .= " AND judul LIKE '%$search%'";
                            }

                            if (!empty($_GET['from_date'])) {
                                $from = mysqli_real_escape_string($koneksi, $_GET['from_date']);
                                $where .= " AND tanggal >= '$from'";
                            }
                            if (!empty($_GET['to_date'])) {
                                $to = mysqli_real_escape_string($koneksi, $_GET['to_date']);
                                $where .= " AND tanggal <= '$to'";
                            }

                            $data = mysqli_query($koneksi, "SELECT * FROM pengumuman WHERE $where ORDER BY id DESC");

                            while ($row = mysqli_fetch_assoc($data)) : ?>
                                <tr>
                                    <td class="text-center"><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($row['judul']); ?></td>
                                    <td title="<?= strip_tags($row['deskripsi']); ?>">
                                        <?= limit_text($row['deskripsi'], 120); ?>
                                    </td>
                                    <td class="text-center"><?= $row['tanggal']; ?></td>
                                    <td class="text-center">
                                        <a href="index.php?page=pengumumandetail&id=<?= $row['id']; ?>"
                                            class="btn btn-sm btn-warning">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <?php if ($_SESSION['user']['role'] == 'Admin'): ?>
                                            <a href="index.php?page=pengumumanedit&id=<?= $row['id']; ?>"
                                                class="btn btn-sm btn-warning">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a href="index.php?page=pengumumanhapus&id=<?= $row['id']; ?>"
                                                class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus data?')">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Tambah Pengumuman</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST">
                <div class="modal-body">

                    <div class="mb-3">
                        <label class="fw-bold">Judul</label>
                        <input type="text" name="judul" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="4" required></textarea>
                        <script>
                            CKEDITOR.replace('deskripsi');
                        </script>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" name="simpan_pengumuman" class="btn btn-primary">
                        Simpan
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<?php
if (isset($_POST['simpan_pengumuman'])) {

    $judul      = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $deskripsi  = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $tanggal    = date('Y-m-d');

    mysqli_query($koneksi, "
        INSERT INTO pengumuman (judul, deskripsi, tanggal)
        VALUES ('$judul', '$deskripsi', '$tanggal')
    ");

    $createdAt = date('Y-m-d H:i:s');
    $judulNotif = "Pengumuman Baru";
    $pesanNotif = "Pengumuman baru telah diterbitkan: \"$judul\"";
    $statusNotif = "Belum Dibaca";

    $users = mysqli_query($koneksi, "
        SELECT id FROM users WHERE role = 'Mahasiswa'
    ");

    while ($u = mysqli_fetch_assoc($users)) {
        $user_id = $u['id'];

        mysqli_query($koneksi, "
            INSERT INTO notifikasi (user_id, judul, pesan, status, created_at)
            VALUES ('$user_id', '$judulNotif', '$pesanNotif', '$statusNotif', '$createdAt')
        ");
    }

    echo "<script>
        alert('Pengumuman berhasil disimpan & notifikasi dikirim');
        window.location='index.php?page=pengumuman';
    </script>";
    exit;
}
?>