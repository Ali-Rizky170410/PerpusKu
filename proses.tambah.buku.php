<?php
/**
 * File: proses.tambah.buku.php
 * Fungsi: Memproses penambahan data buku baru ke database
 */

// Koneksi database langsung
$host = "localhost";
$user = "root";
$pass = "";
$database = "perpus";

// Membuat koneksi
$koneksi = mysqli_connect($host, $user, $pass, $database);

// Cek koneksi
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set charset untuk menghindari masalah encoding
mysqli_set_charset($koneksi, "utf8");

// Cek apakah request method adalah POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Validasi: Cek apakah semua field yang required ada
    $required_fields = ['judul', 'penulis', 'harga', 'gambar_url', 'kategori'];
    $errors = [];
    
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
            $errors[] = "Field " . $field . " harus diisi!";
        }
    }
    
    // Jika ada error validasi
    if (!empty($errors)) {
        $error_message = implode("<br>", $errors);
        die("<div style='padding: 20px; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px;'>
                <strong>Error Validasi:</strong><br>$error_message<br><br>
                <a href='create.buku.php'>Kembali ke Form</a>
            </div>");
    }
    
    // Ambil dan bersihkan data dari form
    $judul = mysqli_real_escape_string($koneksi, trim($_POST['judul']));
    $penulis = mysqli_real_escape_string($koneksi, trim($_POST['penulis']));
    $penerbit = isset($_POST['penerbit']) ? mysqli_real_escape_string($koneksi, trim($_POST['penerbit'])) : '';
    $kategori = mysqli_real_escape_string($koneksi, trim($_POST['kategori']));
    $harga = mysqli_real_escape_string($koneksi, trim($_POST['harga']));
    $gambar_url = mysqli_real_escape_string($koneksi, trim($_POST['gambar_url']));
    
    // Validasi kategori (harus salah satu dari daftar yang valid)
    $valid_categories = ['Novel', 'Komik', 'Majalah', 'Edukasi'];
    if (!in_array($kategori, $valid_categories)) {
        die("<div style='padding: 20px; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px;'>
                <strong>Error:</strong> Kategori tidak valid!<br><br>
                <a href='create.buku.php'>Kembali ke Form</a>
            </div>");
    }
    
    // Validasi harga harus angka
    if (!is_numeric($harga) || $harga <= 0) {
        die("<div style='padding: 20px; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px;'>
                <strong>Error:</strong> Harga harus berupa angka positif (lebih dari 0)!<br><br>
                <a href='create.buku.php'>Kembali ke Form</a>
            </div>");
    }
    
    // Validasi URL gambar harus dimulai dengan http/https
    if (!filter_var($gambar_url, FILTER_VALIDATE_URL) || !preg_match('/^https?:\/\//i', $gambar_url)) {
        die("<div style='padding: 20px; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px;'>
                <strong>Error:</strong> URL gambar tidak valid! Harus dimulai dengan http:// atau https://<br><br>
                <a href='create.buku.php'>Kembali ke Form</a>
            </div>");
    }
    
    // Query INSERT ke database
    // Menambahkan kolom kategori ke dalam query
    $query = "INSERT INTO buku (judul, penulis, penerbit, kategori, harga, gambar_url, tanggal_ditambahkan) 
              VALUES ('$judul', '$penulis', '$penerbit', '$kategori', '$harga', '$gambar_url', NOW())";
    
    // Eksekusi query
    if (mysqli_query($koneksi, $query)) {
        // Berhasil - Redirect dengan pesan sukses ke create.buku.php
        mysqli_close($koneksi);
        header("Location: create.buku.php?status=sukses");
        exit();
    } else {
        // Gagal - Tampilkan error untuk debugging
        $error = mysqli_error($koneksi);
        mysqli_close($koneksi);
        
        // Tampilkan pesan error yang lebih informatif
        die("<div style='padding: 20px; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px;'>
                <strong>Error Database:</strong><br>
                $error<br><br>
                <strong>Query:</strong><br>
                <code>$query</code><br><br>
                <a href='create.buku.php'>Kembali ke Form</a>
            </div>");
    }
    
} else {
    // Jika diakses langsung tanpa POST (akses langsung via URL)
    mysqli_close($koneksi);
    header("Location: create.buku.php");
    exit();
}
?>