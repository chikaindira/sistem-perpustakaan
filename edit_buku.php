<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: index.php');
    exit();
}

require 'koneksi.php';

if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit();
}

$id = $_GET['id'];

// Mencegah SQL Injection dengan prepared statement
$stmt = $conn->prepare("SELECT * FROM buku WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$buku = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$buku) {
    header('Location: dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = $_POST['judul'];
    $pengarang = $_POST['pengarang'];
    $tahun_terbit = $_POST['tahun_terbit'];
    $cover = $_FILES['cover']['name'] ? $_FILES['cover']['name'] : $buku['cover'];

    // Jika ada file yang diupload, pindahkan file ke direktori uploads
    if ($_FILES['cover']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($cover);
        move_uploaded_file($_FILES['cover']['tmp_name'], $target_file);
    }

    // Menggunakan prepared statement untuk update data buku
    $stmt = $conn->prepare("UPDATE buku SET judul = ?, pengarang = ?, tahun_terbit = ?, cover = ? WHERE id = ?");
    $stmt->bind_param("ssisi", $judul, $pengarang, $tahun_terbit, $cover, $id);

    if ($stmt->execute()) {
        header('Location: dashboard.php');
        exit();
    } else {
        $error = "Gagal mengupdate buku: " . $conn->error;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Buku</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Edit Buku</h1>
    <?php if (isset($error)): ?>
        <p style="color:red;"><?= htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="POST" action="" enctype="multipart/form-data" class="form">
        <label for="judul">Judul Buku:</label>
        <input type="text" name="judul" id="judul" value="<?= htmlspecialchars($buku['judul']); ?>" required>

        <label for="pengarang">Pengarang:</label>
        <input type="text" name="pengarang" id="pengarang" value="<?= htmlspecialchars($buku['pengarang']); ?>" required>

        <label for="tahun_terbit">Tahun Terbit:</label>
        <input type="number" name="tahun_terbit" id="tahun_terbit" value="<?= htmlspecialchars($buku['tahun_terbit']); ?>" required>

        <label for="cover">Cover Buku:</label>
        <input type="file" name="cover" id="cover" accept="image/*">
        <?php if (!empty($buku['cover'])): ?>
            <p>Cover saat ini:</p>
            <img src="uploads/<?= htmlspecialchars($buku['cover']); ?>" alt="Cover Buku" style="max-width: 100px; margin-bottom: 10px;">
        <?php endif; ?>

        <button type="submit" class="add-button">Update</button>
    </form>
</div>
</body>
</html>
