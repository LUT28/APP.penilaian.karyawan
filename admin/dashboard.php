<?php
session_start();
require_once '../config/koneksi.php';

// Cek otentikasi
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$db = new Database();
$koneksi = $db->getKoneksi();

// Ambil statistik
$query_total_karyawan = "SELECT COUNT(*) as total FROM karyawan";
$query_total_penilaian = "SELECT COUNT(*) as total FROM penilaian";

$total_karyawan = $koneksi->query($query_total_karyawan)->fetch_assoc()['total'];
$total_penilaian = $koneksi->query($query_total_penilaian)->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - Penilaian Kinerja</title>
    <link rel="stylesheet" href="../assets/css/gaya.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0; padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f5f7fa;
            padding: 30px;
        }

        .kontainer-dashboard {
            max-width: 900px;
            margin: auto;
            background: #fff;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 40px;
            color: #333;
        }

        .kartu-statistik {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 40px;
        }

        .kartu {
            background-color: #007bff;
            color: white;
            padding: 30px;
            border-radius: 12px;
            flex: 1;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .kartu h3 {
            margin-bottom: 10px;
            font-size: 20px;
        }

        .kartu p {
            font-size: 28px;
            font-weight: bold;
        }

        .menu-dashboard {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .menu-dashboard a {
            text-decoration: none;
            background-color: #28a745;
            color: white;
            padding: 14px 20px;
            border-radius: 8px;
            text-align: center;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .menu-dashboard a:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="kontainer-dashboard">
        <h1>Selamat Datang, Admin</h1>
        
        <div class="kartu-statistik">
            <div class="kartu">
                <h3>Total Karyawan</h3>
                <p><?php echo $total_karyawan; ?></p>
            </div>
            <div class="kartu">
                <h3>Total Penilaian</h3>
                <p><?php echo $total_penilaian; ?></p>
            </div>
        </div>

        <div class="menu-dashboard">
            <a href="kelola_karyawan.php">Kelola Data Karyawan</a>
            <a href="kelola_kriteria.php">Kelola Kriteria Penilaian</a>
            <a href="laporan_penilaian.php">Laporan Penilaian</a>
            <a href="../auth/logout.php">Logout</a>
        </div>
    </div>
</body>
</html>

