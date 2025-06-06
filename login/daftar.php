<?php
include '../koneksi.php'; // Pastikan koneksi tersedia

// Variabel untuk menyimpan pesan kesalahan
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash password menggunakan Bcrypt
    $no_hp = htmlspecialchars($_POST['no_hp']);
    $role = "umum"; // Otomatis diisi "umum"
    $status = "diajukan"; // Otomatis diisi "diajukan"

    try {
        $db = new Database();
        $conn = $db->getConnection();

        // Cek apakah email sudah terdaftar
        $stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            $error_message = "Email sudah terdaftar!";
        } else {
            // Cek apakah username sudah terdaftar
            $stmt = $conn->prepare("SELECT username FROM users WHERE username = ?");
            $stmt->execute([$username]);

            if ($stmt->rowCount() > 0) {
                $error_message = "Username sudah terdaftar!";
            } else {
                // Query untuk menambahkan user baru
                $sql = "INSERT INTO users (username, email, password, no_hp, role, status) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$username, $email, $password, $no_hp, $role, $status]);

                echo "<script>alert('Pendaftaran berhasil!'); window.location='login.php';</script>";
                exit; // Pastikan untuk keluar setelah redirect
            }
        }
    } catch (PDOException $e) {
        $error_message = 'Gagal mendaftar: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun</title>
    <link rel="icon" href="../assets/img/kalsel.png" type="image/png">
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/login.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="d-flex justify-content-center align-items-center vh-100 bg-light">
    <div class="card shadow p-4" style="width: 450px;">
        <div class="text-center mb-3">
            <img src="../assets/img/kalsel.png" alt="Logo" style="width: 50px;">
        </div>
        <h3 class="text-center">Daftar E-WASDAL GATRIK</h3>
        <hr>
        <form method="post">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input name="username" type="text" class="form-control" id="username" placeholder="Masukkan username" required>
                <?php if (strpos($error_message, 'Username') !== false): ?>
                    <div class="text-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input name="email" type="email" class="form-control" id="email" placeholder="Masukkan email" required>
                <?php if (strpos($error_message, 'Email') !== false): ?>
                    <div class="text-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" required>
                    <span class="input-group-text" onclick="togglePassword()" style="cursor:pointer;">
                        <i class="bi bi-eye-slash" id="toggleIcon"></i>
                    </span>
                </div>
            </div>
            <div class="mb-3">
                <label for="no_hp" class="form-label">No. HP</label>
                <input name="no_hp" type="number" pattern="[0-9]+" class="form-control" id="no_hp" placeholder="Masukkan nomor handphone" required>
                <?php if (strpos($error_message, 'No. HP') !== false): ?>
                    <div class="text-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>
            </div>
            <button type="submit" class="btn custom-btn w-100">Daftar</button>
        </form>
        <div class="text-center mt-3">
            <a href="login.php">Sudah punya akun?</a>
        </div>
    </div>
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script>
function togglePassword() {
    const passwordField = document.getElementById("password");
    const toggleIcon = document.getElementById("toggleIcon");

    const type = passwordField.getAttribute("type") === "password" ? "text" : "password";
    passwordField.setAttribute("type", type);

    toggleIcon.classList.toggle("bi-eye");
    toggleIcon.classList.toggle("bi-eye-slash");
}
</script>
</body>

</html>