<div class="container">
    <div class="page-inner">

        <!-- HEADER -->
        <div class="page-header d-flex justify-content-between align-items-center">
            <h3 class="fw-bold">Manajemen Pendaftaran Beasiswa</h3>
            <?php if ($_SESSION['user']['role'] == 'Mahasiswa'): ?>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="fa fa-plus me-1"></i> Tambah Pendaftaran
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
                                <th>Nama Lengkap</th>
                                <th>NIM</th>
                                <th>Prodi</th>

                                <th>Tanggal</th>
                                <th>Status</th>
                                <th width="140">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $beasiswa_id = $_GET['id'];
                            $user_id = $_SESSION['user']['id'];
                            if ($_SESSION['user']['role'] == 'Admin') {
                                $data = mysqli_query($koneksi, "
                                    SELECT * FROM pendaftaran 
                                    WHERE beasiswa_id = '$beasiswa_id'
                                    ORDER BY id DESC
                                ");
                            } else {
                                $data = mysqli_query($koneksi, "
                                    SELECT * FROM pendaftaran 
                                    WHERE beasiswa_id = '$beasiswa_id'
                                    AND user_id = '$user_id'
                                    ORDER BY id DESC
                                ");
                            }
                            ?>

                            <?php while ($row = mysqli_fetch_assoc($data)) : ?>
                                <tr>
                                    <td class="text-center"><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($row['namalengkap']); ?></td>
                                    <td><?= htmlspecialchars($row['nim']); ?></td>
                                    <td><?= htmlspecialchars($row['prodi']); ?></td>
                                    <td class="text-center"><?= $row['tanggal']; ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary"><?= $row['status']; ?></span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <a href="index.php?page=pendaftarandetail&id=<?= $row['id']; ?>"
                                                class="btn btn-sm btn-info">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <?php if ($_SESSION['user']['role'] == 'Mahasiswa' && $row['user_id'] == $_SESSION['user']['id']): ?>

                                                <?php if ($row['status'] == 'Diterima'): ?>
                                                    <?php
                                                    // Cek apakah sudah upload berkas lulus
                                                    $is_uploaded = false;
                                                    // Cek file default
                                                    if (!empty($row['file_lengkapi'])) {
                                                        $is_uploaded = true;
                                                    } else {
                                                        // Cek file dinamis
                                                        $qCekUp = mysqli_query($koneksi, "
                                                            SELECT pendaftaran_files.id 
                                                            FROM pendaftaran_files 
                                                            JOIN beasiswa_syarat ON pendaftaran_files.syarat_id = beasiswa_syarat.id 
                                                            WHERE pendaftaran_files.pendaftaran_id = '" . $row['id'] . "' 
                                                            AND beasiswa_syarat.tahap = 'Lulus'
                                                        ");
                                                        if (mysqli_num_rows($qCekUp) > 0) {
                                                            $is_uploaded = true;
                                                        }
                                                    }
                                                    ?>

                                                    <?php if ($is_uploaded): ?>
                                                        <!-- Button Penanda Sudah Upload -->
                                                        <button class="btn btn-sm btn-secondary" title="Berkas Telah Diupload" disabled>
                                                            <i class="fa fa-check-circle"></i>
                                                        </button>
                                                    <?php else: ?>
                                                        <!-- Button Upload -->
                                                        <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalLengkapi<?= $row['id']; ?>" title="Melengkapi Berkas">
                                                            <i class="fa fa-upload"></i>
                                                        </button>

                                                        <!-- Modal Lengkapi -->
                                                        <div class="modal fade" id="modalLengkapi<?= $row['id']; ?>" tabindex="-1">
                                                            <div class="modal-dialog modal-dialog-centered">
                                                                <div class="modal-content">
                                                                    <div class="modal-header bg-success text-white">
                                                                        <h5 class="modal-title">Lengkapi Berkas</h5>
                                                                        <button class="btn-close" data-bs-dismiss="modal"></button>
                                                                    </div>
                                                                    <form method="POST" enctype="multipart/form-data">
                                                                        <div class="modal-body text-start">
                                                                            <input type="hidden" name="id_pendaftaran" value="<?= $row['id']; ?>">

                                                                            <?php
                                                                            $qLengkap = mysqli_query($koneksi, "SELECT * FROM beasiswa_syarat WHERE beasiswa_id = '$beasiswa_id' AND tahap = 'Lulus'");
                                                                            if (mysqli_num_rows($qLengkap) > 0):
                                                                                while ($sl = mysqli_fetch_assoc($qLengkap)):
                                                                            ?>
                                                                                    <div class="mb-3">
                                                                                        <label class="fw-bold"><?= $sl['nama_syarat']; ?></label>
                                                                                        <input type="file" name="file_lengkap_<?= $sl['id']; ?>" class="form-control" required>
                                                                                    </div>
                                                                                <?php
                                                                                endwhile;
                                                                            else:
                                                                                ?>
                                                                                <div class="mb-3">
                                                                                    <label class="fw-bold">Upload Berkas Tambahan</label>
                                                                                    <input type="file" name="file_lengkapi_default" class="form-control" required>
                                                                                    <small class="text-muted">Upload berkas kelengkapan (Zip/PDF).</small>
                                                                                </div>
                                                                            <?php endif; ?>

                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                                            <button type="submit" name="upload_lengkapi" class="btn btn-primary">Simpan</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php endif; ?>

                                                <a href="index.php?page=pendaftaranedit&id=<?= $row['id']; ?>"
                                                    class="btn btn-sm btn-warning">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                            <?php endif; ?>
                                            <?php if ($_SESSION['user']['role'] == 'Admin'): ?>
                                                <a href="index.php?page=pendaftaranhapus&id=<?= $row['id']; ?>"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Yakin ingin menghapus data?')">
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
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Tambah Pendaftaran</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">

                    <div class="mb-3">
                        <label class="fw-bold">Nama Lengkap</label>
                        <input type="text" name="namalengkap" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">NIM</label>
                        <input type="text" name="nim" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Program Studi</label>
                        <input type="text" name="prodi" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Nomor Rekening</label>
                        <input type="text" name="no_rekening" class="form-control" placeholder="Contoh: BN 1234567890" required>
                    </div>

                    <?php
                    $qSyarat = mysqli_query($koneksi, "SELECT * FROM beasiswa_syarat WHERE beasiswa_id = '$beasiswa_id' AND tahap = 'Pendaftaran'");
                    if (mysqli_num_rows($qSyarat) > 0) {
                        while ($s = mysqli_fetch_assoc($qSyarat)) {
                    ?>
                            <div class="mb-3">
                                <label class="fw-bold"><?= $s['nama_syarat']; ?></label>
                                <input type="file" name="file_<?= $s['id']; ?>" class="form-control" required>
                            </div>
                        <?php
                        }
                    } else {
                        ?>
                        <div class="mb-3">
                            <label class="fw-bold">Upload File (ZIP/PDF)</label>
                            <input type="file" name="file_default" class="form-control" required>
                        </div>
                    <?php } ?>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" name="simpan_pendaftaran" class="btn btn-primary">
                        Simpan
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
<?php
if (isset($_POST['simpan_pendaftaran'])) {

    $namalengkap = mysqli_real_escape_string($koneksi, $_POST['namalengkap']);
    $nim         = mysqli_real_escape_string($koneksi, $_POST['nim']);
    $prodi       = mysqli_real_escape_string($koneksi, $_POST['prodi']);
    $no_rekening = mysqli_real_escape_string($koneksi, $_POST['no_rekening']);
    $status      = 'Menunggu Konfirmasi';
    $tanggal     = date('Y-m-d');

    // 1. Insert Pendaftaran Data first
    mysqli_query($koneksi, "
        INSERT INTO pendaftaran 
        (user_id, beasiswa_id, namalengkap, nim, prodi, no_rekening, tanggal, status)
        VALUES 
        ('$user_id', '$beasiswa_id', '$namalengkap', '$nim', '$prodi', '$no_rekening', '$tanggal', '$status')
    ");

    $pendaftaran_id = mysqli_insert_id($koneksi);

    // 2. Handle File Uploads
    $qSyarat = mysqli_query($koneksi, "SELECT * FROM beasiswa_syarat WHERE beasiswa_id = '$beasiswa_id' AND tahap = 'Pendaftaran'");

    if (mysqli_num_rows($qSyarat) > 0) {
        while ($s = mysqli_fetch_assoc($qSyarat)) {
            $inputName = 'file_' . $s['id'];
            if (!empty($_FILES[$inputName]['name'])) {
                $file = $_FILES[$inputName]['name'];
                $tmp  = $_FILES[$inputName]['tmp_name'];
                $newName = time() . '_' . $s['id'] . '_' . $file;
                move_uploaded_file($tmp, "../uploads/" . $newName);

                mysqli_query($koneksi, "INSERT INTO pendaftaran_files (pendaftaran_id, syarat_id, file) VALUES ('$pendaftaran_id', '" . $s['id'] . "', '$newName')");
            }
        }
    } else {
        // Fallback for scholarships without dynamic requirements (using 'file_default')
        if (!empty($_FILES['file_default']['name'])) {
            $file = $_FILES['file_default']['name'];
            $tmp  = $_FILES['file_default']['tmp_name'];
            $newName = time() . '_DEF_' . $file;
            move_uploaded_file($tmp, "../uploads/" . $newName);

            // For backward compatibility, update the main table's 'file' column
            mysqli_query($koneksi, "UPDATE pendaftaran SET file = '$newName' WHERE id = '$pendaftaran_id'");
        }
    }

    $judulNotif = "Pendaftaran Beasiswa Baru";
    $pesanNotif = "Mahasiswa $namalengkap ($nim) telah mendaftar beasiswa dan menunggu konfirmasi.";

    $createdAt = date('Y-m-d H:i:s');

    $dosen = mysqli_query($koneksi, "SELECT id FROM users WHERE role = 'Admin'");

    while ($d = mysqli_fetch_assoc($dosen)) {
        $dosen_id = $d['id'];

        mysqli_query($koneksi, "
            INSERT INTO notifikasi (user_id, judul, pesan, status, created_at)
            VALUES ('$dosen_id', '$judulNotif', '$pesanNotif', 'Belum Dibaca', '$createdAt')
        ");
    }

    echo "<script>
        alert('Pendaftaran berhasil disimpan');
        window.location='index.php?page=pendaftaran&id=$beasiswa_id';
    </script>";
    exit;
}

if (isset($_POST['upload_lengkapi'])) {
    $id_pendaftaran = $_POST['id_pendaftaran'];

    // Check dynamic requirements
    // We need beasiswa_id. pendaftaran table has user_id, beasiswa_id.
    // Fetch beasiswa_id first
    $qCek = mysqli_query($koneksi, "SELECT beasiswa_id FROM pendaftaran WHERE id='$id_pendaftaran'");
    $dCek = mysqli_fetch_assoc($qCek);
    $beasiswa_id_target = $dCek['beasiswa_id'];

    $qSyarat = mysqli_query($koneksi, "SELECT * FROM beasiswa_syarat WHERE beasiswa_id = '$beasiswa_id_target' AND tahap = 'Lulus'");

    if (mysqli_num_rows($qSyarat) > 0) {
        while ($s = mysqli_fetch_assoc($qSyarat)) {
            $inputName = 'file_lengkap_' . $s['id'];
            if (!empty($_FILES[$inputName]['name'])) {
                $file = $_FILES[$inputName]['name'];
                $tmp  = $_FILES[$inputName]['tmp_name'];
                $newName = time() . '_LENGKAP_' . $s['id'] . '_' . $file;
                move_uploaded_file($tmp, "../uploads/" . $newName);

                mysqli_query($koneksi, "INSERT INTO pendaftaran_files (pendaftaran_id, syarat_id, file) VALUES ('$id_pendaftaran', '" . $s['id'] . "', '$newName')");
            }
        }
    } else {
        // Fallback
        if (!empty($_FILES['file_lengkapi_default']['name'])) {
            $file = $_FILES['file_lengkapi_default']['name'];
            $tmp  = $_FILES['file_lengkapi_default']['tmp_name'];
            $newName = time() . '_LENGKAPI_' . $file;
            move_uploaded_file($tmp, "../uploads/" . $newName);

            mysqli_query($koneksi, "UPDATE pendaftaran SET file_lengkapi = '$newName' WHERE id = '$id_pendaftaran'");
        }
    }

    echo "<script>
        alert('Berkas kelengkapan berhasil diupload');
        window.location='index.php?page=pendaftaran&id=$beasiswa_id_target';
    </script>";
    exit;
}

?>