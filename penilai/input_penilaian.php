<?php
session_start();
require_once '../config/koneksi.php';

// Cek otentikasi penilai
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'penilai') {
    header("Location: ../auth/login.php");
    exit();
}

$db = new Database();
$koneksi = $db->getKoneksi();

// Ambil daftar karyawan
$query_karyawan = "SELECT id_karyawan, nama, departemen FROM karyawan";
$result_karyawan = $koneksi->query($query_karyawan);

// Ambil daftar kriteria
$query_kriteria = "SELECT id_kriteria, nama_kriteria, bobot FROM kriteria";
$result_kriteria = $koneksi->query($query_kriteria);

// Proses submit penilaian
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Mulai transaksi
    $koneksi->begin_transaction();

    try {
        // Simpan data penilaian utama
        $id_karyawan = $_POST['id_karyawan'];
        $id_penilai = $_SESSION['id_penilai']; // Pastikan ada di session
        $tanggal_penilaian = date('Y-m-d');
        $id_periode = $_POST['id_periode'];

        // Insert penilaian
        $query_penilaian = "INSERT INTO penilaian (id_karyawan, id_penilai, tanggal_penilaian, id_periode) 
                            VALUES (?, ?, ?, ?)";
        $stmt_penilaian = $koneksi->prepare($query_penilaian);
        $stmt_penilaian->bind_param("iisi", $id_karyawan, $id_penilai, $tanggal_penilaian, $id_periode);
        $stmt_penilaian->execute();
        
        // Ambil ID penilaian yang baru saja dibuat
        $id_penilaian = $koneksi->insert_id;

        // Simpan detail penilaian
        $query_detail = "INSERT INTO detail_penilaian (id_penilaian, id_kriteria, skor) VALUES (?, ?, ?)";
        $stmt_detail = $koneksi->prepare($query_detail);

        // Loop kriteria
        foreach ($_POST['kriteria'] as $id_kriteria => $skor) {
            $stmt_detail->bind_param("iii", $id_penilaian, $id_kriteria, $skor);
            $stmt_detail->execute();
        }

        // Commit transaksi
        $koneksi->commit();
        $sukses = "Penilaian berhasil disimpan!";
    } catch (Exception $e) {
        // Rollback jika ada kesalahan
        $koneksi->rollback();
        $error = "Gagal menyimpan penilaian: " . $e->getMessage();
    }
}

// Ambil daftar periode
$query_periode = "SELECT id_periode, nama_periode FROM periode";
$result_periode = $koneksi->query($query_periode);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Input Penilaian Karyawan</title>
    <link rel="stylesheet" href="../assets/css/gaya.css">
    <style>
        .kontainer-penilaian {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .kriteria-group {
            margin-bottom: 15px;
        }

        .kriteria-group label {
            display: block;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="kontainer-penilaian">
        <h2>Input Penilaian Karyawan</h2>

        <?php if(isset($error)): ?>
            <div class="pesan-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if(isset($sukses)): ?>
            <div class="pesan-sukses"><?php echo $sukses; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Pilih Karyawan</label>
                <select name="id_karyawan" required>
                    <option value="">Pilih Karyawan</option>
                    <?php while($karyawan = $result_karyawan->fetch_assoc()): ?>
                        <option value="<?php echo $karyawan['id_karyawan']; ?>">
                            <?php echo $karyawan['nama'] . " - " . $karyawan['departemen']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Pilih Periode</label>
                <select name="id_periode" required>
                    <option value="">Pilih Periode Penilaian</option>
                    <?php while($periode = $result_periode->fetch_assoc()): ?>
                        <option value="<?php echo $periode['id_periode']; ?>">
                            <?php echo $periode['nama_periode']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <h3>Kriteria Penilaian</h3>
            <?php 
            // Reset pointer result set
            $result_kriteria->data_seek(0);
            while($kriteria = $result_kriteria->fetch_assoc()): 
            ?>
                <div class="kriteria-group">
                    <label>
                        <?php echo $kriteria['nama_kriteria']; ?> 
                        (Bobot: <?php echo $kriteria['bobot']; ?>%)
                    </label>
                    <input 
                        type="number" 
                        name="kriteria[<?php echo $kriteria['id_kriteria']; ?>]" 
                        min="0" 
                        max="100" 
                        required
                    >
                </div>
            <?php endwhile; ?>

            <button type="submit">Simpan Penilaian</button>
        </form>
    </div>
</body>
</html>