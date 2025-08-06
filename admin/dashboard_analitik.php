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

// Statistik Kinerja Karyawan
$query_statistik_kinerja = "
    SELECT 
        d.departemen,
        AVG(dp.skor * k.bobot / 100) as rata_rata_skor,
        COUNT(DISTINCT p.id_karyawan) as jumlah_karyawan
    FROM 
        penilaian p
    JOIN 
        karyawan k ON p.id_karyawan = k.id_karyawan
    JOIN 
        detail_penilaian dp ON p.id_penilaian = dp.id_penilaian
    JOIN 
        kriteria k ON dp.id_kriteria = k.id_kriteria
    JOIN 
        departemen d ON k.departemen = d.nama_departemen
    GROUP BY 
        d.departemen
";
$result_statistik_kinerja = $koneksi->query($query_statistik_kinerja);

// Performa Karyawan Terbaik
$query_top_karyawan = "
    SELECT 
        k.nama,
        k.departemen,
        AVG(dp.skor * kr.bobot / 100) as total_skor
    FROM 
        penilaian p
    JOIN 
        karyawan k ON p.id_karyawan = k.id_karyawan
    JOIN 
        detail_penilaian dp ON p.id_penilaian = dp.id_penilaian
    JOIN 
        kriteria kr ON dp.id_kriteria = kr.id_kriteria
    GROUP BY 
        k.id_karyawan
    ORDER BY 
        total_skor DESC
    LIMIT 5
";
$result_top_karyawan = $koneksi->query($query_top_karyawan);

// Trend Penilaian Bulanan
$query_trend_penilaian = "
    SELECT 
        DATE_FORMAT(p.tanggal_penilaian, '%Y-%m') as bulan,
        AVG(dp.skor * kr.bobot / 100) as rata_rata_skor
    FROM 
        penilaian p
    JOIN 
        detail_penilaian dp ON p.id_penilaian = dp.id_penilaian
    JOIN 
        kriteria kr ON dp.id_kriteria = kr.id_kriteria
    GROUP BY 
        bulan
    ORDER BY 
        bulan
    LIMIT 12
";
$result_trend_penilaian = $koneksi->query($query_trend_penilaian);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Analitik Kinerja</title>
    <link rel="stylesheet" href="../assets/css/gaya.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="kontainer-analitik">
        <h1>Dashboard Analitik Kinerja Karyawan</h1>

        <div class="grafik-container">
            <div class="grafik-kiri">
                <h2>Performa Departemen</h2>
                <canvas id="grafikDepartemen"></canvas>
            </div>
            <div class="grafik-kanan">
                <h2>Trend Penilaian Bulanan</h2>
                <canvas id="grafikTrendPenilaian"></canvas>
            </div>
        </div>

        <div class="tabel-container">
            <div class="top-karyawan">
                <h2>5 Karyawan Terbaik</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Departemen</th>
                            <th>Skor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($karyawan = $result_top_karyawan->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $karyawan['nama']; ?></td>
                                <td><?php echo $karyawan['departemen']; ?></td>
                                <td><?php echo number_format($karyawan['total_skor'], 2); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Grafik Departemen
        var ctxDepartemen = document.getElementById('grafikDepartemen').getContext('2d');
        var departemenLabels = [];
        var departemenScores = [];

        <?php
        // Reset pointer
        $result_statistik_kinerja->data_seek(0);
        while($row = $result_statistik_kinerja->fetch_assoc()): ?>
            departemenLabels.push('<?php echo $row['departemen']; ?>');
            departemenScores.push(<?php echo $row['rata_rata_skor']; ?>);
        <?php endwhile; ?>

        new Chart(ctxDepartemen, {
            type: 'bar',
            data: {
                labels: departemenLabels,
                datasets: [{
                    label: 'Rata-rata Skor Kinerja',
                    data: departemenScores,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Grafik Trend Penilaian
        var ctxTrend = document.getElementById('grafikTrendPenilaian').getContext('2d');
        var bulanLabels = [];
        var trendScores = [];

        <?php
        // Reset pointer
        $result_trend_penilaian->data_seek(0);
        while($row = $result_trend_penilaian->fetch_assoc()): ?>
            bulanLabels.push('<?php echo $row['bulan']; ?>');
            trendScores.push(<?php echo $row['rata_rata_skor']; ?>);
        <?php endwhile; ?>

        new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: bulanLabels,
                datasets: [{
                    label: 'Trend Skor Penilaian',
                    data: trendScores,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>