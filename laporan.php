<?php
require 'koneksi.php';

// Ambil data buku dari database
$sql = "SELECT * FROM buku";
$result = $conn->query($sql);
$buku = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $buku[] = $row;
    }
}

// Fungsi untuk mengunduh laporan PDF
if (isset($_GET['download']) && $_GET['download'] == 'pdf') {
    // ... (kode PDF tetap sama karena PDF memang sulit menampilkan gambar)
}

// Fungsi untuk mengunduh laporan Excel
if (isset($_GET['download']) && $_GET['download'] === 'excel') {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=daftar_buku.xls");

    echo "<table border='1'>";
    echo "<tr>";
    echo "<th>ID</th>";
    echo "<th>Judul</th>";
    echo "<th>Pengarang</th>";
    echo "<th>Tahun Terbit</th>";
    echo "<th>Cover</th>";
    echo "</tr>";

    foreach ($buku as $row) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['judul']}</td>";
        echo "<td>{$row['pengarang']}</td>";
        echo "<td>{$row['tahun_terbit']}</td>";

        if (!empty($row['cover'])) {
            $imagePath = 'uploads/' . $row['cover'];
            if (file_exists($imagePath)) {
                $base64Image = base64_encode(file_get_contents($imagePath));
                $imgTag = "<img src='data:image/jpeg;base64,{$base64Image}' width='50'>";
                echo "<td>{$imgTag}</td>";
            } else {
                echo "<td>Tidak Ada</td>";
            }
        } else {
            echo "<td>Tidak Ada</td>";
        }

        echo "</tr>";
    }

    echo "</table>";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Laporan Buku</title>
    <link rel="stylesheet" href="style.css">
    <style>
        @media print {
            img {
                max-width: 100px;
                height: auto;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Laporan Semua Buku</h1>
    <table border="1" cellpadding="5" cellspacing="0" style="width: 100%; text-align: left;">
        <thead>
            <tr>
                <th style="width: 5%;">ID</th>
                <th style="width: 30%;">Judul</th>
                <th style="width: 25%;">Pengarang</th>
                <th style="width: 15%;">Tahun Terbit</th>
                <th style="width: 25%;">Cover</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($buku) > 0): ?>
                <?php foreach ($buku as $data): ?>
                    <tr>
                        <td><?= htmlspecialchars($data['id']); ?></td>
                        <td><?= htmlspecialchars($data['judul']); ?></td>
                        <td><?= htmlspecialchars($data['pengarang']); ?></td>
                        <td><?= htmlspecialchars($data['tahun_terbit']); ?></td>
                        <td>
                            <?php if (!empty($data['cover']) && file_exists('uploads/' . $data['cover'])): ?>
                                <img src="uploads/<?= htmlspecialchars($data['cover']); ?>" alt="Cover" style="width: 50px; height: 75px;">
                            <?php else: ?>
                                Tidak Ada Cover
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align: center;">Tidak ada data buku</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <br>
    <div class="no-print">
        <button onclick="window.print()">Print</button>
        <a href="dashboard.php" class="add-button">Kembali ke Dashboard</a>
    </div>
</div>
</body>
</html>