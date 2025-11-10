<?php
/**
 * File: export_csv.php
 * Fungsi: Export data transaksi ke format CSV
 */

// Koneksi Database
$host = "localhost";
$user = "root";
$pass = "";
$database = "perpus";
$koneksi = mysqli_connect($host, $user, $pass, $database);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Logika Pencarian (sama seperti di laporan_transaksi.php)
$search_query = "";
$sql_where = "";
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search_query = mysqli_real_escape_string($koneksi, trim($_GET['search']));
    $sql_where = " WHERE t.id LIKE '%$search_query%' OR c.nama LIKE '%$search_query%' OR b.judul LIKE '%$search_query%'";
}

// Query Data Transaksi
$query = "SELECT 
            t.id AS transaksi_id,
            DATE_FORMAT(t.tanggal_beli, '%d/%m/%Y %H:%i') AS tanggal_beli,
            c.nama AS nama_customer,
            c.email AS email_customer,
            c.telepon AS telepon_customer,
            c.alamat AS alamat_customer,
            b.judul AS judul_buku,
            b.penulis AS penulis_buku,
            t.jumlah,
            t.total_harga,
            t.metode_pembayaran
          FROM transaksi t
          JOIN customer c ON t.customer_id = c.id
          JOIN buku b ON t.buku_id = b.id
          $sql_where
          ORDER BY t.tanggal_beli DESC";

$result = mysqli_query($koneksi, $query);

if (!$result) {
    die("Query gagal: " . mysqli_error($koneksi));
}

// Set header untuk download file CSV
$filename = "Laporan_Transaksi_" . date('YmdHis') . ".csv";
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Buka output stream
$output = fopen('php://output', 'w');

// Tulis BOM untuk UTF-8 (agar Excel bisa baca karakter khusus dengan benar)
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Header kolom CSV
$headers = [
    'ID Transaksi',
    'Tanggal Beli',
    'Nama Customer',
    'Email',
    'Telepon',
    'Alamat',
    'Judul Buku',
    'Penulis',
    'Jumlah',
    'Total Harga',
    'Metode Pembayaran'
];

fputcsv($output, $headers);

// Tulis data transaksi
while ($row = mysqli_fetch_assoc($result)) {
    $data = [
        $row['transaksi_id'],
        $row['tanggal_beli'],
        $row['nama_customer'],
        $row['email_customer'],
        $row['telepon_customer'],
        $row['alamat_customer'],
        $row['judul_buku'],
        $row['penulis_buku'],
        $row['jumlah'],
        'Rp' . number_format($row['total_harga'], 0, ',', '.'),
        $row['metode_pembayaran']
    ];
    
    fputcsv($output, $data);
}

// Tutup koneksi
fclose($output);
mysqli_close($koneksi);
exit();
?>