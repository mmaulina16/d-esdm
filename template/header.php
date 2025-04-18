<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIP2 GATRIK</title>

    <!-- Bootstrap CSS -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark px-3 w-100" style="background-color: #008B47;">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="assets/img/kalsel.png" alt="Logo Kalsel" class="me-2" style="max-height: 40px;">
                <strong>SIP2 GATRIK</strong>
            </a>

            <!-- Navbar Toggle untu mobile -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Menu Navbar -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto d-flex align-items-center"> <!-- Menjadikan elemen sejajar -->
                    <!-- LONCENG NOTIFIKASI -->
                    <li class="nav-item">
                        <?php
                        include 'koneksi.php';
                        $database = new Database();
                        $conn = $database->getConnection();

                        // Query untuk menghitung jumlah laporan_semester yang berstatus 'diajukan'
                        $queryLaporan = "SELECT COUNT(*) as total FROM laporan_semester WHERE status = 'diajukan'";
                        $stmtLaporan = $conn->prepare($queryLaporan);
                        $stmtLaporan->execute();
                        $resultLaporan = $stmtLaporan->fetch(PDO::FETCH_ASSOC);
                        $jumlahLaporanDiajukan = $resultLaporan['total'];

                        // Query untuk menghitung jumlah pengguna yang berstatus 'diajukan'
                        $queryPengguna = "SELECT COUNT(*) as total FROM users WHERE status = 'diajukan'";
                        $stmtPengguna = $conn->prepare($queryPengguna);
                        $stmtPengguna->execute();
                        $resultPengguna = $stmtPengguna->fetch(PDO::FETCH_ASSOC);
                        $jumlahPenggunaDiajukan = $resultPengguna['total'];

                        // Total notifikasi yang diajukan
                        $totalNotifikasi = $jumlahLaporanDiajukan + $jumlahPenggunaDiajukan;
                        ?>

                        <?php if ($_SESSION['role'] == 'admin') { ?> <!-- hanya admin yang bisa mengakses menu ini -->
                            <a href="?page=notifikasi" class="nav-link position-relative me-3">
                                <i class="fas fa-bell fa-lg"></i>
                                <?php if ($totalNotifikasi > 0): ?>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        <?= $totalNotifikasi; ?>
                                        <span class="visually-hidden">unread messages</span>
                                    </span>
                                <?php endif; ?>
                            </a>
                        <?php } ?>
                    </li>

                    <!-- NAMA USER -->
                    <li class="nav-item dropdown d-flex align-items-center"> <!-- Tambahkan d-flex align-items-center -->
                        <!-- Dropdown User -->
                        <a class="nav-link dropdown-toggle" href="#" id="drop2" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fas fa-user me-1"></i> <?= $_SESSION['username']; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="drop2">
                            <li>
                                <a href="?page=pengguna_edit_admin&id_user=<?= $_SESSION['id_user']; ?>" class="dropdown-item">
                                    <i class="fas fa-user-circle fs-6"></i> Profil Saya
                                </a>
                            </li>
                            <li>
                                <a href="?page=edit_password&id_user=<?= $_SESSION['id_user']; ?>" class="dropdown-item">
                                    <i class="fas fa-key fs-6"></i> Ganti Password
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a href="login/logout.php" onclick="return confirm('Anda yakin ingin logout?')"
                                    class="dropdown-item text-danger">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Bootstrap JS -->
    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>