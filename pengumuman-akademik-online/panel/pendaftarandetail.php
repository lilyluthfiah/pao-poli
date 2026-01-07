<?php
$id = $_GET['id'];

$data = mysqli_query($koneksi, "
    SELECT pendaftaran.*, beasiswa.nama AS nama_beasiswa
    FROM pendaftaran
    INNER JOIN beasiswa ON pendaftaran.beasiswa_id = beasiswa.id
    WHERE pendaftaran.id = '$id'
");
$row = mysqli_fetch_assoc($data);
?>

<div class="container">
    <div class="page-inner">

        <div class="page-header">
            <h3 class="fw-bold">Detail Pendaftaran Beasiswa</h3>
        </div>

        <div class="card mt-3">
            <div class="card-body">

                <table class="table table-bordered">
                    <tr>
                        <th width="200">Nama Beasiswa</th>
                        <td><?= $row['nama_beasiswa']; ?></td>
                    </tr>
                    <tr>
                        <th>Nama Lengkap</th>
                        <td><?= htmlspecialchars($row['namalengkap']); ?></td>
                    </tr>
                    <tr>
                        <th>NIM</th>
                        <td><?= htmlspecialchars($row['nim']); ?></td>
                    </tr>
                    <tr>
                        <th>Program Studi</th>
                        <td><?= htmlspecialchars($row['prodi']); ?></td>
                    </tr>
                    <tr>
                        <th>File</th>
                        <td>
                            <?php
                            $qFiles = mysqli_query($koneksi, "
                                SELECT pendaftaran_files.*, beasiswa_syarat.nama_syarat, beasiswa_syarat.tahap 
                                FROM pendaftaran_files 
                                LEFT JOIN beasiswa_syarat ON pendaftaran_files.syarat_id = beasiswa_syarat.id
                                WHERE pendaftaran_id = '$id'
                            ");

                            if (mysqli_num_rows($qFiles) > 0) {
                                echo '<ul class="list-group list-group-flush">';
                                while ($f = mysqli_fetch_assoc($qFiles)) {
                                    $namaSyarat = $f['nama_syarat'] ? $f['nama_syarat'] . " (" . $f['tahap'] . ")" : "Berkas Lain";
                                    echo '
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <span><i class="fa fa-file-pdf-o text-danger me-2"></i> ' . $namaSyarat . '</span>
                                        <a href="../uploads/' . $f['file'] . '" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fa fa-download"></i></a>
                                    </li>';
                                }
                                echo '</ul>';
                            } else {
                                // Fallback Layout
                                if (!empty($row['file'])) {
                                    echo '<div class="mb-2"><strong>Berkas Pendaftaran:</strong> <a href="../uploads/' . $row['file'] . '" target="_blank" class="btn btn-sm btn-info ms-2"><i class="fa fa-eye"></i> Lihat</a></div>';
                                }
                                if (!empty($row['file_lengkapi'])) {
                                    echo '<div class="mb-2"><strong>Berkas Kelengkapan:</strong> <a href="../uploads/' . $row['file_lengkapi'] . '" target="_blank" class="btn btn-sm btn-info ms-2"><i class="fa fa-eye"></i> Lihat</a></div>';
                                }
                                if (empty($row['file']) && empty($row['file_lengkapi'])) {
                                    echo '-';
                                }
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Tanggal Daftar</th>
                        <td><?= $row['tanggal']; ?></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <span class="badge bg-secondary"><?= $row['status']; ?></span>
                        </td>
                    </tr>
                </table>

                <?php if ($_SESSION['user']['role'] == 'Admin'): ?>

                    <hr>

                    <!-- UPDATE STATUS -->
                    <form method="POST" class="mt-3">
                        <div class="mb-3">
                            <label class="fw-bold">Update Status</label>
                            <select name="status" class="form-control" required>
                                <option value="Menunggu Konfirmasi"
                                    <?= $row['status'] == 'Menunggu Konfirmasi' ? 'selected' : ''; ?>>
                                    Menunggu Konfirmasi
                                </option>
                                <option value="Diterima"
                                    <?= $row['status'] == 'Diterima' ? 'selected' : ''; ?>>
                                    Diterima
                                </option>
                                <option value="Ditolak"
                                    <?= $row['status'] == 'Ditolak' ? 'selected' : ''; ?>>
                                    Ditolak
                                </option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="index.php?page=pendaftaran&id=<?= $row['beasiswa_id']; ?>"
                                class="btn btn-secondary">
                                Kembali
                            </a>
                            <button type="submit" name="update_status"
                                class="btn btn-success">
                                Update Status
                            </button>
                        </div>
                    </form>
                <?php endif; ?>

            </div>
        </div>

    </div>
</div>

<?php
if (isset($_POST['update_status'])) {

    $status = mysqli_real_escape_string($koneksi, $_POST['status']);

    mysqli_query($koneksi, "
        UPDATE pendaftaran SET status = '$status'
        WHERE id = '$id'
    ");

    $user_pendaftar = $row['user_id'];
    $nama_beasiswa  = $row['nama_beasiswa'];

    $judulNotif = "Status Pendaftaran Beasiswa";
    $pesanNotif = "Status pendaftaran Anda untuk beasiswa \"$nama_beasiswa\" telah diperbarui menjadi: $status.";

    $createdAt = date('Y-m-d H:i:s');

    mysqli_query($koneksi, "
        INSERT INTO notifikasi (user_id, judul, pesan, status, created_at)
        VALUES ('$user_pendaftar', '$judulNotif', '$pesanNotif', 'Belum Dibaca', '$createdAt')
    ");

    echo "<script>
        alert('Status berhasil diperbarui');
        window.location='index.php?page=pendaftarandetail&id=$id';
    </script>";
    exit;
}
?>