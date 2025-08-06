<?php
class Middleware {
    // Cek otentikasi dan otorisasi
    public static function cekOtentikasi($role_izin = []) {
        session_start();

        // Cek login
        if (!isset($_SESSION['login'])) {
            header("Location: ../auth/login.php");
            exit();
        }

        // Cek otorisasi
        if (!empty($role_izin) && !in_array($_SESSION['role'], $role_izin)) {
            // Redirect ke halaman tidak diizinkan
            header("Location: ../error/403.php");
            exit();
        }
    }

    // Logging aktivitas
    public static function log($aktivitas, $id_user = null) {
        global $koneksi; // Pastikan koneksi database tersedia

        $id_user = $id_user ?? $_SESSION['id_user'] ?? null;
        $ip_address = $_SERVER['REMOTE_ADDR'];

        $query = "INSERT INTO log_aktivitas (id_user, aktivitas, ip_address) VALUES (?, ?, ?)";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("iss", $id_user, $aktivitas, $ip_address);
        $stmt->execute();
    }

    // Pembatasan percobaan login
    public static function cekPercobaanLogin($username) {
        global $koneksi;

        $query = "SELECT COUNT(*) as percobaan FROM percobaan_login WHERE username = ? AND waktu > DATE_SUB(NOW(), INTERVAL 15 MINUTE)";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();

        return $data['percobaan'] >= 5; // Maksimal 5 percobaan
    }

    // Catat percobaan login
    public static function catatPercobaanLogin($username) {
        global $koneksi;

        $query = "INSERT INTO percobaan_login (username, waktu) VALUES (?, NOW())";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
    }
}

// Tabel tambahan untuk log dan keamanan
/*
CREATE TABLE log_aktivitas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT,
    aktivitas TEXT,
    ip_address VARCHAR(50),
    waktu TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE percobaan_login (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50),
    waktu TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
*/