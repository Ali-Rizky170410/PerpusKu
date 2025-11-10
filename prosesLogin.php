<?php
session_start();
include "connect.php"; // koneksi, pastikan $conn sudah ada

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Gunakan prepared statement
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Cek password dengan hash
        if (password_verify($password, $user['password'])) {
    $_SESSION['username'] = $user['username'];
    $_SESSION['status']   = $user['status'];

    if ($user['status'] === "staf") {
        header("Location: dashboard_staf.php");
        exit;
    } else {
        header("Location: dashboard_umum.php");
        exit;
    }
} else {
    echo "<script>alert('Password salah!'); window.location='login.php';</script>";
}
    } else {
        echo "<script>alert('Username tidak ditemukan!'); window.location='login.php';</script>";
        exit();
    }
}
?>
