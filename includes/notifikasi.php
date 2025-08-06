<?php
class Notifikasi {
    private $koneksi;

    public function __construct($koneksi) {
        $this->koneksi = $koneksi;
    }

    // Kirim notifikasi
    public function kirim($penerima_id, $judul, $pesan, $jenis = 'info') {
        $query = "INSERT INTO notifikasi 
                  (penerima_id, judul, pesan, jenis, status) 
                  VALUES (?, ?, ?, ?, 'belum_dibaca')";
        
        $stmt = $this->koneksi->prepare($query);
        $stmt->bind_param("isss", $penerima_id, $judul, $pesan, $jenis);
        
        return $stmt->execute();
    }

    // Ambil notifikasi untuk pengguna
    public function ambilNotifikasi($penerima_id, $batas = 10) {
        $query = "SELECT * FROM notifikasi 
                  WHERE penerima_id = ? 
                  ORDER BY waktu DESC 
                  LIMIT ?";
        
        $stmt = $this->koneksi->prepare($query);
        $stmt->bind_param("ii", $penerima_id, $batas);
        $stmt->execute();
        
        return $stmt->get_result();
    }

    // Tandai notifikasi sebagai dibaca
    public function tandaiDibaca($id_notifikasi) {
        $query = "UPDATE notifikasi SET status = 'dibaca' WHERE id = ?";
        
        $stmt = $this->koneksi->prepare($query);
        $stmt->bind_param("i", $id_notifikasi);
        
        return $stmt->execute();
    }
}

// Struktur tabel notifikasi
/*
CREATE TABLE notifikasi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    penerima_id INT,
    judul VARCHAR(255),
    pesan TEXT,
    jenis ENUM('info', 'peringatan', 'penting') DEFAULT 'info',
    status ENUM('belum_dibaca', 'dibaca') DEFAULT 'belum_dibaca',
    waktu TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (penerima_id) REFERENCES users(id)
);
*/