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

// Query laporan penilaian
$query_laporan = "
    SELECT 
        k.nama AS nama_karyawan,
        p.tanggal_penilaian,
        pr.nama_periode,
        SUM(dp.skor * kr.bobot / 100) AS total_skor
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
    GROUP BY 
        p.id_penilaian
    ORDER BY 
        p.tanggal_penilaian DESC
";

$result_laporan = $koneksi->query($query_laporan);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penilaian Karyawan</title>
    <link rel="stylesheet" href="../assets/css/gaya.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }

        .kontainer {
            max-width: 1000px;
            margin: 0 auto;
            background-color: #fff;
            padding: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }

        .tabel-laporan {
            width: 100%;
            border-collapse: collapse;
        }

        .tabel-laporan th, .tabel-laporan td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        .tabel-laporan th {
            background-color: #e0e0e0;
            color: #333;
        }

        .tabel-laporan tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .aksi-laporan {
            margin-top: 20px;
            text-align: center;
        }

        .aksi-laporan button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            margin: 0 10px;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .aksi-laporan button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<div class="kontainer">
    <h2>Laporan Penilaian Karyawan</h2>

    <table class="tabel-laporan">
        <thead>
            <tr>
                <th>Nama Karyawan</th>
                <th>Periode</th>
                <th>Tanggal Penilaian</th>
                <th>Total Skor</th>
                <th>Kategori</th>
            </tr>
        </thead>
        <tbody>
            <?php while($laporan = $result_laporan->fetch_assoc()): ?>
                <?php $total_skor = $laporan['total_skor']; ?>
                <tr>
                    <td><?php echo htmlspecialchars($laporan['nama_karyawan']); ?></td>
                    <td><?php echo htmlspecialchars($laporan['nama_periode']); ?></td>
                    <td><?php echo date('d-m-Y', strtotime($laporan['tanggal_penilaian'])); ?></td>
                    <td><?php echo number_format($total_skor, 2); ?></td>
                    <td>
                        <?php
                        if ($total_skor >= 90) {
                            echo "<span style='color:green;'>Sangat Baik</span>";
                        } elseif ($total_skor >= 75) {
                            echo "<span style='color:blue;'>Baik</span>";
                        } elseif ($total_skor >= 60) {
                            echo "<span style='color:orange;'>Cukup</span>";
                        } else {
                            echo "<span style='color:red;'>Kurang</span>";
                        }
                        ?>
                    </td>
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
        alert('Fitur ekspor Excel akan segera hadir');
    }
</script>
</body>
</html>
