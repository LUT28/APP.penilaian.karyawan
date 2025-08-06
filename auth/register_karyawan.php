<?php
require_once '../config/koneksi.php';
$db = new Database();
$koneksi = $db->getKoneksi();

$pesan = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $departemen = $_POST['departemen'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'karyawan';

    // Insert ke tabel karyawan
    $stmt = $koneksi->prepare("INSERT INTO karyawan (nama, departemen) VALUES (?, ?)");
    $stmt->bind_param("ss", $nama, $departemen);

    if ($stmt->execute()) {
        $id_karyawan = $koneksi->insert_id;

        // Insert ke tabel users
        $stmt2 = $koneksi->prepare("INSERT INTO users (username, email, password, role, id_karyawan) VALUES (?, ?, ?, ?, ?)");
        $stmt2->bind_param("ssssi", $username, $email, $password, $role, $id_karyawan);

        if ($stmt2->execute()) {
            // Redirect ke halaman login karyawan
            header("Location: login_karyawan.php?success=1");
            exit;
        } else {
            $pesan = "Gagal membuat akun: " . $stmt2->error;
        }
    } else {
        $pesan = "Gagal menyimpan data karyawan: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register Karyawan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e6e6e6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .card {
            background-color: #f2f2f2;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px 15px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            font-weight: bold;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 15px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .login-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #007bff;
            text-decoration: none;
        }
        .login-link:hover {
            text-decoration: underline;
        }
        .alert {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="card">
    <h2>Register Karyawan</h2>

    <?php if (!empty($pesan)): ?>
        <div class="alert"><?= $pesan ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="text" name="nama" placeholder="Nama Lengkap" required>
        <input type="text" name="nip" placeholder="NIP" required>
        <input type="text" name="jabatan" placeholder="Jabatan" required>

        <input type="text" name="departemen" placeholder="Departemen" required>
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Register</button>
    </form>

    <a class="login-link" href="login_karyawan.php">Sudah punya akun? Login di sini</a>
</div>
</body>
</html>
