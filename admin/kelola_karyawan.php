<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$db = new Database();
$koneksi = $db->getKoneksi();
$data_karyawan = $koneksi->query("SELECT * FROM karyawan");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Data Karyawan</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f1f1f1;
            color: #333;
        }

        header {
            background-color: #e0e0e0;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        h1 {
            margin: 0;
            font-size: 26px;
        }

        .container {
            max-width: 1000px;
            margin: 30px auto;
            background-color: #fff;
            padding: 25px 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .tambah-btn {
            display: inline-block;
            margin-bottom: 15px;
            padding: 10px 20px;
            background-color: #59bfeeff;
            color: white;
            border-radius: 6px;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .tambah-btn:hover {
            background-color: #59bfeeff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid #ccc;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        .aksi a {
            margin-right: 10px;
            color: #007BFF;
            text-decoration: none;
            font-weight: bold;
        }

        .aksi a:hover {
            text-decoration: underline;
        }

        .kembali {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #555;
        }

        .kembali:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <h1>Kelola Data Karyawan</h1>
    </header>

    <div class="container">
        <a href="tambah_karyawan.php" class="tambah-btn">+ Tambah Karyawan</a>

        <table>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>NIP</th>
                <th>Jabatan</th>
                <th>Departemen</th>
                <th>Aksi</th>
            </tr>
            <?php
            $no = 1;
            while ($row = $data_karyawan->fetch_assoc()) {
               echo "<tr>
                <td>$no</td>
                <td>{$row['nama']}</td>
                <td>{$row['nip']}</td>
                <td>{$row['jabatan']}</td>
                <td>{$row['departemen']}</td>
                <td class='aksi'>
                    <a href='edit_karyawan.php?id_karyawan={$row['id_karyawan']}'>Edit</a>
                    <a href='hapus_karyawan.php?id_karyawan={$row['id_karyawan']}' onclick=\"return confirm('Yakin hapus?')\">Hapus</a>
                </td>
            </tr>";
                $no++;
            }
            ?>
        </table>

        <a href="dashboard.php" class="kembali">← Kembali ke Dashboard</a>
    </div>
</body>
</html>
