<?php
// FILE: logout.php

// 1. Memulai atau melanjutkan sesi yang sudah ada
session_start();

// 2. Menghapus semua variabel sesi
$_SESSION = array();

// 3. Menghancurkan sesi
session_destroy();

// 4. Mengarahkan pengguna kembali ke halaman login (atau halaman utama)
header("Location: login.php");
exit;
?>
