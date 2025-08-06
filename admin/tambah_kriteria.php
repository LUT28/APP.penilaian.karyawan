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
    $nama = $_POST['nama_kriteria'];
    $bobot = $_POST['bobot'];
    $deskripsi = $_POST['deskripsi'];

 $stmt = $koneksi->prepare("INSERT INTO kriteria (nama_kriteria, bobot, deskripsi) VALUES (?, ?, ?)");
if (!$stmt) {
    die("Query error: " . $koneksi->error);
}
$stmt->bind_param("sds", $nama, $bobot, $deskripsi);


    header("Location: kelola_kriteria.php");
    exit();
}
?>

<form method="post">
    <label>Nama Kriteria:</label>
    <input type="text" name="nama_kriteria" required><br>
    <label>Bobot (%):</label>
    <input type="number" name="bobot" step="0.01" required><br>
    <label>Deskripsi:</label>
    <textarea name="deskripsi" rows="4"></textarea><br>
    <button type="submit">Simpan</button>
</form>
<a href="kelola_kriteria.php">← Kembali</a>
