<?php
session_start();
require 'koneksi.php'; // Pastikan file koneksi ke database ada

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    $query = "SELECT * FROM admin WHERE username='$username' AND password='$password'";
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        $_SESSION['admin'] = $username;
        header('Location: dashboard.php');
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Login Admin</h1>
    <!-- Tampilkan error jika ada -->
    <?php if (isset($error)): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="POST" class="form">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>

        <button type="submit" class="add-button">Login</button>
    </form>
</div>
</body>
</html>
