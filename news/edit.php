<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include_once 'koneksi.php';

// Pastikan pengguna sudah login
if (!isset($_SESSION['id_user'])) {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location.href='login.php';</script>";
    exit;
}

$database = new Database();
$db = $database->getConnection();

// Ambil data konten berdasarkan ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$query = "SELECT * FROM news WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $id);
$stmt->execute();
$konten = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$konten) {
    echo "<script>alert('Data tidak ditemukan!'); window.location.href='?page=konten';</script>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    function sanitizeInput($input) {
        return htmlspecialchars(strip_tags(trim($input)));
    }

    $caption = sanitizeInput($_POST['caption']);
    $jenis_konten = sanitizeInput($_POST['jenis_konten']);
    $new_konten = $konten['konten'];
    $tanggal = date('Y-m-d H:i:s');

    if ($jenis_konten == 'gambar' || $jenis_konten == 'file') {
        if (!empty($_FILES['konten']['name'])) {
            $new_konten = uploadFile('konten');
        }
    } elseif ($jenis_konten == 'link') {
        $new_konten = sanitizeInput($_POST['konten']);
    }

    $updateSQL = "UPDATE news SET caption = :caption, jenis_konten = :jenis_konten, konten = :konten, tanggal = :tanggal WHERE id = :id";
    $stmt = $db->prepare($updateSQL);
    $stmt->bindParam(':caption', $caption);
    $stmt->bindParam(':jenis_konten', $jenis_konten);
    $stmt->bindParam(':konten', $new_konten);
    $stmt->bindParam(':tanggal', $tanggal);
    $stmt->bindParam(':id', $id);
    
    if ($stmt->execute()) {
        $_SESSION['hasil'] = true;
        $_SESSION['pesan'] = "Berhasil Mengupdate Data";
    } else {
        $_SESSION['hasil'] = false;
        $_SESSION['pesan'] = "Gagal Mengupdate Data";
    }
    echo "<meta http-equiv='refresh' content='0; url=?page=konten'>";
}

// Fungsi untuk upload file
function uploadFile($input_name) {
    if (!empty($_FILES[$input_name]['name'])) {
        $target_dir = "uploads/";
        $file_name = basename($_FILES[$input_name]["name"]);
        $file_name = preg_replace("/[^a-zA-Z0-9.\-_]/", "", $file_name);
        $target_file = $target_dir . time() . "_" . $file_name;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx'];
        if (!in_array($file_type, $allowed_types)) {
            $_SESSION['pesan'] = "Format file tidak diizinkan!";
            return null;
        }

        if (move_uploaded_file($_FILES[$input_name]["tmp_name"], $target_file)) {
            return $target_file;
        }
    }
    return null;
}
?>

<div class="container mt-4">
    <h3 class="text-center mb-3">Edit Konten</h3>
    <hr>
    <div class="card shadow">
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Caption</label>
                    <input type="text" name="caption" class="form-control" required value="<?= $konten['caption']; ?>">
                </div>
                <div class="form-group mb-2">
                    <label>Jenis Konten</label>
                    <select class="form-control" name="jenis_konten" required>
                        <option value="gambar" <?= ($konten['jenis_konten'] == 'gambar') ? 'selected' : ''; ?>>Gambar</option>
                        <option value="file" <?= ($konten['jenis_konten'] == 'file') ? 'selected' : ''; ?>>File</option>
                        <option value="link" <?= ($konten['jenis_konten'] == 'link') ? 'selected' : ''; ?>>Link</option>
                        <option value="kosong" <?= ($konten['jenis_konten'] == 'kosong') ? 'selected' : ''; ?>>Kosong</option>
                    </select>
                </div>
                <div class="mb-3" id="konten_input">
                    <label class="form-label">Konten</label>
                    <?php if ($konten['jenis_konten'] == 'gambar' || $konten['jenis_konten'] == 'file') : ?>
                        <input type="file" name="konten" class="form-control" accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx">
                        <p class="mt-2">File saat ini: <a href="<?= $konten['konten']; ?>" target="_blank">Lihat</a></p>
                    <?php elseif ($konten['jenis_konten'] == 'link') : ?>
                        <input type="text" name="konten" class="form-control" value="<?= $konten['konten']; ?>">
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="?page=tabel" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
</div>
