<?php
$id = $_GET['id'];

$data = mysqli_query($koneksi, "SELECT * FROM beasiswa WHERE id = '$id'");
$row = mysqli_fetch_assoc($data);

if (!$row) {
    echo "<script>alert('Data tidak ditemukan'); window.location='index.php?page=beasiswa';</script>";
    exit;
}

// Fetch existing requirements
$syarat_pendaftaran = [];
$syarat_lulus = [];
$qSyarat = mysqli_query($koneksi, "SELECT * FROM beasiswa_syarat WHERE beasiswa_id = '$id'");
while ($s = mysqli_fetch_assoc($qSyarat)) {
    if ($s['tahap'] == 'Pendaftaran') {
        $syarat_pendaftaran[] = $s;
    } else {
        $syarat_lulus[] = $s;
    }
}
?>

<div class="container">
    <div class="page-inner">

        <div class="page-header">
            <h3 class="fw-bold">Edit Beasiswa</h3>
        </div>

        <div class="card mt-4">
            <div class="card-body">

                <form method="POST" enctype="multipart/form-data">

                    <div class="mb-3">
                        <label class="fw-bold">Nama Beasiswa</label>
                        <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($row['nama']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Deskripsi</label>
                        <textarea name="deskripsi" id="deskripsi" class="form-control" rows="4" required><?= htmlspecialchars($row['deskripsi']); ?></textarea>
                        <script>CKEDITOR.replace('deskripsi');</script>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Syarat Umum (Teks)</label>
                        <textarea name="syarat" class="form-control" rows="3" required><?= htmlspecialchars($row['syarat']); ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="fw-bold">Tanggal Mulai</label>
                                <input type="date" name="tanggal" class="form-control" value="<?= $row['tanggal']; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="fw-bold">Tanggal Akhir</label>
                                <input type="date" name="tanggal_akhir" class="form-control" value="<?= $row['tanggal_akhir']; ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Upload Panduan Baru (PDF)</label>
                        <input type="file" name="file_beasiswa" class="form-control">
                        <small class="text-muted">Biarkan kosong jika tidak ingin mengubah file.</small>
                        <?php if(!empty($row['file_beasiswa'])): ?>
                            <div class="mt-1"><a href="../uploads/<?= $row['file_beasiswa']; ?>" target="_blank">Lihat File Saat Ini</a></div>
                        <?php endif; ?>
                    </div>

                    <hr>
                    <h5 class="fw-bold mb-3">Persyaratan Dokumen</h5>
                    <div id="deleted-syarat-container"></div>

                    <div class="mb-3">
                        <label class="fw-bold">Dokumen Saat Pendaftaran</label>
                        <div id="syarat-pendaftaran-container">
                            <?php foreach ($syarat_pendaftaran as $sp): ?>
                                <div class="input-group mb-2">
                                    <input type="hidden" name="existing_syarat_id[]" value="<?= $sp['id']; ?>">
                                    <input type="text" name="existing_syarat_nama[<?= $sp['id']; ?>]" class="form-control" value="<?= htmlspecialchars($sp['nama_syarat']); ?>" required>
                                    <button type="button" class="btn btn-danger btn-remove" onclick="removeSyarat(this, <?= $sp['id']; ?>)">-</button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary mt-1" onclick="addSyarat('pendaftaran')">
                            + Tambah Dokumen
                        </button>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Dokumen Saat Lulus (Kelengkapan)</label>
                        <div id="syarat-lulus-container">
                            <?php foreach ($syarat_lulus as $sl): ?>
                                <div class="input-group mb-2">
                                    <input type="hidden" name="existing_syarat_id[]" value="<?= $sl['id']; ?>">
                                    <input type="text" name="existing_syarat_nama[<?= $sl['id']; ?>]" class="form-control" value="<?= htmlspecialchars($sl['nama_syarat']); ?>" required>
                                    <button type="button" class="btn btn-danger btn-remove" onclick="removeSyarat(this, <?= $sl['id']; ?>)">-</button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary mt-1" onclick="addSyarat('lulus')">
                            + Tambah Dokumen
                        </button>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="index.php?page=beasiswa" class="btn btn-secondary">Kembali</a>
                        <button type="submit" name="update_beasiswa" class="btn btn-primary">Update</button>
                    </div>

                </form>

            </div>
        </div>

    </div>
</div>

<script>
    function addSyarat(type) {
        let container = document.getElementById('syarat-' + type + '-container');
        let div = document.createElement('div');
        div.className = 'input-group mb-2';
        div.innerHTML = `
            <input type="text" name="new_syarat_${type}[]" class="form-control" placeholder="Nama Dokumen Baru" required>
            <button type="button" class="btn btn-danger btn-remove" onclick="this.parentElement.remove()">-</button>
        `;
        container.appendChild(div);
    }

    function removeSyarat(btn, id) {
        let container = document.getElementById('deleted-syarat-container');
        let input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'delete_syarat[]';
        input.value = id;
        container.appendChild(input);
        btn.parentElement.remove();
    }
</script>

<?php
if (isset($_POST['update_beasiswa'])) {

    $nama       = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $deskripsi  = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $syarat     = mysqli_real_escape_string($koneksi, $_POST['syarat']);
    $tanggal    = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
    $tanggal_akhir = mysqli_real_escape_string($koneksi, $_POST['tanggal_akhir']);

    // Handle File
    $sql_file = "";
    if (!empty($_FILES['file_beasiswa']['name'])) {
        $file = $_FILES['file_beasiswa']['name'];
        $tmp = $_FILES['file_beasiswa']['tmp_name'];
        $file_beasiswa = time() . '_' . $file;
        move_uploaded_file($tmp, "../uploads/" . $file_beasiswa);
        $sql_file = ", file_beasiswa = '$file_beasiswa'";
    }

    mysqli_query($koneksi, "
        UPDATE beasiswa SET
            nama = '$nama',
            deskripsi = '$deskripsi',
            syarat = '$syarat',
            tanggal = '$tanggal',
            tanggal_akhir = '$tanggal_akhir'
            $sql_file
        WHERE id = '$id'
    ");

    // 1. Update Existing
    if (isset($_POST['existing_syarat_id'])) {
        foreach ($_POST['existing_syarat_id'] as $eid) {
            $ename = mysqli_real_escape_string($koneksi, $_POST['existing_syarat_nama'][$eid]);
            mysqli_query($koneksi, "UPDATE beasiswa_syarat SET nama_syarat = '$ename' WHERE id = '$eid'");
        }
    }

    // 2. Delete Marked
    if (isset($_POST['delete_syarat'])) {
        foreach ($_POST['delete_syarat'] as $did) {
            mysqli_query($koneksi, "DELETE FROM beasiswa_syarat WHERE id = '$did'");
        }
    }

    // 3. Add New Pendaftaran
    if (isset($_POST['new_syarat_pendaftaran'])) {
        foreach ($_POST['new_syarat_pendaftaran'] as $sy) {
            if (!empty($sy)) {
                $sy = mysqli_real_escape_string($koneksi, $sy);
                mysqli_query($koneksi, "INSERT INTO beasiswa_syarat (beasiswa_id, nama_syarat, tahap) VALUES ('$id', '$sy', 'Pendaftaran')");
            }
        }
    }

    // 4. Add New Lulus
    if (isset($_POST['new_syarat_lulus'])) {
        foreach ($_POST['new_syarat_lulus'] as $sy) {
            if (!empty($sy)) {
                $sy = mysqli_real_escape_string($koneksi, $sy);
                mysqli_query($koneksi, "INSERT INTO beasiswa_syarat (beasiswa_id, nama_syarat, tahap) VALUES ('$id', '$sy', 'Lulus')");
            }
        }
    }

    echo "<script>alert('Data berhasil diupdate'); window.location='index.php?page=beasiswa';</script>";
    exit;
}
?>