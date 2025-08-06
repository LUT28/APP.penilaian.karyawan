<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$db = new Database();
$koneksi = $db->getKoneksi();

// Tambah kriteria
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_kriteria = $_POST['nama_kriteria'];
    $bobot = $_POST['bobot'];
    $deskripsi = $_POST['deskripsi'];

    $stmt = $koneksi->prepare("INSERT INTO kriteria (nama_kriteria, bobot, deskripsi) VALUES (?, ?, ?)");
    if (!$stmt) {
        die("Query error: " . $koneksi->error);
    }
    $stmt->bind_param("sds", $nama_kriteria, $bobot, $deskripsi);
    $stmt->execute();
    $stmt->close();

    header("Location: kelola_kriteria.php");
    exit();
}

// Hapus kriteria
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $koneksi->query("DELETE FROM kriteria WHERE id_kriteria = $id");
    header("Location: kelola_kriteria.php");
    exit();
}

// Ambil semua data kriteria
$data_kriteria = $koneksi->query("SELECT * FROM kriteria");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Kriteria</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f7f7f7;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: auto;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .btn {
            background: #2196F3;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin-bottom: 20px;
        }
        .btn:hover {
            background: #1976D2;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #e0e0e0;
        }
        a.hapus {
            color: red;
            text-decoration: none;
        }
        a.hapus:hover {
            text-decoration: underline;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 10;
            left: 0; top: 0;
            width: 100%; height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fff;
            margin: 8% auto;
            padding: 20px;
            border-radius: 10px;
            width: 500px;
            position: relative;
        }
        .close {
            color: #aaa;
            font-size: 28px;
            position: absolute;
            right: 20px;
            top: 10px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover {
            color: black;
        }
        input[type="text"],
        input[type="number"],
        textarea {
            width: 100%;
            padding: 10px;
            margin: 8px 0 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        label {
            font-weight: bold;
        }
        .submit-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
        }
        .submit-btn:hover {
            background-color: #388E3C;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Kelola Kriteria</h2>

    <button class="btn" onclick="document.getElementById('modalForm').style.display='block'">+ Tambah Kriteria</button>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Kriteria</th>
                <th>Bobot (%)</th>
                <th>Deskripsi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $no = 1;
        while ($row = $data_kriteria->fetch_assoc()) {
            echo "<tr>
                <td>{$no}</td>
                <td>{$row['nama_kriteria']}</td>
                <td>{$row['bobot']}</td>
                <td>{$row['deskripsi']}</td>
                <td><a class='hapus' href='?hapus={$row['id_kriteria']}' onclick=\"return confirm('Yakin ingin menghapus?')\">Hapus</a></td>
            </tr>";
            $no++;
        }
        ?>
        </tbody>
    </table>
    <a href="dashboard.php" class="kembali">← Kembali ke Dashboard</a>
</div>

<!-- Modal Form -->
<div id="modalForm" class="modal">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('modalForm').style.display='none'">&times;</span>
        <h3>Tambah Kriteria</h3>
        <form method="POST">
            <label>Nama Kriteria</label>
            <input type="text" name="nama_kriteria" required>

            <label>Bobot (%)</label>
            <input type="number" name="bobot" step="0.01" required>

            <label>Deskripsi</label>
            <textarea name="deskripsi" rows="4" required></textarea>

            <button type="submit" class="submit-btn">Simpan</button>
        </form>
    </div>
</div>

<script>
// Tutup modal saat klik di luar
window.onclick = function(event) {
    const modal = document.getElementById('modalForm');
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
</script>
</body>
</html>
