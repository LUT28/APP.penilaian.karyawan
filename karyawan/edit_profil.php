<?php
session_start();
require_once '../config/koneksi.php';
require_once '../config/keamanan.php';

// Cek otentikasi karyawan
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'karyawan') {
    header("Location: ../auth/login.php");
    exit();
}

$db = new Database();
$koneksi = $db->getKoneksi();

// Ambil data karyawan
$id_karyawan = $_SESSION['id_karyawan'];
$query_karyawan = "SELECT * FROM karyawan WHERE id_karyawan = ?";
$stmt = $koneksi->prepare($query_karyawan);
$stmt->bind_param("i", $id_karyawan);
$stmt->execute();
$karyawan = $stmt->get_result()->fetch_assoc();

// Proses update profil
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validasi input
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $no_telepon = Keamanan::validasiInput($_POST['no_telepon']);
    $alamat = Keamanan::validasiInput($_POST['alamat']);

    // Cek apakah ada file upload foto
    $foto_profil = $karyawan['foto_profil']; // Default foto existing
    if (!empty($_FILES['foto_profil']['name'])) {
        $target_dir = "../uploads/profil/";
        
        // Buat direktori jika tidak ada
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_name = $id_karyawan . "_" . basename($_FILES['foto_profil']['name']);
        $target_file = $target_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validasi tipe file
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($imageFileType, $allowed_types)) {
            if (move_uploaded_file($_FILES['foto_profil']['tmp_name'], $target_file)) {
                $foto_profil = $file_name;
            } else {
                $error = "Gagal mengunggah foto profil.";
            }
        } else {
            $error = "Tipe file tidak diizinkan. Gunakan JPG, JPEG, PNG, atau GIF.";
        }
    }

    // Update data karyawan
    $query_update = "UPDATE karyawan 
                     SET email = ?, no_telepon = ?, alamat = ?, foto_profil = ?
                     WHERE id_karyawan = ?";
    $stmt_update = $koneksi->prepare($query_update);
    $stmt_update->bind_param("ssssi", $email, $no_telepon, $alamat, $foto_profil, $id_karyawan);

    if ($stmt_update->execute()) {
        $sukses = "Profil berhasil diperbarui.";
        // Refresh data karyawan
        $stmt->execute();
        $karyawan = $stmt->get_result()->fetch_assoc();
    } else {
        $error = "Gagal memperbarui profil: " . $stmt_update->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Profil Karyawan</title>
    <link rel="stylesheet" href="../assets/css/gaya.css">
    <style>
        .pratinjau-foto {
            max-width: 200px;
            border-radius: 50%;
        }
    </style>
</head>
<body>
    <div class="kontainer">
        <h2>Edit Profil</h2>

        <?php if(isset($error)): ?>
            <div class="pesan-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if(isset($sukses)): ?>
            <div class="pesan-sukses"><?php echo $sukses; ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Foto Profil</label>
                <?php if(!empty($karyawan['foto_profil'])): ?>
                    <img 
                        src="../uploads/profil/<?php echo $karyawan['foto_profil']; ?>" 
                        alt="Foto Profil" 
                        class="pratinjau-foto"
                    >
                <?php endif; ?>
                <input type="file" name="foto_profil" accept="image/*">
            </div>

            <div class="form-group">
                <label>Nama Lengkap</label>
                <input 
                    type="text" 
                    value="<?php echo $karyawan['nama']; ?>" 
                    readonly
                >
            </div>

            <div class="form-group">
                <label>NIP</label>
                <input 
                    type="text" 
                    value="<?php echo $karyawan['nip']; ?>" 
                    readonly
                >
            </div>

            <div class="form-group">
                <label>Email</label>
                <input 
                    type="email" 
                    name="email" 
                    value="<?php echo $karyawan['email'] ?? ''; ?>" 
                    required
                >
            </div>

            <div class="form-group">
                <label>Nomor Telepon</label>
                <input 
                    type="tel" 
                    name="no_telepon" 
                    value="<?php echo $karyawan['no_telepon'] ?? ''; ?>"
                >
            </div>

            <div class="form-group">
                <label>Alamat</label>
                <textarea 
                    name="alamat" 
                    rows="4"
                ><?php echo $karyawan['alamat'] ?? ''; ?></textarea>
            </div>

            <button type="submit">Perbarui Profil</button>
        </form>
    </div>

    <script>
        // Pratinjau foto
        document.querySelector('input[name="foto_profil"]').addEventListener('change', function(e) {
            const reader = new FileReader();
            reader.onload = function(event) {
                const imgElement = document.querySelector('.pratinjau-foto');
                if (imgElement) {
                    imgElement.src = event.target.result;
                } else {
                    const img = document.createElement('img');
                    img.src = event.target.result;
                    img.classList.add('pratinjau-foto');
                    e.target.parentNode.insertBefore(img, e.target.nextSibling);
                }
            }
            reader.readAsDataURL(e.target.files[0]);
        });
    </script>
</body>
</html>