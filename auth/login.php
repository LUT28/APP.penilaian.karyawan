<?php
session_start();
require_once '../config/koneksi.php';
require_once '../config/keamanan.php';

$db = new Database();
$koneksi = $db->getKoneksi();

// Fungsi buat admin default
function buatAdminDefault($koneksi) {
    $cek = $koneksi->prepare("SELECT * FROM users WHERE role = 'admin'");
    if (!$cek) {
        die("Gagal prepare cek admin: " . $koneksi->error);
    }

    $cek->execute();
    $result = $cek->get_result();

    if ($result->num_rows === 0) {
        $username = 'admin';
        $email = 'admin@muliamajujaya.com';
        $password = Keamanan::hashPassword('admin123');

        $insert = $koneksi->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, 'admin')");
        if (!$insert) {
            die("Gagal prepare insert admin: " . $koneksi->error);
        }

        $insert->bind_param("sss", $username, $password, $email);
        $insert->execute();
    }
}


// Panggil buat admin default
buatAdminDefault($koneksi);

$error = '';

// Proses login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = Keamanan::validasiInput($_POST['username']);
    $password = $_POST['password'];

    $stmt = $koneksi->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if (Keamanan::verifikasiPassword($password, $user['password'])) {
            $_SESSION['login'] = true;
            $_SESSION['id_user'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Arahkan berdasarkan role
            if ($user['role'] === 'admin') {
                header("Location: ../admin/dashboard.php");
            } elseif ($user['role'] === 'penilai') {
                header("Location: ../penilai/dashboard.php");
            } elseif ($user['role'] === 'karyawan') {
                header("Location: ../karyawan/dashboard.php");
            } else {
                header("Location: ../index.php");
            }
            exit();
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Pengguna tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Sistem Penilaian</title>
    <link rel="stylesheet" href="../assets/css/gaya.css">
    <style>
        body {
            background-color: #f5f5f5;
            font-family: sans-serif;
        }
        .login-box {
            width: 400px;
            margin: 80px auto;
            background: white;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        .login-box h2 {
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #0066cc;
            color: white;
            border: none;
            cursor: pointer;
        }
        .error {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Login</h2>
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required placeholder="Masukkan username">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="Masukkan password">
            </div>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
