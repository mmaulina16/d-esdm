<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include "koneksi.php";

// Pastikan hanya umum yang tidak bisa mengakses halaman ini
if (!isset($_SESSION['role']) || ($_SESSION['role'] == 'umum' && $_SESSION['role'] == 'kementerian')) {
    echo "<script>alert('Akses ditolak!'); window.location.href='index.php';</script>";
    exit;
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Ambil semua konten
    $sql = "SELECT * FROM news ORDER BY tanggal DESC";
    $stmt = $conn->query($sql);
    $konten_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

    <div class="container mt-4">
        <h2 class="text-center mb-3">Kelola Konten</h2>
        <hr>
        <div class="card shadow" style="overflow-x: auto; max-height: calc(100vh - 150px); overflow-y: auto;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <a href="?page=dashboard" class="btn btn-secondary">Kembali ke Dashboard</a>
                    <a href="?page=upload" class="btn btn-primary">Tambah Konten</a>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>No.</th>
                                <th>Jenis</th>
                                <th>Caption</th>
                                <th>Konten</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            foreach ($konten_list as $konten) : ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo ucfirst($konten['jenis_konten']); ?></td>
                                    <td><?php echo htmlspecialchars($konten['caption']); ?></td>
                                    <td>
                                        <?php
                                        if ($konten['jenis_konten'] === 'gambar') : ?>
                                            <img src="<?php echo htmlspecialchars($konten['konten']); ?>" alt="Gambar Konten" width="100">
                                        <?php elseif ($konten['jenis_konten'] === 'link') : ?>
                                            <a href="<?php echo htmlspecialchars($konten['konten']); ?>" target="_blank">
                                                🌐 Lihat Link
                                            </a>
                                        <?php elseif ($konten['jenis_konten'] === 'file') : ?>
                                            <a href="<?php echo htmlspecialchars($konten['konten']); ?>" target="_blank" class="btn btn-sm btn-info">
                                                <i class="fas fa-file-alt"></i> Lihat
                                            </a>
                                        <?php else : ?>
                                            <p class="text-muted">Tidak ada konten</p>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $konten['tanggal']; ?></td>
                                    <td>
                                        <a href="?page=edit_konten&id=<?php echo $konten['id']; ?>" class="btn btn-warning btn-sm border-1">Edit</a>
                                        <a href="?page=hapus_konten&id=<?php echo $konten['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus konten ini?');">Hapus</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>