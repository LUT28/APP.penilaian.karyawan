<?php
session_start();
// Jika sudah login, redirect ke dashboard sesuai role
if (isset($_SESSION['login'])) {
    switch ($_SESSION['role']) {
        case 'admin':
            header("Location: admin/dashboard.php");
            break;
        case 'penilai':
            header("Location: penilai/dashboard.php");
            break;
        case 'karyawan':
            header("Location: karyawan/dashboard.php");
            break;
        default:
            header("Location: auth/login.php");
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sistem Penilaian Kinerja - PT Mulia Maju Jaya</title>
    <link rel="stylesheet" href="assets/css/gaya.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .landing-container {
            text-align: center;
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            max-width: 500px;
            width: 100%;
        }

        .landing-container h1 {
            color: #333;
            margin-bottom: 20px;
        }

        .landing-container .tombol-utama {
            display: inline-block;
            margin: 10px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .landing-container .tombol-utama:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="landing-container">
        <h1>Sistem Penilaian Kinerja</h1>
        <p>PT Mulia Maju Jaya</p>
        
        <div class="aksi-landing">
            <a href="auth/login.php" class="tombol-utama">Login</a>
            <a href="auth/register.php" class="tombol-utama">Daftar</a>
        </div>

        <div class="deskripsi">
            <p>Selamat datang di sistem penilaian kinerja karyawan. Silakan login atau daftar untuk melanjutkan.</p>
        </div>
    </div>
</body>
</html>