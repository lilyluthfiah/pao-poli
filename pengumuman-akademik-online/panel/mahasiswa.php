<?php
if ($_SESSION['user']['role'] == 'Mahasiswa') {
    echo "<script>window.location='index.php?page=dashboard';</script>";
    exit;
}
?>
<div class="container">
    <div class="page-inner">

        <!-- HEADER -->
        <div class="page-header d-flex justify-content-between align-items-center">
            <h3 class="fw-bold">Data Mahasiswa</h3>
            <?php if ($_SESSION['user']['role'] == 'Admin'): ?>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="fa fa-plus me-1"></i> Tambah Mahasiswa
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
                                <th>Nama</th>
                                <th>NIM / NIDN</th>
                                <th>Email</th>
                                <th>No HP</th>
                                <th>Prodi</th>
                                <th>Kelas</th>
                                <?php if ($_SESSION['user']['role'] == 'Admin'): ?>
                                    <th width="120">Aksi</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $data = mysqli_query($koneksi, "
            SELECT * FROM users 
            WHERE role = 'Mahasiswa'
            ORDER BY id DESC
        ");
                            ?>

                            <?php while ($row = mysqli_fetch_assoc($data)) : ?>
                                <tr>
                                    <td class="text-center"><?= $no++; ?></td>
                                    <td><?= $row['nama']; ?></td>
                                    <td><?= $row['nimataunidn']; ?></td>
                                    <td><?= $row['email']; ?></td>
                                    <td><?= $row['nohp']; ?></td>
                                    <td><?= $row['prodi']; ?></td>
                                    <td><?= $row['kelas']; ?></td>

                                    <?php if ($_SESSION['user']['role'] == 'Admin'): ?>
                                        <td class="text-center">
                                            <a href="index.php?page=mahasiswaedit&id=<?= $row['id']; ?>"
                                                class="btn btn-sm btn-warning">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a href="index.php?page=mahasiswahapus&id=<?= $row['id']; ?>"
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
                <h5 class="modal-title">Tambah Mahasiswa</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST">
                <div class="modal-body">

                    <div class="mb-3">
                        <label class="fw-bold">Nama</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">NIM</label>
                        <input type="text" name="nimataunidn" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">No HP</label>
                        <input type="text" name="nohp" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Alamat</label>
                        <textarea name="alamat" class="form-control"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Prodi</label>
                        <input type="text" name="prodi" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Kelas</label>
                        <input type="text" name="kelas" class="form-control">
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" name="simpan_mahasiswa" class="btn btn-primary">
                        Simpan
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>


<?php
if (isset($_POST['simpan_mahasiswa'])) {

    $nama   = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $nim    = mysqli_real_escape_string($koneksi, $_POST['nimataunidn']);
    $email  = mysqli_real_escape_string($koneksi, $_POST['email']);
    $pass   = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $nohp   = mysqli_real_escape_string($koneksi, $_POST['nohp']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $prodi  = mysqli_real_escape_string($koneksi, $_POST['prodi']);
    $kelas  = mysqli_real_escape_string($koneksi, $_POST['kelas']);

    mysqli_query($koneksi, "
        INSERT INTO users 
        (nama, email, password, nimataunidn, nohp, alamat, prodi, kelas, role)
        VALUES 
        ('$nama', '$email', '$pass', '$nim', '$nohp', '$alamat', '$prodi', '$kelas', 'Mahasiswa')
    ");

    echo "<script>
        alert('Data mahasiswa berhasil ditambahkan');
        window.location='index.php?page=mahasiswa';
    </script>";
    exit;
}
?>