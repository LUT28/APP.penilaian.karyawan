<?php
session_start();
require_once '../config/koneksi.php';
require_once '../config/keamanan.php';

// Cek otentikasi
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit();
}

$db = new Database();
$koneksi = $db->getKoneksi();

$id_user = $_SESSION['id_user'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password_lama = $_POST['password_lama'];
    $password_baru = $_POST['password_baru'];
    $konfirmasi_password = $_POST['konfirmasi_password'];

    // Validasi input
    if (empty($password_lama) || empty($password_baru) || empty($konfirmasi_password)) {
        $error = "Semua field harus diisi!";
    } elseif ($password_baru !== $konfirmasi_password) {
        $error = "Konfirmasi password tidak cocok!";
    } elseif (strlen($password_baru) < 8) {
        $error = "Password baru minimal 8 karakter!";
    }

    // Cek password lama
    if (empty($error)) {
        $query_cek_password = "SELECT password FROM users WHERE id = ?";
        $stmt_cek = $koneksi->prepare($query_cek_password);
        $stmt_cek->bind_param("i", $id_user);
        $stmt_cek->execute();
        $result = $stmt_cek->get_result();
        $user = $result->fetch_assoc();

        if (!Keamanan::verifikasiPassword($password_lama, $user['password'])) {
            $error = "Password lama salah!";
        }
    }

    // Update password
    if (empty($error)) {
        $password_hash_baru = Keamanan::hashPassword($password_baru);
        
        $query_update = "UPDATE users SET password = ? WHERE id = ?";
        $stmt_update = $koneksi->prepare($query_update);
        $stmt_update->bind_param("si", $password_hash_baru, $id_user);

        if ($stmt_update->execute()) {
            $sukses = "Password berhasil diperbarui!";
        } else {
            $error = "Gagal memperbarui password: " . $stmt_update->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ganti Password</title>
    <link rel="stylesheet" href="../assets/css/gaya.css">
</head>
<body>
    <div class="kontainer">
        <h2>Ganti Password</h2>

        <?php if(isset($error)): ?>
            <div class="pesan-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if(isset($sukses)): ?>
            <div class="pesan-sukses"><?php echo $sukses; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Password Lama</label>
                <input 
                    type="password" 
                    name="password_lama" 
                    required
                >
            </div>

            <div class="form-group">
                <label>Password Baru</label>
                <input 
                    type="password" 
                    name="password_baru" 
                    required 
                    minlength="8"
                >
            </div>

            <div class="form-group">
                <label>Konfirmasi Password Baru</label>
                <input 
                    type="password" 
                    name="konfirmasi_password" 
                    required 
                    minlength="8"
                >
            </div>

            <button type="submit">Ganti Password</button>
        </form>
    </div>
</body>
</html>