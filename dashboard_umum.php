<?php
// FILE: dashboard_umum.php

$host = "localhost";
$user = "root";
$pass = "";
$database = "perpus";
$koneksi = mysqli_connect($host, $user, $pass, $database);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// --- LOGIKA PENCARIAN DAN KATEGORI ---
$search_query = "";
$kategori = "";
$sql_where = "";

if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search_query = mysqli_real_escape_string($koneksi, trim($_GET['search']));
    $sql_where = " WHERE judul LIKE '%$search_query%' OR penulis LIKE '%$search_query%'";
}

if (isset($_GET['kategori']) && !empty($_GET['kategori'])) {
    $kategori = mysqli_real_escape_string($koneksi, $_GET['kategori']);
    if (!empty($sql_where)) {
        $sql_where .= " AND kategori = '$kategori'";
    } else {
        $sql_where = " WHERE kategori = '$kategori'";
    }
}
// --- LOGIKA PENCARIAN DAN KATEGORI SELESAI ---

// Cek apakah sedang dalam mode beli
$mode_beli = isset($_GET['beli']) && isset($_GET['id']);
$mode_pinjam = isset($_GET['pinjam']) && isset($_GET['id']);
$buku_beli = null;
$buku_pinjam = null;

if ($mode_beli) {
    $id_beli = intval($_GET['id']);
    $query_beli = "SELECT * FROM buku WHERE id = $id_beli";
    $result_beli = mysqli_query($koneksi, $query_beli);
    
    if ($result_beli && mysqli_num_rows($result_beli) > 0) {
        $buku_beli = mysqli_fetch_assoc($result_beli);
    } else {
        header("Location: dashboard_umum.php");
        exit();
    }
}

if ($mode_pinjam) {
    $id_pinjam = intval($_GET['id']);
    $query_pinjam = "SELECT * FROM buku WHERE id = $id_pinjam";
    $result_pinjam = mysqli_query($koneksi, $query_pinjam);
    
    if ($result_pinjam && mysqli_num_rows($result_pinjam) > 0) {
        $buku_pinjam = mysqli_fetch_assoc($result_pinjam);
    } else {
        header("Location: dashboard_umum.php");
        exit();
    }
}

// Proses form pembelian saat disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['proses_beli'])) {
    $buku_id = intval($_POST['buku_id']);
    $nama = mysqli_real_escape_string($koneksi, trim($_POST['nama']));
    $email = mysqli_real_escape_string($koneksi, trim($_POST['email']));
    $telepon = mysqli_real_escape_string($koneksi, trim($_POST['telepon']));
    $alamat = mysqli_real_escape_string($koneksi, trim($_POST['alamat']));
    $jumlah = intval($_POST['jumlah']);
    $total_harga = floatval($_POST['total_harga']);
    $metode_pembayaran = mysqli_real_escape_string($koneksi, trim($_POST['metode_pembayaran']));
    
    $check_customer = "SELECT id FROM customer WHERE email = '$email'";
    $result_customer = mysqli_query($koneksi, $check_customer);
    
    if (mysqli_num_rows($result_customer) > 0) {
        $customer = mysqli_fetch_assoc($result_customer);
        $customer_id = $customer['id'];
    } else {
        $insert_customer = "INSERT INTO customer (nama, email, telepon, alamat) VALUES ('$nama', '$email', '$telepon', '$alamat')";
        if (mysqli_query($koneksi, $insert_customer)) {
            $customer_id = mysqli_insert_id($koneksi);
        } else {
            $error_message = "Error membuat customer: " . mysqli_error($koneksi);
            $customer_id = 0;
        }
    }
    
    if ($customer_id > 0) {
        $insert_transaksi = "INSERT INTO transaksi (customer_id, buku_id, jumlah, total_harga, metode_pembayaran, status_pembayaran) VALUES ($customer_id, $buku_id, $jumlah, $total_harga, '$metode_pembayaran', 'pending')";
        if (mysqli_query($koneksi, $insert_transaksi)) {
            $transaksi_id = mysqli_insert_id($koneksi);
            header("Location: dashboard_umum.php?status=pembelian_sukses&transaksi_id=$transaksi_id");
            exit();
        } else {
            $error_message = "Error membuat transaksi: " . mysqli_error($koneksi);
        }
    }
}

// Proses form peminjaman saat disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['proses_pinjam'])) {
    $buku_id = intval($_POST['buku_id']);
    $nama = mysqli_real_escape_string($koneksi, trim($_POST['nama']));
    $email = mysqli_real_escape_string($koneksi, trim($_POST['email']));
    $telepon = mysqli_real_escape_string($koneksi, trim($_POST['telepon']));
    $alamat = mysqli_real_escape_string($koneksi, trim($_POST['alamat']));
    $tanggal_pinjam = mysqli_real_escape_string($koneksi, $_POST['tanggal_pinjam']);
    $tanggal_kembali = mysqli_real_escape_string($koneksi, $_POST['tanggal_kembali']);
    
    $check_peminjam = "SELECT id FROM peminjam WHERE email = '$email'";
    $result_peminjam = mysqli_query($koneksi, $check_peminjam);
    
    if (mysqli_num_rows($result_peminjam) > 0) {
        $peminjam = mysqli_fetch_assoc($result_peminjam);
        $peminjam_id = $peminjam['id'];
    } else {
        $insert_peminjam = "INSERT INTO peminjam (nama, email, telepon, alamat) VALUES ('$nama', '$email', '$telepon', '$alamat')";
        if (mysqli_query($koneksi, $insert_peminjam)) {
            $peminjam_id = mysqli_insert_id($koneksi);
        } else {
            $error_message_pinjam = "Error membuat data peminjam: " . mysqli_error($koneksi);
            $peminjam_id = 0;
        }
    }
    
    if ($peminjam_id > 0) {
        $insert_peminjaman = "INSERT INTO peminjaman (peminjam_id, buku_id, tanggal_pinjam, tanggal_kembali, status) VALUES ($peminjam_id, $buku_id, '$tanggal_pinjam', '$tanggal_kembali', 'aktif')";
        if (mysqli_query($koneksi, $insert_peminjaman)) {
            $peminjaman_id = mysqli_insert_id($koneksi);
            header("Location: dashboard_umum.php?status=peminjaman_sukses&peminjaman_id=$peminjaman_id");
            exit();
        } else {
            $error_message_pinjam = "Error membuat peminjaman: " . mysqli_error($koneksi);
        }
    }
}

// Mendapatkan data transaksi untuk keperluan cetak
$transaksi_data = null;
if (isset($_GET['status']) && $_GET['status'] == 'pembelian_sukses' && isset($_GET['transaksi_id'])) {
    $transaksi_id = intval($_GET['transaksi_id']);
    
    $query_transaksi = "SELECT t.*, c.nama, c.email, c.telepon, c.alamat, b.judul, b.penulis, b.harga 
                       FROM transaksi t 
                       JOIN customer c ON t.customer_id = c.id 
                       JOIN buku b ON t.buku_id = b.id 
                       WHERE t.id = $transaksi_id";
    
    $result_transaksi = mysqli_query($koneksi, $query_transaksi);
    if ($result_transaksi && mysqli_num_rows($result_transaksi) > 0) {
        $transaksi_data = mysqli_fetch_assoc($result_transaksi);
    }
}

// Mendapatkan data peminjaman untuk keperluan cetak
$peminjaman_data = null;
if (isset($_GET['status']) && $_GET['status'] == 'peminjaman_sukses' && isset($_GET['peminjaman_id'])) {
    $peminjaman_id = intval($_GET['peminjaman_id']);
    
    $query_peminjaman = "SELECT p.*, pm.nama, pm.email, pm.telepon, pm.alamat, b.judul, b.penulis 
                        FROM peminjaman p 
                        JOIN peminjam pm ON p.peminjam_id = pm.id 
                        JOIN buku b ON p.buku_id = b.id 
                        WHERE p.id = $peminjaman_id";
    
    $result_peminjaman = mysqli_query($koneksi, $query_peminjaman);
    if ($result_peminjaman && mysqli_num_rows($result_peminjaman) > 0) {
        $peminjaman_data = mysqli_fetch_assoc($result_peminjaman);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <title>Perpusku</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="styles.css" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap');
            
            body {
                font-family: 'Poppins', sans-serif;
            }

            /* CSS untuk Card Produk */
            .card-product { transition: transform 0.2s ease, box-shadow 0.2s ease; border-radius: 0.75rem; }
            .card-product:hover { transform: translateY(-5px); box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important; }
            .card-img-top-container { background-color: #f8f9fa; width: 123px; height: 158px; margin: 1rem auto 0; overflow: hidden; border-radius: 0.5rem; }
            .card-img-top-container img { width: 100%; height: 100%; object-fit: cover; }
            .badge-stock { position: absolute; top: 10px; right: 10px; z-index: 10; }
            
            /* CSS untuk Modal */
            .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 1050; display: flex; align-items: center; justify-content: center; animation: fadeIn 0.3s; }
            .modal-content-custom { background: white; border-radius: 15px; padding: 30px; max-width: 700px; width: 90%; max-height: 90vh; overflow-y: auto; box-shadow: 0 10px 40px rgba(0,0,0,0.3); animation: slideUp 0.3s; }
            @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
            @keyframes slideUp { from { transform: translateY(50px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

            /* CSS untuk BAR KATEGORI */
            .category-bar {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                box-shadow: 0 4px 15px rgba(0,0,0,0.1);
                padding: 1rem 0;
                position: sticky;
                top: 0;
                z-index: 1020;
            }
            
            .category-btn {
                background: rgba(255, 255, 255, 0.2);
                border: 2px solid rgba(255, 255, 255, 0.3);
                color: white;
                padding: 0.75rem 2rem;
                border-radius: 50px;
                font-weight: 600;
                transition: all 0.3s ease;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                margin: 0.25rem;
            }
            
            .category-btn:hover {
                background: rgba(255, 255, 255, 0.3);
                border-color: white;
                color: white;
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            }
            
            .category-btn.active {
                background: white;
                color: #667eea;
                border-color: white;
                box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            }
            
            .category-btn i {
                font-size: 1.2rem;
            }
            
            @media (max-width: 768px) {
                .category-btn {
                    padding: 0.5rem 1rem;
                    font-size: 0.875rem;
                }
            }

            /* CSS untuk cetak */
            .print-container {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: white;
                z-index: 9999;
                padding: 20px;
                overflow: auto;
            }
            
            .print-header {
                text-align: center;
                margin-bottom: 20px;
                border-bottom: 2px solid #333;
                padding-bottom: 10px;
            }
            
            .print-content {
                margin: 20px 0;
            }
            
            .print-footer {
                margin-top: 30px;
                text-align: center;
                font-size: 12px;
                color: #666;
            }
            
            @media print {
                body * {
                    visibility: hidden;
                }
                .print-container, .print-container * {
                    visibility: visible;
                }
                .print-container {
                    position: absolute;
                    left: 0;
                    top: 0;
                    display: block;
                }
                .no-print {
                    display: none !important;
                }
            }
            
            /* CSS UNTUK KARTU IKLAN */
            .promo-banner {
                background: linear-gradient(135deg, #e3f2fd, #f8e8f8);
                border-radius: 1.5rem;
                overflow: hidden;
                position: relative;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            }
            .promo-banner::before { content: ''; position: absolute; top: -50px; left: -50px; width: 200px; height: 200px; background-color: rgba(255, 255, 255, 0.3); border-radius: 50%; filter: blur(20px); }
            .promo-banner::after { content: ''; position: absolute; bottom: -80px; right: -80px; width: 300px; height: 300px; background-color: rgba(187, 134, 252, 0.1); border-radius: 50%; filter: blur(30px); }
            .promo-content { position: relative; z-index: 10; }
            .promo-title { font-weight: 800; font-size: 3.5rem; color: #343a40; line-height: 1.2; }
            .promo-subtitle { font-size: 1.25rem; color: #495057; }
            .promo-image-container { display: flex; align-items: center; justify-content: center; }
            .promo-image { max-width: 100%; transform: rotate(-5deg) scale(1.1); transition: transform 0.3s ease-in-out; }
            .promo-banner:hover .promo-image { transform: rotate(0deg) scale(1.05); }
            .btn-promo { font-weight: 600; padding: 0.75rem 2rem; border-radius: 50px; box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3); transition: all 0.3s ease; }
            .btn-promo:hover { transform: translateY(-3px); box-shadow: 0 7px 20px rgba(0, 123, 255, 0.4); }
            @media (max-width: 768px) {
                .promo-title { font-size: 2.5rem; }
                .promo-banner .text-center-md { text-align: center !important; }
            }

            /* CSS untuk tombol Beli dan Pinjam */
            .btn-action {
                padding: 0.5rem 0.75rem;
                font-size: 0.875rem;
                gap: 0.5rem;
            }
        </style>
    </head>
    <body>
        <!-- NAVBAR -->
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <a class="navbar-brand ps-3 d-flex align-items-center" href="dashboard_umum.php">
                <img src="logo perpus.ico" alt="Perpus Icon" width="60" height="60" class="me-2">
                <span style="font-size: 1.25rem; font-weight: 600;">Buka Baca Buku</span>
            </a>
            <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0" method="GET" action="dashboard_umum.php">
                <?php if (!empty($kategori)): ?>
                <input type="hidden" name="kategori" value="<?php echo htmlspecialchars($kategori); ?>">
                <?php endif; ?>
                <div class="input-group">
                    <input class="form-control" type="text" name="search" placeholder="Cari judul atau penulis..." value="<?php echo htmlspecialchars($search_query); ?>" />
                    <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                </div>
            </form>
            <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
                <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
            </ul>
        </nav>
        
        <!-- BAR KATEGORI -->
        <div class="category-bar">
            <div class="container-fluid px-4">
                <div class="d-flex flex-wrap justify-content-center align-items-center">
                    <a href="dashboard_umum.php" class="category-btn <?php echo empty($kategori) ? 'active' : ''; ?>">
                        <i class="fas fa-home"></i>
                        <span>Semua Buku</span>
                    </a>
                    <a href="?kategori=Novel" class="category-btn <?php echo $kategori == 'Novel' ? 'active' : ''; ?>">
                        <i class="fas fa-book"></i>
                        <span>Novel</span>
                    </a>
                    <a href="?kategori=Komik" class="category-btn <?php echo $kategori == 'Komik' ? 'active' : ''; ?>">
                        <i class="fas fa-book-open"></i>
                        <span>Komik</span>
                    </a>
                    <a href="?kategori=Majalah" class="category-btn <?php echo $kategori == 'Majalah' ? 'active' : ''; ?>">
                        <i class="fas fa-newspaper"></i>
                        <span>Majalah</span>
                    </a>
                    <a href="?kategori=Edukasi" class="category-btn <?php echo $kategori == 'Edukasi' ? 'active' : ''; ?>">
                        <i class="fas fa-graduation-cap"></i>
                        <span>Edukasi</span>
                    </a>
                </div>
            </div>
        </div>

        <div>
            <main>
                <div class="container-fluid px-4">
                    <?php if ($mode_beli && $buku_beli): ?>
                    <!-- MODAL PEMBELIAN -->
                    <div class="modal-overlay">
                        <div class="modal-content-custom">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h3><i class="fas fa-shopping-cart text-success"></i> Form Pembelian</h3>
                                <a href="dashboard_umum.php<?php echo !empty($kategori) ? '?kategori='.$kategori : ''; ?>" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-times"></i> Tutup
                                </a>
                            </div>
                            <?php if (isset($error_message)): ?><div class="alert alert-danger"><?php echo $error_message; ?></div><?php endif; ?>
                            <div class="row mb-4">
                                <div class="col-md-4"><img src="<?php echo htmlspecialchars($buku_beli['gambar_url']); ?>" alt="Cover" class="img-fluid rounded shadow"></div>
                                <div class="col-md-8">
                                    <h4><?php echo htmlspecialchars($buku_beli['judul']); ?></h4>
                                    <p class="text-muted mb-2">Penulis: <?php echo htmlspecialchars($buku_beli['penulis']); ?></p>
                                    <?php if (!empty($buku_beli['kategori'])): ?>
                                    <span class="badge bg-info mb-2"><?php echo htmlspecialchars($buku_beli['kategori']); ?></span>
                                    <?php endif; ?>
                                    <h3 class="text-primary">Rp<?php echo number_format($buku_beli['harga'], 0, ',', '.'); ?></h3>
                                </div>
                            </div>
                            <form method="POST" action="">
                                <input type="hidden" name="buku_id" value="<?php echo $buku_beli['id']; ?>">
                                <input type="hidden" name="proses_beli" value="1">
                                <input type="hidden" name="total_harga" id="total_harga" value="<?php echo $buku_beli['harga']; ?>">
                                <h5 class="mb-3">Data Pembeli</h5>
                                <div class="mb-3"><label class="form-label">Nama Lengkap*</label><input type="text" class="form-control" name="nama" placeholder="Masukkan nama lengkap Anda" required></div>
                                <div class="mb-3"><label class="form-label">Email*</label><input type="email" class="form-control" name="email" placeholder="contoh: email@anda.com" required></div>
                                <div class="mb-3"><label class="form-label">No. Telepon*</label><input type="text" class="form-control" name="telepon" placeholder="contoh: 081234567890" required></div>
                                <div class="mb-3"><label class="form-label">Alamat Pengiriman*</label><textarea class="form-control" name="alamat" rows="3" placeholder="Masukkan alamat lengkap untuk pengiriman" required></textarea></div>
                                <hr>
                                <h5 class="mb-3">Detail Pesanan</h5>
                                <div class="mb-3"><label class="form-label">Jumlah*</label><input type="number" class="form-control" name="jumlah" id="jumlah" value="1" min="1" required onchange="hitungTotal(<?php echo $buku_beli['harga']; ?>)"></div>
                                <div class="mb-3"><label class="form-label">Metode Pembayaran*</label><select class="form-select" name="metode_pembayaran" required><option value="">-- Pilih Metode --</option><option value="Transfer Bank">Transfer Bank</option><option value="E-Wallet">E-Wallet (GoPay/OVO/Dana)</option><option value="COD">COD (Bayar di Tempat)</option></select></div>
                                <div class="alert alert-info"><strong>Total Pembayaran: </strong><span class="fs-4 text-primary" id="display_total">Rp<?php echo number_format($buku_beli['harga'], 0, ',', '.'); ?></span></div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Konfirmasi Pembelian</button>
                                    <button type="button" class="btn btn-info" onclick="printPreview()"><i class="fas fa-print"></i> Cetak Transaksi</button>
                                    <a href="dashboard_umum.php<?php echo !empty($kategori) ? '?kategori='.$kategori : ''; ?>" class="btn btn-secondary"><i class="fas fa-times"></i> Batal</a>
                                </div>
                            </form>
                        </div>
                    </div>
                    <script>
                        function hitungTotal(harga) { 
                            var jumlah = document.getElementById('jumlah').value; 
                            var total = harga * jumlah; 
                            document.getElementById('total_harga').value = total; 
                            document.getElementById('display_total').innerText = 'Rp' + total.toLocaleString('id-ID'); 
                        }
                        
                        function printPreview() {
                            var nama = document.querySelector('input[name="nama"]').value || '[Nama Pembeli]';
                            var email = document.querySelector('input[name="email"]').value || '[Email]';
                            var telepon = document.querySelector('input[name="telepon"]').value || '[No. Telepon]';
                            var alamat = document.querySelector('textarea[name="alamat"]').value || '[Alamat]';
                            var jumlah = document.querySelector('input[name="jumlah"]').value || '1';
                            var metode_pembayaran = document.querySelector('select[name="metode_pembayaran"]').value || '[Metode Pembayaran]';
                            var total_harga = document.getElementById('total_harga').value || '0';
                            var judul_buku = '<?php echo htmlspecialchars($buku_beli['judul']); ?>';
                            var penulis_buku = '<?php echo htmlspecialchars($buku_beli['penulis']); ?>';
                            var harga_buku = '<?php echo $buku_beli['harga']; ?>';
                            var tanggal = new Date().toLocaleDateString('id-ID');
                            
                            var printContent = `
                                <div class="print-container" id="printContainer">
                                    <div class="print-header">
                                        <h2>BUKA BACA BUKU</h2>
                                        <h3>BUKTI PEMBELIAN</h3>
                                        <p>Tanggal: ${tanggal}</p>
                                    </div>
                                    <div class="print-content">
                                        <h4>Detail Pembeli:</h4>
                                        <p>Nama: ${nama}</p>
                                        <p>Email: ${email}</p>
                                        <p>Telepon: ${telepon}</p>
                                        <p>Alamat: ${alamat}</p>
                                        <hr>
                                        <h4>Detail Buku:</h4>
                                        <p>Judul: ${judul_buku}</p>
                                        <p>Penulis: ${penulis_buku}</p>
                                        <p>Harga Satuan: Rp${parseInt(harga_buku).toLocaleString('id-ID')}</p>
                                        <p>Jumlah: ${jumlah}</p>
                                        <hr>
                                        <h4>Detail Pembayaran:</h4>
                                        <p>Metode Pembayaran: ${metode_pembayaran}</p>
                                        <p><strong>Total Pembayaran: Rp${parseInt(total_harga).toLocaleString('id-ID')}</strong></p>
                                    </div>
                                    <div class="print-footer">
                                        <p>Terima kasih telah berbelanja di Buka Baca Buku!</p>
                                        <p>Ini adalah dokumen cetak otomatis, tidak memerlukan tanda tangan.</p>
                                    </div>
                                </div>
                            `;
                            
                            document.body.insertAdjacentHTML('beforeend', printContent);
                            window.print();
                            
                            setTimeout(function() {
                                var printContainer = document.getElementById('printContainer');
                                if (printContainer) {
                                    printContainer.remove();
                                }
                            }, 1000);
                        }
                    </script>
                    <?php endif; ?>

                    <?php if ($mode_pinjam && $buku_pinjam): ?>
                    <!-- MODAL PEMINJAMAN -->
                    <div class="modal-overlay">
                        <div class="modal-content-custom">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h3><i class="fas fa-book text-info"></i> Form Peminjaman</h3>
                                <a href="dashboard_umum.php<?php echo !empty($kategori) ? '?kategori='.$kategori : ''; ?>" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-times"></i> Tutup
                                </a>
                            </div>
                            <?php if (isset($error_message_pinjam)): ?><div class="alert alert-danger"><?php echo $error_message_pinjam; ?></div><?php endif; ?>
                            <div class="row mb-4">
                                <div class="col-md-4"><img src="<?php echo htmlspecialchars($buku_pinjam['gambar_url']); ?>" alt="Cover" class="img-fluid rounded shadow"></div>
                                <div class="col-md-8">
                                    <h4><?php echo htmlspecialchars($buku_pinjam['judul']); ?></h4>
                                    <p class="text-muted mb-2">Penulis: <?php echo htmlspecialchars($buku_pinjam['penulis']); ?></p>
                                    <?php if (!empty($buku_pinjam['kategori'])): ?>
                                    <span class="badge bg-info mb-2"><?php echo htmlspecialchars($buku_pinjam['kategori']); ?></span>
                                    <?php endif; ?>
                                    <div class="alert alert-warning mt-3">
                                        <i class="fas fa-info-circle"></i> Durasi peminjaman maksimal 14 hari
                                    </div>
                                </div>
                            </div>
                            <form method="POST" action="">
                                <input type="hidden" name="buku_id" value="<?php echo $buku_pinjam['id']; ?>">
                                <input type="hidden" name="proses_pinjam" value="1">
                                <h5 class="mb-3">Data Peminjam</h5>
                                <div class="mb-3"><label class="form-label">Nama Lengkap*</label><input type="text" class="form-control" name="nama" placeholder="Masukkan nama lengkap Anda" required></div>
                                <div class="mb-3"><label class="form-label">Email*</label><input type="email" class="form-control" name="email" placeholder="contoh: email@anda.com" required></div>
                                <div class="mb-3"><label class="form-label">No. Telepon*</label><input type="text" class="form-control" name="telepon" placeholder="contoh: 081234567890" required></div>
                                <div class="mb-3"><label class="form-label">Alamat*</label><textarea class="form-control" name="alamat" rows="3" placeholder="Masukkan alamat Anda" required></textarea></div>
                                <hr>
                                <h5 class="mb-3">Tanggal Peminjaman</h5>
                                <div class="mb-3"><label class="form-label">Tanggal Pinjam*</label><input type="date" class="form-control" name="tanggal_pinjam" id="tanggal_pinjam" required></div>
                                <div class="mb-3"><label class="form-label">Tanggal Kembali*</label><input type="date" class="form-control" name="tanggal_kembali" id="tanggal_kembali" required></div>
                                <div class="alert alert-info" id="durasi_alert"><i class="fas fa-clock"></i> Durasi peminjaman: <strong id="durasi_text">-</strong> hari</div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Konfirmasi Peminjaman</button>
                                    <button type="button" class="btn btn-info" onclick="printPeminjamanPreview()"><i class="fas fa-print"></i> Cetak Peminjaman</button>
                                    <a href="dashboard_umum.php<?php echo !empty($kategori) ? '?kategori='.$kategori : ''; ?>" class="btn btn-secondary"><i class="fas fa-times"></i> Batal</a>
                                </div>
                            </form>
                        </div>
                    </div>
                    <script>
                        // Set tanggal pinjam ke hari ini
                        document.addEventListener('DOMContentLoaded', function() {
                            var today = new Date().toISOString().split('T')[0];
                            document.getElementById('tanggal_pinjam').value = today;
                            hitungDurasi();
                        });
                        
                        document.getElementById('tanggal_pinjam').addEventListener('change', hitungDurasi);
                        document.getElementById('tanggal_kembali').addEventListener('change', hitungDurasi);
                        
                        function hitungDurasi() {
                            var tanggal_pinjam = new Date(document.getElementById('tanggal_pinjam').value);
                            var tanggal_kembali = new Date(document.getElementById('tanggal_kembali').value);
                            
                            if (tanggal_pinjam && tanggal_kembali) {
                                var selisih = Math.floor((tanggal_kembali - tanggal_pinjam) / (1000 * 60 * 60 * 24));
                                document.getElementById('durasi_text').innerText = selisih + 1;
                                
                                if (selisih > 14) {
                                    document.getElementById('durasi_alert').className = 'alert alert-danger';
                                    document.getElementById('durasi_alert').innerHTML = '<i class="fas fa-exclamation-triangle"></i> Durasi peminjaman <strong>tidak boleh lebih dari 14 hari</strong>! Durasi saat ini: <strong>' + (selisih + 1) + ' hari</strong>';
                                } else if (selisih < 0) {
                                    document.getElementById('durasi_alert').className = 'alert alert-danger';
                                    document.getElementById('durasi_alert').innerHTML = '<i class="fas fa-exclamation-triangle"></i> Tanggal kembali tidak boleh lebih awal dari tanggal pinjam!';
                                } else {
                                    document.getElementById('durasi_alert').className = 'alert alert-info';
                                    document.getElementById('durasi_alert').innerHTML = '<i class="fas fa-clock"></i> Durasi peminjaman: <strong>' + (selisih + 1) + ' hari</strong>';
                                }
                            }
                        }
                        
                        function printPeminjamanPreview() {
                            var nama = document.querySelector('input[name="nama"]').value || '[Nama Peminjam]';
                            var email = document.querySelector('input[name="email"]').value || '[Email]';
                            var telepon = document.querySelector('input[name="telepon"]').value || '[No. Telepon]';
                            var alamat = document.querySelector('textarea[name="alamat"]').value || '[Alamat]';
                            var tanggal_pinjam = document.querySelector('input[name="tanggal_pinjam"]').value || '[Tanggal Pinjam]';
                            var tanggal_kembali = document.querySelector('input[name="tanggal_kembali"]').value || '[Tanggal Kembali]';
                            var judul_buku = '<?php echo htmlspecialchars($buku_pinjam['judul']); ?>';
                            var penulis_buku = '<?php echo htmlspecialchars($buku_pinjam['penulis']); ?>';
                            var tanggal_sekarang = new Date().toLocaleDateString('id-ID');
                            
                            var printContent = `
                                <div class="print-container" id="printContainer">
                                    <div class="print-header">
                                        <h2>BUKA BACA BUKU</h2>
                                        <h3>BUKTI PEMINJAMAN BUKU</h3>
                                        <p>Tanggal: ${tanggal_sekarang}</p>
                                    </div>
                                    <div class="print-content">
                                        <h4>Data Peminjam:</h4>
                                        <p>Nama: ${nama}</p>
                                        <p>Email: ${email}</p>
                                        <p>Telepon: ${telepon}</p>
                                        <p>Alamat: ${alamat}</p>
                                        <hr>
                                        <h4>Detail Buku:</h4>
                                        <p>Judul: ${judul_buku}</p>
                                        <p>Penulis: ${penulis_buku}</p>
                                        <hr>
                                        <h4>Tanggal Peminjaman:</h4>
                                        <p>Tanggal Pinjam: ${tanggal_pinjam}</p>
                                        <p>Tanggal Kembali: ${tanggal_kembali}</p>
                                    </div>
                                    <div class="print-footer">
                                        <p>Terima kasih telah meminjam buku di Buka Baca Buku!</p>
                                        <p>Harap kembalikan buku tepat pada waktunya.</p>
                                        <p>Ini adalah dokumen cetak otomatis, tidak memerlukan tanda tangan.</p>
                                    </div>
                                </div>
                            `;
                            
                            document.body.insertAdjacentHTML('beforeend', printContent);
                            window.print();
                            
                            setTimeout(function() {
                                var printContainer = document.getElementById('printContainer');
                                if (printContainer) {
                                    printContainer.remove();
                                }
                            }, 1000);
                        }
                    </script>
                    <?php endif; ?>
                    
                    <!-- KARTU IKLAN -->
                    <div class="promo-banner p-4 p-md-5 mb-4 mt-4">
                        <div class="row align-items-center promo-content">
                            <div class="col-md-6 text-center-md">
                                <h1 class="promo-title mb-3">Koleksi Terbaru</h1>
                                <p class="promo-subtitle mb-4">Temukan buku-buku dan majalah pilihan yang baru tiba minggu ini!</p>
                                <a href="https://www.gramedia.com" class="btn btn-primary btn-lg btn-promo">
                                    <i class="fas fa-book-open me-2"></i> Lihat Sekarang
                                </a>
                            </div>
                            <div class="col-md-6 mt-4 mt-md-0 promo-image-container">
                                <img src="waguri2.png" width="50%" alt="Promosi Buku Baru" class="promo-image">
                            </div>
                        </div>
                    </div>

                    <?php if (isset($_GET['status']) && $_GET['status'] == 'pembelian_sukses'): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Pembelian Berhasil!</strong> Transaksi Anda dengan ID: <?php echo htmlspecialchars($_GET['transaksi_id']); ?> telah dibuat.
                            <?php if ($transaksi_data): ?>
                            <button type="button" class="btn btn-info btn-sm ms-2" onclick="printTransaction(<?php echo htmlspecialchars($_GET['transaksi_id']); ?>)">
                                <i class="fas fa-print"></i> Cetak Transaksi
                            </button>
                            <?php endif; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_GET['status']) && $_GET['status'] == 'peminjaman_sukses'): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Peminjaman Berhasil!</strong> Peminjaman Anda dengan ID: <?php echo htmlspecialchars($_GET['peminjaman_id']); ?> telah dibuat.
                            <?php if ($peminjaman_data): ?>
                            <button type="button" class="btn btn-info btn-sm ms-2" onclick="printPeminjamanTransaction(<?php echo htmlspecialchars($_GET['peminjaman_id']); ?>)">
                                <i class="fas fa-print"></i> Cetak Bukti Peminjaman
                            </button>
                            <?php endif; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <!-- JUDUL KATEGORI -->
                    <?php if (!empty($kategori)): ?>
                    <h2 class="mt-4 mb-3">
                        <i class="fas fa-tags text-primary me-2"></i>
                        Kategori: <?php echo htmlspecialchars($kategori); ?>
                    </h2>
                    <?php elseif (!empty($search_query)): ?>
                    <h2 class="mt-4 mb-3">
                        <i class="fas fa-search text-primary me-2"></i>
                        Hasil Pencarian: "<?php echo htmlspecialchars($search_query); ?>"
                    </h2>
                    <?php endif; ?>
                    
                    <div class="row">
                        <?php
                            if ($koneksi) {
                            $query_buku = "SELECT * FROM buku" . $sql_where . " ORDER BY id DESC";
                            $result_buku = mysqli_query($koneksi, $query_buku);

                            if ($result_buku && mysqli_num_rows($result_buku) > 0) {
                                while($buku = mysqli_fetch_assoc($result_buku)) {
                        ?>
                        <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                            <div class="card card-product h-100 shadow-sm position-relative">
                                <span class="badge bg-success badge-stock">Tersedia</span>
                                <?php if (!empty($buku['kategori'])): ?>
                                <span class="badge bg-info" style="position: absolute; top: 10px; left: 10px; z-index: 10;">
                                    <?php echo htmlspecialchars($buku['kategori']); ?>
                                </span>
                                <?php endif; ?>
                                <div class="card-img-top-container"><img src="<?php echo htmlspecialchars($buku['gambar_url']); ?>" alt="Cover <?php echo htmlspecialchars($buku['judul']); ?>"></div>
                                <div class="card-body d-flex flex-column text-center pt-2">
                                    <div class="text-muted small"><?php echo htmlspecialchars($buku['penulis']); ?></div>
                                    <h5 class="card-title card-title-clamp fw-semibold mt-1 fs-6"><a href="#" class="text-decoration-none text-dark"><?php echo htmlspecialchars($buku['judul']); ?></a></h5>
                                    <p class="card-text fw-bold fs-5 mt-auto text-primary">Rp<?php echo number_format($buku['harga'], 0, ',', '.'); ?></p>
                                    <div class="d-grid gap-2 mt-2">
                                        <a href="?beli=true&id=<?php echo $buku['id']; ?><?php echo !empty($kategori) ? '&kategori='.$kategori : ''; ?>" class="btn btn-success btn-action btn-sm">
                                            <i class="fas fa-shopping-cart"></i> Beli
                                        </a>
                                        <a href="?pinjam=true&id=<?php echo $buku['id']; ?><?php echo !empty($kategori) ? '&kategori='.$kategori : ''; ?>" class="btn btn-info btn-action btn-sm">
                                            <i class="fas fa-book"></i> Pinjam
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                                }
                            } else {
                                if (!empty($search_query)) {
                                    echo '<div class="col-12"><div class="alert alert-warning">Buku dengan kata kunci "<strong>'.htmlspecialchars($search_query).'</strong>" tidak ditemukan.</div></div>';
                                } elseif (!empty($kategori)) {
                                    echo '<div class="col-12"><div class="alert alert-warning">Belum ada buku dalam kategori "<strong>'.htmlspecialchars($kategori).'</strong>".</div></div>';
                                } else {
                                    echo '<div class="col-12"><div class="alert alert-warning">Belum ada buku yang ditambahkan.</div></div>';
                                }
                            }
                            if(isset($koneksi)) { mysqli_close($koneksi); }
                        }
                        ?>
                    </div>
                </div>
            </main>
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; Buka Baca Buku 2024</div>
                        <div>
                            <a href="#">Privacy Policy</a>
                            &middot;
                            <a href="#">Terms &amp; Conditions</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="js/scripts.js"></script>
        
        <?php if ($transaksi_data): ?>
        <script>
            function printTransaction(transaksiId) {
                var printContent = `
                    <div class="print-container" id="printContainer">
                        <div class="print-header">
                            <h2>BUKA BACA BUKU</h2>
                            <h3>BUKTI PEMBELIAN</h3>
                            <p>ID Transaksi: #${transaksiId}</p>
                            <p>Tanggal: ${new Date().toLocaleDateString('id-ID')}</p>
                        </div>
                        <div class="print-content">
                            <h4>Detail Pembeli:</h4>
                            <p>Nama: <?php echo htmlspecialchars($transaksi_data['nama']); ?></p>
                            <p>Email: <?php echo htmlspecialchars($transaksi_data['email']); ?></p>
                            <p>Telepon: <?php echo htmlspecialchars($transaksi_data['telepon']); ?></p>
                            <p>Alamat: <?php echo htmlspecialchars($transaksi_data['alamat']); ?></p>
                            <hr>
                            <h4>Detail Buku:</h4>
                            <p>Judul: <?php echo htmlspecialchars($transaksi_data['judul']); ?></p>
                            <p>Penulis: <?php echo htmlspecialchars($transaksi_data['penulis']); ?></p>
                            <p>Harga Satuan: Rp<?php echo number_format($transaksi_data['harga'], 0, ',', '.'); ?></p>
                            <p>Jumlah: <?php echo $transaksi_data['jumlah']; ?></p>
                            <hr>
                            <h4>Detail Pembayaran:</h4>
                            <p>Metode Pembayaran: <?php echo htmlspecialchars($transaksi_data['metode_pembayaran']); ?></p>
                            <p><strong>Total Pembayaran: Rp<?php echo number_format($transaksi_data['total_harga'], 0, ',', '.'); ?></strong></p>
                        </div>
                        <div class="print-footer">
                            <p>Terima kasih telah berbelanja di Buka Baca Buku!</p>
                            <p>Ini adalah dokumen cetak otomatis, tidak memerlukan tanda tangan.</p>
                        </div>
                    </div>
                `;
                
                document.body.insertAdjacentHTML('beforeend', printContent);
                window.print();
                
                setTimeout(function() {
                    var printContainer = document.getElementById('printContainer');
                    if (printContainer) {
                        printContainer.remove();
                    }
                }, 1000);
            }
        </script>
        <?php endif; ?>

        <?php if ($peminjaman_data): ?>
        <script>
            function printPeminjamanTransaction(peminjamanId) {
                var printContent = `
                    <div class="print-container" id="printContainer">
                        <div class="print-header">
                            <h2>BUKA BACA BUKU</h2>
                            <h3>BUKTI PEMINJAMAN</h3>
                            <p>ID Peminjaman: #${peminjamanId}</p>
                            <p>Tanggal: ${new Date().toLocaleDateString('id-ID')}</p>
                        </div>
                        <div class="print-content">
                            <h4>Data Peminjam:</h4>
                            <p>Nama: <?php echo htmlspecialchars($peminjaman_data['nama']); ?></p>
                            <p>Email: <?php echo htmlspecialchars($peminjaman_data['email']); ?></p>
                            <p>Telepon: <?php echo htmlspecialchars($peminjaman_data['telepon']); ?></p>
                            <p>Alamat: <?php echo htmlspecialchars($peminjaman_data['alamat']); ?></p>
                            <hr>
                            <h4>Detail Buku:</h4>
                            <p>Judul: <?php echo htmlspecialchars($peminjaman_data['judul']); ?></p>
                            <p>Penulis: <?php echo htmlspecialchars($peminjaman_data['penulis']); ?></p>
                            <hr>
                            <h4>Tanggal Peminjaman:</h4>
                            <p>Tanggal Pinjam: <?php echo date('d-m-Y', strtotime($peminjaman_data['tanggal_pinjam'])); ?></p>
                            <p>Tanggal Kembali: <?php echo date('d-m-Y', strtotime($peminjaman_data['tanggal_kembali'])); ?></p>
                        </div>
                        <div class="print-footer">
                            <p>Terima kasih telah meminjam buku di Buka Baca Buku!</p>
                            <p>Harap kembalikan buku tepat pada waktunya.</p>
                            <p>Ini adalah dokumen cetak otomatis, tidak memerlukan tanda tangan.</p>
                        </div>
                    </div>
                `;
                
                document.body.insertAdjacentHTML('beforeend', printContent);
                window.print();
                
                setTimeout(function() {
                    var printContainer = document.getElementById('printContainer');
                    if (printContainer) {
                        printContainer.remove();
                    }
                }, 1000);
            }
        </script>
        <?php endif; ?>
    </body>
</html>