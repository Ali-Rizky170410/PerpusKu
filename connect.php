<?php
$host = "localhost";
$user = "root";     // username database
$pass = "";         // password database (biasanya kosong di XAMPP)
$db   = "perpus"; // nama database

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
