<?php
$id = $_GET['id'];

$data = mysqli_query($koneksi, "SELECT * FROM pendaftaran WHERE id = '$id'");
$row = mysqli_fetch_assoc($data);
$beasiswa_id = $row['beasiswa_id'];

// Check dynamic requirements
$qSyarat = mysqli_query($koneksi, "SELECT * FROM beasiswa_syarat WHERE beasiswa_id = '$beasiswa_id' AND tahap = 'Pendaftaran'");
$isDynamic = (mysqli_num_rows($qSyarat) > 0);
?>

<div class="container">
    <div class="page-inner">

        <div class="page-header">
            <h3 class="fw-bold">Edit Pendaftaran Beasiswa</h3>
        </div>

        <div class="card mt-3">
            <div class="card-body">

                <form method="POST" enctype="multipart/form-data">

                    <div class="mb-3">
                        <label class="fw-bold">Nama Lengkap</label>
                        <input type="text" name="namalengkap" value="<?= htmlspecialchars($row['namalengkap']); ?>" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">NIM</label>
                        <input type="text" name="nim" value="<?= htmlspecialchars($row['nim']); ?>" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Program Studi</label>
                        <input type="text" name="prodi" value="<?= htmlspecialchars($row['prodi']); ?>" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Nomor Rekening</label>
                        <input type="text" name="no_rekening" value="<?= htmlspecialchars($row['no_rekening']); ?>" class="form-control" required>
                    </div>

                    <hr>
                    <h5 class="fw-bold">Update Berkas</h5>

                    <?php if ($isDynamic): ?>
                        <?php while ($s = mysqli_fetch_assoc($qSyarat)): ?>
                            <?php 
                                // Cek file yg sudah ada untuk syarat ini
                                $qFileExisting = mysqli_query($koneksi, "SELECT * FROM pendaftaran_files WHERE pendaftaran_id = '$id' AND syarat_id = '".$s['id']."'");
                                $fExisting = mysqli_fetch_assoc($qFileExisting);
                            ?>
                            <div class="mb-3">
                                <label class="fw-bold"><?= $s['nama_syarat']; ?></label><br>
                                <?php if ($fExisting): ?>
                                    <div class="mb-1">
                                        <a href="../uploads/<?= $fExisting['file']; ?>" target="_blank" class="btn btn-sm btn-outline-info">
                                            <i class="fa fa-file"></i> Lihat File Saat Ini
                                        </a>
                                    </div>
                                <?php endif; ?>
                                <input type="file" name="file_syarat_<?= $s['id']; ?>" class="form-control">
                                <small class="text-muted">Kosongkan jika tidak ingin mengubah file ini.</small>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <!-- Fallback Legacy -->
                         <div class="mb-3">
                            <label class="fw-bold">File Berkas (Legacy)</label><br>
                            <?php if (!empty($row['file'])): ?>
                                <a href="../uploads/<?= $row['file']; ?>" target="_blank" class="btn btn-sm btn-info mb-2">
                                    <i class="fa fa-file"></i> Lihat File Lama
                                </a>
                            <?php endif; ?>
                            <input type="file" name="file_default" class="form-control">
                            <small class="text-muted">Kosongkan jika tidak diganti</small>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="index.php?page=pendaftaran&id=<?= $row['beasiswa_id']; ?>" class="btn btn-secondary">Kembali</a>
                        <button type="submit" name="update_pendaftaran" class="btn btn-primary">Simpan Perubahan</button>
                    </div>

                </form>

            </div>
        </div>

    </div>
</div>

<?php
if (isset($_POST['update_pendaftaran'])) {

    $namalengkap = mysqli_real_escape_string($koneksi, $_POST['namalengkap']);
    $nim         = mysqli_real_escape_string($koneksi, $_POST['nim']);
    $prodi       = mysqli_real_escape_string($koneksi, $_POST['prodi']);
    $no_rekening = mysqli_real_escape_string($koneksi, $_POST['no_rekening']);

    // Update Data
    mysqli_query($koneksi, "
        UPDATE pendaftaran SET
            namalengkap = '$namalengkap',
            nim = '$nim',
            prodi = '$prodi',
            no_rekening = '$no_rekening'
        WHERE id = '$id'
    ");

    // Update Files (Dynamic)
    $qSyaratUpdate = mysqli_query($koneksi, "SELECT * FROM beasiswa_syarat WHERE beasiswa_id = '$beasiswa_id' AND tahap = 'Pendaftaran'");
    if (mysqli_num_rows($qSyaratUpdate) > 0) {
        while ($s = mysqli_fetch_assoc($qSyaratUpdate)) {
            $inputName = 'file_syarat_' . $s['id'];
            if (!empty($_FILES[$inputName]['name'])) {
                // Upload New
                $file = $_FILES[$inputName]['name'];
                $tmp  = $_FILES[$inputName]['tmp_name'];
                $newName = time() . '_' . $s['id'] . '_' . $file;
                move_uploaded_file($tmp, "../uploads/" . $newName);

                // Check exists check
                $qCheck = mysqli_query($koneksi, "SELECT id, file FROM pendaftaran_files WHERE pendaftaran_id='$id' AND syarat_id='".$s['id']."'");
                if (mysqli_num_rows($qCheck) > 0) {
                    // Update
                    $dCheck = mysqli_fetch_assoc($qCheck);
                    // allow keep old file not deleted physically just logic update? Or delete?
                    // Safe logic: Update record
                    mysqli_query($koneksi, "UPDATE pendaftaran_files SET file = '$newName' WHERE id = '".$dCheck['id']."'");
                } else {
                    // Insert
                    mysqli_query($koneksi, "INSERT INTO pendaftaran_files (pendaftaran_id, syarat_id, file) VALUES ('$id', '".$s['id']."', '$newName')");
                }
            }
        }
    } else {
        // Fallback Legacy
        if (!empty($_FILES['file_default']['name'])) {
             $file    = $_FILES['file_default']['name'];
             $tmp     = $_FILES['file_default']['tmp_name'];
             $newName = time() . '_DEF_' . $file;
             move_uploaded_file($tmp, "../uploads/" . $newName);
             
             mysqli_query($koneksi, "UPDATE pendaftaran SET file = '$newName' WHERE id = '$id'");
        }
    }

    echo "<script>
        alert('Data berhasil diupdate');
        window.location='index.php?page=pendaftaran&id={$row['beasiswa_id']}';
    </script>";
    exit;
}
?>