<?php
class Pengingat {
    private $koneksi;

    public function __construct($koneksi) {
        $this->koneksi = $koneksi;
    }

    // Cek karyawan yang belum dinilai
    public function cekKaryawanBelumDinilai() {
        $query = "
            SELECT k.id_karyawan, k.nama, k.email, 
                   (SELECT MAX(tanggal_penilaian) 
                    FROM penilaian 
                    WHERE id_karyawan = k.id_karyawan) as terakhir_dinilai
            FROM karyawan k
            WHERE 
                (SELECT MAX(tanggal_penilaian) 
                 FROM penilaian 
                 WHERE id_karyawan = k.id_karyawan) IS NULL 
                OR 
                DATEDIFF(CURRENT_DATE, 
                    (SELECT MAX(tanggal_penilaian) 
                     FROM penilaian 
                     WHERE id_karyawan = k.id_karyawan)
                ) > 365
        ";

        $result = $this->koneksi->query($query);
        return $result;
    }

    // Kirim pengingat via email
    public function kirimPengingat() {
        $karyawan_belum_dinilai = $this->cekKaryawanBelumDinilai();

        while ($karyawan = $karyawan_belum_dinilai->fetch_assoc()) {
            $subjek = "Pengingat Penilaian Kinerja";
            $pesan = "Halo {$karyawan['nama']},\n\n";
            $pesan .= "Anda belum melakukan penilaian kinerja dalam setahun terakhir. ";
            $pesan .= "Silakan segera hubungi atasan untuk melakukan penilaian.";

            // Kirim email (butuh konfigurasi email)
            $this->kirimEmail($karyawan['email'], $subjek, $pesan);

            // Buat notifikasi
            $notifikasi = new Notifikasi($this->koneksi);
            $notifikasi->kirim(
                $karyawan['id_karyawan'], 
                $subjek, 
                $pesan, 
                'peringatan'
            );
        }
    }

    // Fungsi kirim email (membutuhkan konfigurasi SMTP)
    private function kirimEmail($penerima, $subjek, $pesan) {
        // Implementasi pengiriman email
        // Contoh menggunakan PHP mail() atau library PHPMailer
    }
}

// Cara menggunakan (bisa dijadikan cron job)
// $pengingat = new Pengingat($koneksi);
// $pengingat->kirimPengingat();