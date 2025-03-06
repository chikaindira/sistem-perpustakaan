<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: index.php');
    exit();
}
require 'koneksi.php';

$search_query = "";
if (isset($_POST['search'])) {
    $search_query = $_POST['search'];
    $stmt = $conn->prepare("SELECT * FROM buku WHERE judul LIKE ? OR pengarang LIKE ?");
    $search_param = "%" . $search_query . "%";
    $stmt->bind_param("ss", $search_param, $search_param);
    $stmt->execute();
    $buku = $stmt->get_result();
} else {
    $buku = $conn->query("SELECT * FROM buku");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Dashboard Admin</h1>
        <div class="search-form">
            <form action="" method="post">
                <input type="text" name="search" value="<?= htmlspecialchars($search_query); ?>" placeholder="Cari buku..." class="search-input" />
                <button type="submit" class="search-button">Cari</button>
            </form>
        </div>

        <h2>Daftar Buku</h2>
        <a href="tambah_buku.php" class="add-button">Tambah Buku</a>
        <a href="laporan.php" class="add-button">Lihat Laporan</a>


        <table class="book-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Judul</th>
                    <th>Pengarang</th>
                    <th>Tahun Terbit</th>
                    <th>Cover</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $buku->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id']; ?></td>
                    <td><?= $row['judul']; ?></td>
                    <td><?= $row['pengarang']; ?></td>
                    <td><?= $row['tahun_terbit']; ?></td>
                    <td>
                        <?php if ($row['cover']): ?>
                            <img src="uploads/<?= htmlspecialchars($row['cover']); ?>" alt="Cover Buku" class="book-cover">
                        <?php else: ?>
                            <p class="no-cover">No Cover</p>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="edit_buku.php?id=<?= $row['id']; ?>" class="edit-button">Edit</a> |
                        <a href="hapus_buku.php?id=<?= $row['id']; ?>" class="delete-button" onclick="return confirm('Yakin ingin menghapus buku ini?')">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <a href="logout.php" class="logout-button">Logout</a>
    </div>
</body>
</html>
