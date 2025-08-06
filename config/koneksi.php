<?php
class Database {
    private $host = 'localhost';
    private $username = 'root';
    private $password = '';
    private $database = 'db_penilaian_kinerja';
    public $koneksi;

    public function __construct() {
        $this->koneksi = new mysqli(
            $this->host, 
            $this->username, 
            $this->password, 
            $this->database
        );
if ($this->koneksi->connect_error) {
    error_log("Koneksi DB gagal: " . $this->koneksi->connect_error);
    exit("Gagal terhubung ke database. Hubungi administrator.");
}

    }

    public function getKoneksi() {
        return $this->koneksi;
    }
}
?>