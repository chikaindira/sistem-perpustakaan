<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: index.php');
    exit();
}

require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = $_POST['judul'];
    $pengarang = $_POST['pengarang'];
    $tahun_terbit = $_POST['tahun_terbit'];
    $cover = $_FILES['cover']['name'];

    // Jika ada file yang diupload, pindahkan file ke direktori uploads
    if ($_FILES['cover']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($cover);
        move_uploaded_file($_FILES['cover']['tmp_name'], $target_file);
    }

    // Mencegah SQL Injection dengan prepared statement
    $stmt = $conn->prepare("INSERT INTO buku (judul, pengarang, tahun_terbit, cover) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $judul, $pengarang, $tahun_terbit, $cover);

    if ($stmt->execute()) {
        header('Location: dashboard.php');
        exit();
    } else {
        $error = "Gagal menambahkan buku: " . $conn->error;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tambah Buku</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Tambah Buku</h1>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?= htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form action="" method="POST" enctype="multipart/form-data" class="form">
        <label for="judul">Judul Buku:</label>
        <input type="text" name="judul" id="judul" required>

        <label for="pengarang">Pengarang:</label>
        <input type="text" name="pengarang" id="pengarang" required>

        <label for="tahun_terbit">Tahun Terbit:</label>
        <input type="number" name="tahun_terbit" id="tahun_terbit" required>

        <label for="cover">Cover Buku:</label>
        <input type="file" name="cover" id="cover" accept="image/*">

        <button type="submit" class="add-button">Tambah</button>
    </form>
</div>
</body>
</html>
