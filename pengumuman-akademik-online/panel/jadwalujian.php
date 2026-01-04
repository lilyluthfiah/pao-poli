<div class="container">
    <div class="page-inner">

        <!-- HEADER -->
        <div class="page-header d-flex justify-content-between align-items-center">
            <h3 class="fw-bold">Manajemen Jadwal Ujian</h3>
            <?php if ($_SESSION['user']['role'] == 'Admin'): ?>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="fa fa-plus me-1"></i> Tambah Jadwal Ujian
                </button>
            <?php endif; ?>
        </div>

        <!-- TABLE -->
        <div class="card mt-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle" id="datatable">
                        <thead class="table-primary text-center">
                            <tr>
                                <th width="60">No</th>
                                <th>Mata Kuliah</th>
                                <th>Dosen</th>
                                <th>Tanggal</th>
                                <th>Waktu</th>
                                <th>Ruang</th>
                                <?php if ($_SESSION['user']['role'] == 'Admin'): ?>
                                    <th width="120">Aksi</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $user_id = $_SESSION['user']['id'];
                            if ($_SESSION['user']['role'] == 'Admin') {
                                $data = mysqli_query($koneksi, "SELECT * FROM jadwalujian WHERE user_id = '$user_id' ORDER BY tanggal DESC");
                            } else {
                                $data = mysqli_query($koneksi, "SELECT * FROM jadwalujian ORDER BY tanggal DESC");
                            }
                            ?>

                            <?php while ($row = mysqli_fetch_assoc($data)) : ?>
                                <tr>
                                    <td class="text-center"><?= $no++; ?></td>
                                    <td><?= $row['matakuliah']; ?></td>
                                    <td><?= $row['dosen']; ?></td>
                                    <td class="text-center"><?= $row['tanggal']; ?></td>
                                    <td class="text-center"><?= $row['waktu']; ?></td>
                                    <td class="text-center"><?= $row['ruang']; ?></td>
                                    <?php if ($_SESSION['user']['role'] == 'Admin'): ?>
                                        <td class="text-center">
                                            <a href="index.php?page=jadwalujianedit&id=<?= $row['id']; ?>"
                                                class="btn btn-sm btn-warning">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a href="index.php?page=jadwalujianhapus&id=<?= $row['id']; ?>"
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm('Yakin ingin menghapus data?')">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- MODAL TAMBAH -->
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
                        <label class="fw-bold">Dosen</label>
                        <input type="text" name="dosen" class="form-control" placeholder="Nama Admin" required>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Mata Kuliah</label>
                        <input type="text" name="matakuliah" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Waktu</label>
                        <input type="text"
                            name="waktu"
                            class="form-control"
                            placeholder="Contoh: 08:00-10:00"
                            required>
                    </div>


                    <div class="mb-3">
                        <label class="fw-bold">Ruang</label>
                        <input type="text" name="ruang" class="form-control" required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" name="simpan_jadwalujian" class="btn btn-primary">
                        Simpan
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<?php
if (isset($_POST['simpan_jadwalujian'])) {

    $user_id    = $_SESSION['user']['id'];
    $dosen      = mysqli_real_escape_string($koneksi, $_POST['dosen']);
    $matakuliah = mysqli_real_escape_string($koneksi, $_POST['matakuliah']);
    $tanggal    = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
    $waktu      = mysqli_real_escape_string($koneksi, $_POST['waktu']);
    $ruang      = mysqli_real_escape_string($koneksi, $_POST['ruang']);

    mysqli_query($koneksi, "
        INSERT INTO jadwalujian (user_id, dosen, matakuliah, tanggal, waktu, ruang)
        VALUES ('$user_id', '$dosen', '$matakuliah', '$tanggal', '$waktu', '$ruang')
    ");

    $createdAt   = date('Y-m-d H:i:s');
    $judulNotif  = "Jadwal Ujian Baru";
    $pesanNotif  = "Jadwal ujian mata kuliah $matakuliah telah ditambahkan.";
    $statusNotif = "Belum Dibaca";

    $mahasiswa = mysqli_query($koneksi, "
        SELECT id FROM users WHERE role = 'Mahasiswa'
    ");

    while ($m = mysqli_fetch_assoc($mahasiswa)) {
        $idMahasiswa = $m['id'];

        mysqli_query($koneksi, "
            INSERT INTO notifikasi (user_id, judul, pesan, status, created_at)
            VALUES ('$idMahasiswa', '$judulNotif', '$pesanNotif', '$statusNotif', '$createdAt')
        ");
    }

    echo "<script>
        alert('Jadwal ujian berhasil disimpan & notifikasi dikirim');
        window.location='index.php?page=jadwalujian';
    </script>";
    exit;
}
?>