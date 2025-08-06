<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$db = new Database();
$koneksi = $db->getKoneksi();

if (!isset($_GET['id_kriteria'])) {
    echo "ID tidak ditemukan.";
    exit();
}

$id = $_GET['id_kriteria'];

$stmt = $koneksi->prepare("SELECT * FROM kriteria WHERE id_kriteria = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Data tidak ditemukan.";
    exit();
}

$data = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama_kriteria'];
    $bobot = $_POST['bobot'];
    $deskripsi = $_POST['deskripsi'];

    $stmt = $koneksi->prepare("UPDATE kriteria SET nama_kriteria=?, bobot=?, deskripsi=? WHERE id_kriteria=?");
    $stmt->bind_param("sdsi", $nama, $bobot, $deskripsi, $id);
    $stmt->execute();

    header("Location: kelola_kriteria.php");
    exit();
}
?>

<form method="post">
    <label>Nama Kriteria:</label>
    <input type="text" name="nama_kriteria" value="<?= $data['nama_kriteria'] ?>" required><br>
    <label>Bobot (%):</label>
    <input type="number" step="0.01" name="bobot" value="<?= $data['bobot'] ?>" required><br>
    <label>Deskripsi:</label>
    <textarea name="deskripsi" rows="4"><?= $data['deskripsi'] ?></textarea><br>
    <button type="submit">Simpan Perubahan</button>
</form>
<a href="kelola_kriteria.php">← Kembali</a>
