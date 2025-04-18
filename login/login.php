<?php
session_start();
include '../koneksi.php';

$error = '';

if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}

// Proteksi CSRF
if (!isset($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

// if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
//     die("Akses hanya diperbolehkan melalui koneksi aman (HTTPS).");
// }


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_SESSION['login_attempts'] >= 5) {
        $error = "Terlalu banyak percobaan login. Coba lagi nanti.";
    } else {
        if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['token']) {
            die("CSRF Detected!");
        }

        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        if (!empty($username) && !empty($password)) {
            $db = new Database();
            $pdo = $db->getConnection();
            $sql = "SELECT * FROM users WHERE (username = :username OR email = :username)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":username", $username, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                if ($user['status'] !== 'diverifikasi') {
                    $error = "Akun Anda belum diverifikasi. Silakan hubungi admin di <a href='https://wa.me/628115128607' target='_blank'>WhatsApp</a>.";
                } else {
                    if (password_verify($password, $user['password'])) {
                        session_regenerate_id(true); // Tambahkan keamanan sesi
                        $_SESSION['id_user'] = $user['id_user'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['role'] = $user['role'];
                        $_SESSION['login_attempts'] = 0;

                        header("Location: ../index.php");
                        exit();
                    } else {
                        $_SESSION['login_attempts'] += 1;
                        $error = "Username atau password salah!";
                        file_put_contents("log_login.txt", date('Y-m-d H:i:s') . " - Gagal login: $username\n", FILE_APPEND);
                    }
                }
            } else {
                $_SESSION['login_attempts'] += 1;
                $error = "Username atau password salah!";
            }
        } else {
            $error = "Harap isi username/email dan password!";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/login.css">
</head>

<body class="d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow p-4" style="width: 350px;">
        <h3 class="text-center">Login</h3>
        <hr>
        <?php if (!empty($error)) : ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
        <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">

            <div class="mb-3">
                <label for="username" class="form-label">Username atau Email</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan username atau email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>

        <div class="text-center mt-3">
            <a href="daftar.php">Belum punya akun?</a>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>
