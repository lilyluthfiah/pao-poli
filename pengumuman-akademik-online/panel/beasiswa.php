<div class="container">
    <div class="page-inner">

        <!-- HEADER -->
        <div class="page-header d-flex justify-content-between align-items-center">
            <h3 class="fw-bold">Manajemen Beasiswa</h3>

            <?php if ($_SESSION['user']['role'] == 'Admin'): ?>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="fa fa-plus me-1"></i> Tambah Beasiswa
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
                                <th>Nama Beasiswa</th>
                                <th>Deskripsi</th>
                                <th>Syarat</th>
                                <th>Periode Pendaftaran</th>
                                <th>Panduan</th>
                                <th width="120">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $data = mysqli_query($koneksi, "
                                SELECT * FROM beasiswa 
                                ORDER BY tanggal DESC
                            ");
                            ?>

                            <?php while ($row = mysqli_fetch_assoc($data)) : ?>
                                <tr>
                                    <td class="text-center"><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($row['nama']); ?></td>
                                    <td title="<?= strip_tags($row['deskripsi']); ?>">
                                        <?= limit_text(strip_tags($row['deskripsi']), 120); ?>
                                    </td>
                                    <td><?= htmlspecialchars($row['syarat']); ?></td>
                                    <td class="text-center">
                                        <?= date('d M Y', strtotime($row['tanggal'])); ?> -
                                        <?= date('d M Y', strtotime($row['tanggal_akhir'])); ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if (!empty($row['file_beasiswa'])): ?>
                                            <a href="../uploads/<?= $row['file_beasiswa']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fa fa-download"></i> Unduh
                                            </a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <a href="index.php?page=beasiswadetail&id=<?= $row['id']; ?>" class="btn btn-sm btn-info" title="Detail Beasiswa">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="index.php?page=pendaftaran&id=<?= $row['id']; ?>"
                                                class="btn btn-sm btn-primary"
                                                title="Pendaftaran">
                                                <i class="fa fa-users"></i>
                                            </a>

                                            <?php if ($_SESSION['user']['role'] == 'Admin'): ?>
                                                <a href="index.php?page=beasiswaedit&id=<?= $row['id']; ?>"
                                                    class="btn btn-sm btn-warning"
                                                    title="Edit">
                                                    <i class="fa fa-edit"></i>
                                                </a>

                                                <a href="index.php?page=beasiswahapus&id=<?= $row['id']; ?>"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Yakin ingin menghapus data?')"
                                                    title="Hapus">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
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

<!-- MODAL TAMBAH -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Tambah Beasiswa</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">

                    <div class="mb-3">
                        <label class="fw-bold">Nama Beasiswa</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Deskripsi</label>
                        <textarea name="deskripsi" id="deskripsi" class="form-control" rows="4" required></textarea>
                        <script>
                            CKEDITOR.replace('deskripsi');
                        </script>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Syarat</label>
                        <input type="text"
                            name="syarat"
                            class="form-control"
                            placeholder="Contoh: Lampirkan ijazah, sertifikat, dll."
                            required>
                        <small class="text-muted">
                            Pisahkan setiap syarat dengan koma (,)
                        </small>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="fw-bold">Tanggal Mulai Pendaftaran</label>
                                <input type="date" name="tanggal" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="fw-bold">Tanggal Akhir Pendaftaran</label>
                                <input type="date" name="tanggal_akhir" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Upload Panduan (PDF)</label>
                        <input type="file" name="file_beasiswa" class="form-control">
                        <small class="text-muted">Optional. Format: PDF</small>
                    </div>

                    <hr>
                    <h5 class="fw-bold mb-3">Persyaratan Dokumen</h5>

                    <div class="mb-3">
                        <label class="fw-bold">Dokumen Saat Pendaftaran</label>
                        <div id="syarat-pendaftaran-container">
                            <div class="input-group mb-2">
                                <input type="text" name="syarat_pendaftaran[]" class="form-control" placeholder="Nama Dokumen (misal: Scan KTP)" required>
                                <button type="button" class="btn btn-danger btn-remove" disabled>-</button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary mt-1" onclick="addSyarat('pendaftaran')">
                            + Tambah Dokumen
                        </button>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Dokumen Saat Lulus (Kelengkapan)</label>
                        <div id="syarat-lulus-container">
                            <div class="input-group mb-2">
                                <input type="text" name="syarat_lulus[]" class="form-control" placeholder="Nama Dokumen (misal: Buku Tabungan)">
                                <button type="button" class="btn btn-danger btn-remove" disabled>-</button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary mt-1" onclick="addSyarat('lulus')">
                            + Tambah Dokumen
                        </button>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" name="simpan_beasiswa" class="btn btn-primary">
                        Simpan
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
    function addSyarat(type) {
        let container = document.getElementById('syarat-' + type + '-container');
        let div = document.createElement('div');
        div.className = 'input-group mb-2';
        div.innerHTML = `
            <input type="text" name="syarat_${type}[]" class="form-control" placeholder="Nama Dokumen" required>
            <button type="button" class="btn btn-danger btn-remove" onclick="this.parentElement.remove()">-</button>
        `;
        container.appendChild(div);
    }
</script>

<?php
if (isset($_POST['simpan_beasiswa'])) {

    $nama       = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $deskripsi  = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $syarat     = mysqli_real_escape_string($koneksi, $_POST['syarat']);
    $tanggal    = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
    $tanggal_akhir = mysqli_real_escape_string($koneksi, $_POST['tanggal_akhir']);

    $file_beasiswa = '';
    if (!empty($_FILES['file_beasiswa']['name'])) {
        $file = $_FILES['file_beasiswa']['name'];
        $tmp = $_FILES['file_beasiswa']['tmp_name'];
        $file_beasiswa = time() . '_' . $file;
        move_uploaded_file($tmp, "../uploads/" . $file_beasiswa);
    }

    // 1. Simpan Data Beasiswa
    mysqli_query($koneksi, "
        INSERT INTO beasiswa (nama, deskripsi, syarat, tanggal, tanggal_akhir, file_beasiswa)
        VALUES ('$nama', '$deskripsi', '$syarat', '$tanggal', '$tanggal_akhir', '$file_beasiswa')
    ");

    $beasiswa_id = mysqli_insert_id($koneksi);

    // 2. Simpan Syarat Pendaftaran
    if (isset($_POST['syarat_pendaftaran'])) {
        foreach ($_POST['syarat_pendaftaran'] as $syarat) {
            if (!empty($syarat)) {
                $syarat = mysqli_real_escape_string($koneksi, $syarat);
                mysqli_query($koneksi, "INSERT INTO beasiswa_syarat (beasiswa_id, nama_syarat, tahap) VALUES ('$beasiswa_id', '$syarat', 'Pendaftaran')");
            }
        }
    }

    // 3. Simpan Syarat Lulus
    if (isset($_POST['syarat_lulus'])) {
        foreach ($_POST['syarat_lulus'] as $syarat) {
            if (!empty($syarat)) {
                $syarat = mysqli_real_escape_string($koneksi, $syarat);
                mysqli_query($koneksi, "INSERT INTO beasiswa_syarat (beasiswa_id, nama_syarat, tahap) VALUES ('$beasiswa_id', '$syarat', 'Lulus')");
            }
        }
    }

    echo "<script>alert('Data berhasil disimpan'); window.location='index.php?page=beasiswa';</script>";
    exit;
}
?>