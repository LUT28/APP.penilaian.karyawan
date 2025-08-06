<?php
session_start();
require_once '../config/koneksi.php';

// Cek otentikasi admin
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$db = new Database();
$koneksi = $db->getKoneksi();

// Filter laporan
$filter_departemen = $_GET['departemen'] ?? '';
$filter_periode = $_GET['periode'] ?? '';

$query = "
    SELECT 
        k.nama,
        k.departemen,
        p.tanggal_penilaian,
        pr.nama_periode,
        kr.nama_kriteria,
        dp.skor,
        kr.bobot
    FROM 
        penilaian p
    JOIN 
        karyawan k ON p.id_karyawan = k.id_karyawan
    JOIN 
        periode pr ON p.id_periode = pr.id_periode
    JOIN 
        detail_penilaian dp ON p.id_penilaian = dp.id_penilaian
    JOIN 
        kriteria kr ON dp.id_kriteria = kr.id_kriteria
    WHERE 
        1=1
    " . (!empty($filter_departemen) ? " AND k.departemen = '$filter_departemen'" : "") . "
    " . (!empty($filter_periode) ? " AND pr.nama_periode = '$filter_periode'" : "") . "
    ORDER BY 
        k.nama, p.tanggal_penilaian
";

$result = $koneksi->query($query);

// Ambil daftar departemen dan periode untuk filter
$query_departemen = "SELECT DISTINCT departemen FROM karyawan";
$query_periode = "SELECT DISTINCT nama_periode FROM periode";
$departemen_list = $koneksi->query($query_departemen);
$periode_list = $koneksi->query($query_periode);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Detail Kinerja</title>
    <link rel="stylesheet" href="../assets/css/gaya.css">
</head>
<body>
    <div class="kontainer">
        <h1>Laporan Detail Kinerja</h1>

        <form method="GET" class="filter-form">
            <select name="departemen">
                <option value="">Semua Departemen</option>
                <?php while($dep = $departemen_list->fetch_assoc()): ?>
                    <option value="<?php echo $dep['departemen']; ?>">
                        <?php echo $dep['departemen']; ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <select name="periode">
                <option value="">Semua Periode</option>
                <?php while($per = $periode_list->fetch_assoc()): ?>
                    <option value="<?php echo $per['nama_periode']; ?>">
                        <?php echo $per['nama_periode']; ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <button type="submit">Filter</button>
        </form>

        <table class="tabel-laporan">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Departemen</th>
                    <th>Periode</th>
                    <th>Kriteria</th>
                    <th>Skor</th>
                    <th>Bobot</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['nama']; ?></td>
                        <td><?php echo $row['departemen']; ?></td>
                        <td><?php echo $row['nama_periode']; ?></td>
                        <td><?php echo $row['nama_kriteria']; ?></td>
                        <td><?php echo $row['skor']; ?></td>
                        <td><?php echo $row['bobot']; ?>%</td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="aksi-laporan">
            <button onclick="window.print()">Cetak Laporan</button>
            <button onclick="eksporExcel()">Ekspor Excel</button>
        </div>
    </div>

    <script>
        function eksporExcel() {
            // Implementasi ekspor Excel (bisa menggunakan library tambahan)
            alert('Fitur ekspor Excel akan segera hadir');
        }
    </script>
</body>
</html>