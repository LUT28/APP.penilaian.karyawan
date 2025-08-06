<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'karyawan') {
    header("Location: ../auth/login_karyawan.php");
    exit();
}

require_once '../config/koneksi.php';
$db = new Database();
$koneksi = $db->getKoneksi();

$id_karyawan = $_SESSION['id_karyawan'];
$stmt = $koneksi->prepare("SELECT k.*, u.email FROM karyawan k JOIN users u ON k.id_karyawan = u.id_karyawan WHERE k.id_karyawan = ?");
$stmt->bind_param("i", $id_karyawan);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
?>

<h2>Dashboard Karyawan</h2>
<p>Selamat datang, <?php echo htmlspecialchars($data['nama']); ?>!</p>
<p>Departemen: <?php echo htmlspecialchars($data['departemen']); ?></p>
<p>Email: <?php echo htmlspecialchars($data['email']); ?></p>
<a href="../auth/logout.php">Logout</a>
