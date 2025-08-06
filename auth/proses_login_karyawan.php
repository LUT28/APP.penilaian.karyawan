<?php
session_start();
require_once '../config/koneksi.php';

$db = new Database();
$koneksi = $db->getKoneksi();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $koneksi->prepare("SELECT * FROM users WHERE username = ? AND role = 'karyawan' LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['id_user'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['id_karyawan'] = $user['id_karyawan'];
            
            // ✅ Redirect ke folder dashboard karyawan
            header("Location: ../karyawan/dashboard_karyawan.php");
            exit();
        } else {
            header("Location: login_karyawan.php?error=Password salah");
            exit();
        }
    } else {
        header("Location: login_karyawan.php?error=Username tidak ditemukan");
        exit();
    }
}
?>
