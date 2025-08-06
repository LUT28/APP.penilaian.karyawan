<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

if (!isset($_GET['id_kriteria'])) {
    echo "ID tidak ditemukan.";
    exit();
}

$id = $_GET['id_kriteria'];

$db = new Database();
$koneksi = $db->getKoneksi();

$stmt = $koneksi->prepare("DELETE FROM kriteria WHERE id_kriteria = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: kelola_kriteria.php");
exit();
?>
