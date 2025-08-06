<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

if (!isset($_GET['id_karyawan'])) {
    echo "ID tidak ditemukan.";
    exit();
}

$db = new Database();
$koneksi = $db->getKoneksi();

$id_karyawan = $_GET['id_karyawan'];

// Hapus data
$stmt = $koneksi->prepare("DELETE FROM karyawan WHERE id_karyawan = ?");
$stmt->bind_param("i", $id_karyawan);
$stmt->execute();

header("Location: kelola_karyawan.php");
exit();
