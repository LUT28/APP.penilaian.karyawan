<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$db = new Database();
$koneksi = $db->getKoneksi();

if (!isset($_GET['id_karyawan'])) {
    echo "ID tidak ditemukan.";
    exit();
}

$id_karyawan = $_GET['id_karyawan'];

// Ambil data karyawan
$stmt = $koneksi->prepare("SELECT * FROM karyawan WHERE id_karyawan = ?");
$stmt->bind_param("i", $id_karyawan);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Data tidak ditemukan.";
    exit();
}

$data = $result->fetch_assoc();

// Proses update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $nip = $_POST['nip'];
    $jabatan = $_POST['jabatan'];
    $departemen = $_POST['departemen'];

    $stmt = $koneksi->prepare("UPDATE karyawan SET nama=?, nip=?, jabatan=?, departemen=? WHERE id_karyawan=?");
    $stmt->bind_param("ssssi", $nama, $nip, $jabatan, $departemen, $id_karyawan);
    $stmt->execute();

    header("Location: kelola_karyawan.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Karyawan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 20px;
        }
        .card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            max-width: 500px;
            margin: auto;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        input[type="text"] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        label {
            font-weight: bold;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 18px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #388E3C;
        }
        a {
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            color: #333;
        }
    </style>
</head>
<body>

<div class="card">
    <h2>Edit Karyawan</h2>
    <form method="post">
        <label>Nama:</label>
        <input type="text" name="nama" value="<?= $data['nama'] ?>" required>

        <label>NIP:</label>
        <input type="text" name="nip" value="<?= $data['nip'] ?>" required>

        <label>Jabatan:</label>
        <input type="text" name="jabatan" value="<?= $data['jabatan'] ?>" required>

        <label>Departemen:</label>
        <input type="text" name="departemen" value="<?= $data['departemen'] ?>" required>

        <button type="submit">Simpan Perubahan</button>
    </form>
    <a href="kelola_karyawan.php">← Kembali</a>
</div>

</body>
</html>
