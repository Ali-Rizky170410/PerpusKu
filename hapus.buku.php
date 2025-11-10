<?php
// Sisipkan file conn$connect ke database Anda
// Pastikan path filenya sudah benar
include 'connect.php';

// 1. Ambil ID dari URL
// Memastikan ID yang diterima adalah angka untuk keamanan
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 2. Buat query SQL untuk menghapus data
// Gunakan prepared statement untuk mencegah SQL Injection
$sql = "DELETE FROM buku WHERE id = ?";
$stmt = $conn->prepare($sql);

// Jika statement gagal disiapkan, hentikan eksekusi
if ($stmt === false) {
    die("Error preparing statement: " . $connect->error);
}

// 3. Bind parameter ID ke query
$stmt->bind_param("i", $id);

// 4. Eksekusi query dan berikan feedback
if ($stmt->execute()) {
    // Jika berhasil, redirect kembali ke halaman utama dengan pesan sukses
    echo "<script>
            alert('Data buku berhasil dihapus!');
            window.location.href='edit.buku.php'; 
          </script>";
} else {
    // Jika gagal, tampilkan pesan error dan redirect
    echo "<script>
            alert('Gagal menghapus data buku. Error: " . htmlspecialchars($stmt->error) . "');
            window.location.href='index.php';
          </script>";
}

// 5. Tutup statement dan conn$connect
$stmt->close();
$connect->close();

?>