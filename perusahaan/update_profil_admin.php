<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include "koneksi.php";

// Pastikan pengguna sudah login
if (!isset($_SESSION['id_user'])) {
    echo "<script>alert('Anda harus login terlebih dahulu!'); window.location.href='login/login.php';</script>";
    exit();
}

if (!isset($_GET['id_profil'])) {
    echo "<script>alert('Data tidak ditemukan!'); window.location.href='?page=profil_admin';</script>";
    exit();
}

$id_profil = $_GET['id_profil'];

$database = new Database();
$pdo = $database->getConnection(); // Dapatkan koneksi PDO
// Ambil data profil perusahaan
$sql = "SELECT * FROM profil WHERE id_profil = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_profil]);
$profil = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    function sanitize_input($data)
    {
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }

    $nama_perusahaan = sanitize_input($_POST['nama_perusahaan']);
    $kabupaten = sanitize_input($_POST['kabupaten']);
    $alamat = sanitize_input($_POST['alamat']);
    $jenis_usaha = sanitize_input($_POST['jenis_usaha']);
    $no_telp_kantor = sanitize_input($_POST['no_telp_kantor']);
    $no_fax = sanitize_input($_POST['no_fax']);
    $tenaga_teknik = sanitize_input($_POST['tenaga_teknik']);
    $nama = sanitize_input($_POST['nama']);
    $no_hp = sanitize_input($_POST['no_hp']);
    $email = sanitize_input($_POST['email']);

    if (!preg_match('/^[0-9\+]+$/', $no_telp_kantor) || !preg_match('/^[0-9\+]+$/', $no_hp)) {
        echo "<script>alert('Kontak hanya boleh berisi angka dan tanda +!');</script>";
    } else {
        $sql = "UPDATE profil SET nama_perusahaan=?, kabupaten=?, alamat=?, jenis_usaha=?, no_telp_kantor=?, no_fax=?, tenaga_teknik=?, nama=?, no_hp=?, email=? WHERE id_profil=?";
        $stmt = $pdo->prepare($sql);
        $success = $stmt->execute([$nama_perusahaan, $kabupaten, $alamat, $jenis_usaha, $no_telp_kantor, $no_fax, $tenaga_teknik, $nama, $no_hp, $email, $id_profil]);

        if ($success) {
            echo "<script>alert('Profil berhasil diperbarui!'); window.location.href='?page=profil_admin';</script>";
        } else {
            echo "<script>alert('Gagal memperbarui profil. Silakan coba lagi.');</script>";
        }
    }
}
?>

<!-- UPDATE PROFIL PERUSAHAAN -->
<div class="container mt-4">
    <h3 class="text-center mb-3">Update Profil Perusahaan</h3>
    <hr>
    <div class="card shadow">
        <div class="card-body">
            <form method="POST">
                <div class="form-group mb-2">
                    <label>Nama Perusahaan</label>
                    <input type="text" class="form-control" name="nama_perusahaan" required value="<?php echo $profil['nama_perusahaan']; ?>">
                </div>

                <div class="form-group mb-2">
                    <label>Kabupaten/Kota</label>
                    <select class="form-control" name="kabupaten" required>
                        <option value="<?php echo $profil['kabupaten']; ?>" selected><?php echo $profil['kabupaten']; ?></option>
                        <option value="Balangan">Balangan</option>
                        <option value="Banjar">Banjar</option>
                        <option value="Barito Kuala">Barito Kuala</option>
                        <option value="Hulu Sungai Selatan">Hulu Sungai Selatan</option>
                        <option value="Hulu Sungai Tengah">Hulu Sungai Tengah</option>
                        <option value="Hulu Sungai Utara">Hulu Sungai Utara</option>
                        <option value="Kotabaru">Kotabaru</option>
                        <option value="Tabalong">Tabalong</option>
                        <option value="Tanah Bumbu">Tanah Bumbu</option>
                        <option value="Tanah Laut">Tanah Laut</option>
                        <option value="Tapin">Tapin</option>
                        <option value="Kota Banjarmasin">Banjarmasin (Kota)</option>
                        <option value="Kota Banjarbaru">Banjarbaru (Kota)</option>
                    </select>
                </div>

                <div class="form-group mb-2">
                    <label>Alamat</label>
                    <textarea class="form-control" name="alamat" required><?php echo $profil['alamat']; ?></textarea>
                </div>

                <div class="form-group mb-2">
                    <label>Jenis Usaha</label>
                    <select class="form-control" name="jenis_usaha" required>
                        <option value="<?php echo $profil['jenis_usaha']; ?>" selected><?php echo $profil['jenis_usaha']; ?></option>
                        <option value="Kesehatan dan Rumah Sakit">Kesehatan dan Rumah Sakit</option>
                        <option value="Industri dan Manufaktur">Industri dan Manufaktur</option>
                        <option value="Restoran">Restoran</option>
                        <option value="Event Organizer">Event Organizer</option>
                        <option value="Pendidikan">Pendidikan</option>
                        <option value="Perdagangan">Perdagangan</option>
                        <option value="Telekomunikasi dan Teknologi">Telekomunikasi dan Teknologi</option>
                        <option value="Transportasi">Transportasi</option>
                        <option value="Logistik">Logistik</option>
                        <option value="Pertanian">Pertanian</option>
                        <option value="Perikanan">Perikanan</option>
                        <option value="Perternakan">Perternakan</option>
                        <option value="Hiburan dan Wisata">Hiburan dan Wisata</option>
                        <option value="Lingkungan">Lingkungan</option>
                        <option value="Konstruksi dan Infrastruktur">Konstruksi dan Infrastruktur</option>
                        <option value="Jasa">Jasa</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <div class="form-group mb-2">
                    <label>Nomor Telepon Kantor</label>
                    <input type="text" class="form-control" name="no_telp_kantor" required value="<?php echo $profil['no_telp_kantor']; ?>">
                </div>

                <div class="form-group mb-2">
                    <label>No. Fax</label>
                    <input type="text" class="form-control" name="no_fax" required value="<?php echo $profil['no_fax']; ?>">
                </div>

                <div class="form-group mb-2">
                    <label>Tenaga Teknik</label>
                    <input type="text" class="form-control" name="tenaga_teknik" required value="<?php echo $profil['tenaga_teknik']; ?>">
                </div>

                <div class="card-header mt-4">
                    <h6>Kontak Person</h6>
                </div>
                <div class="form-group mt-2 mb-2">
                    <label>Nama</label>
                    <input type="text" class="form-control" name="nama" required value="<?php echo $profil['nama']; ?>">
                </div>

                <div class="form-group mb-2">
                    <label>Nomor HP</label>
                    <input type="text" class="form-control" name="no_hp" required value="<?php echo $profil['no_hp']; ?>">
                </div>

                <div class="form-group mb-2">
                    <label>Email</label>
                    <input type="email" class="form-control" name="email" required value="<?php echo $profil['email']; ?>">
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-warning">Simpan Perubahan</button>
                    <a href="?page=profil_admin" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>