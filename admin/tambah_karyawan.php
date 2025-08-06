<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$db = new Database();
$koneksi = $db->getKoneksi();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $nip = $_POST['nip'];
    $jabatan = $_POST['jabatan'];
    $departemen = $_POST['departemen'];

    $stmt = $koneksi->prepare("INSERT INTO karyawan (nama, nip, jabatan, departemen) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nama, $nip, $jabatan, $departemen);
    $stmt->execute();

    header("Location: kelola_karyawan.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Karyawan</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f3f3f3;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            max-width: 500px;
            margin: 60px auto;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
            padding: 30px;
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }

        label {
            display: block;
            margin-bottom: 10px;
            color: #555;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px 14px;
            border-radius: 8px;
            border: 1px solid #ccc;
            box-sizing: border-box;
            margin-bottom: 20px;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus {
            border-color: #3399ff;
            outline: none;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #3399ff;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #1d7fe3;
        }

        .kembali {
            display: block;
            text-align: center;
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

<div class="container">
    <h2>Tambah Data Karyawan</h2>
    <form method="post" action="">
        <label>Nama:
            <input type="text" name="nama" required>
        </label>
        <label>NIP:
            <input type="text" name="nip" required>
        </label>
        <label>Jabatan:
            <input type="text" name="jabatan" required>
        </label>
        <label>Departemen:
            <input type="text" name="departemen" required>
        </label>
        <button type="submit">Simpan</button>
    </form>
    <a class="kembali" href="kelola_karyawan.php">← Kembali</a>
</div>

</body>
</html>

