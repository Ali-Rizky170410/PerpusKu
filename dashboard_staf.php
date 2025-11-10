<?php
// FILE: laporan_transaksi.php

// Koneksi Database
$host = "localhost";
$user = "root";
$pass = "";
$database = "perpus";
$koneksi = mysqli_connect($host, $user, $pass, $database);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Logika Pencarian
$search_query = "";
$sql_where = "";
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search_query = mysqli_real_escape_string($koneksi, trim($_GET['search']));
    $sql_where = " WHERE t.id LIKE '%$search_query%' OR c.nama LIKE '%$search_query%' OR b.judul LIKE '%$search_query%'";
}

// Logika Sorting
$sort_column = isset($_GET['sort']) ? $_GET['sort'] : 'tanggal_beli';
$sort_order = isset($_GET['order']) ? $_GET['order'] : 'DESC';

$allowed_sort_columns = ['transaksi_id', 'tanggal_beli', 'nama_customer', 'judul_buku', 'total_harga'];
if (!in_array($sort_column, $allowed_sort_columns)) {
    $sort_column = 'tanggal_beli';
}
$sort_order = strtoupper($sort_order) === 'ASC' ? 'ASC' : 'DESC';
$order_by_clause = "ORDER BY $sort_column $sort_order";

// Query Data Transaksi
$query_transaksi = "SELECT 
                        t.id AS transaksi_id, 
                        t.tanggal_beli, 
                        c.nama AS nama_customer, 
                        c.email AS email_customer, 
                        c.telepon AS telepon_customer,
                        c.alamat AS alamat_customer,
                        b.judul AS judul_buku, 
                        b.penulis AS penulis_buku,
                        b.gambar_url,
                        t.jumlah, 
                        t.total_harga, 
                        t.metode_pembayaran
                    FROM transaksi t
                    JOIN customer c ON t.customer_id = c.id
                    JOIN buku b ON t.buku_id = b.id
                    $sql_where
                    $order_by_clause";
$result_transaksi = mysqli_query($koneksi, $query_transaksi);

// Query Statistik
$query_stats = "SELECT COUNT(*) as total_transaksi, SUM(total_harga) as total_pendapatan FROM transaksi";
$result_stats = mysqli_query($koneksi, $query_stats);
$stats = mysqli_fetch_assoc($result_stats);

// Helper Function untuk Sorting Header
function sortableHeader($title, $column, $current_column, $current_order, $search_query) {
    $order = ($current_column == $column && $current_order == 'ASC') ? 'DESC' : 'ASC';
    $class = ($current_column == $column) ? 'active' : '';
    $icon = ($current_column == $column) ? ($current_order == 'ASC' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort';
    echo "<th class='sortable-header $class'>";
    echo "<a href='?sort=$column&order=$order&search=" . urlencode($search_query) . "'>$title <i class='fas $icon sort-icon'></i></a>";
    echo "</th>";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Laporan Transaksi - Perpusku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
    <style>
        @media print { 
            .no-print { display: none !important; } 
            .table { font-size: 12px; } 
        }
        .stats-card { 
            border-left: 4px solid; 
            transition: transform 0.2s; 
        }
        .stats-card:hover { 
            transform: translateY(-5px); 
        }
        .table-hover tbody tr:hover { 
            background-color: #f8f9fa; 
        }
        .sortable-header a { 
            color: inherit; 
            text-decoration: none; 
        }
        .sortable-header a:hover { 
            color: #fff; 
        }
        .sortable-header .sort-icon { 
            margin-left: 5px; 
            color: #adb5bd; 
        }
        .sortable-header.active .sort-icon { 
            color: #fff; 
        }
    </style>
</head>
<body class="sb-nav-fixed">
    <!-- NAVBAR -->
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark no-print">
        <a class="navbar-brand ps-3 d-flex align-items-center" href="dashboard_staf.php">
            <img src="logo perpus.ico" alt="Perpus Icon" width="60" height="60" class="me-2">
            <span style="font-size: 1.25rem; font-weight: 600;">Buka Baca Buku</span>
        </a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle"><i class="fas fa-bars"></i></button>
        
        <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0" method="GET" action="laporan_transaksi.php">
            <div class="input-group">
                <input class="form-control" type="text" name="search" placeholder="Cari ID, nama, atau judul..." value="<?php echo htmlspecialchars($search_query); ?>" />
                <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
            </div>
        </form>

        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown">
                    <i class="fas fa-user fa-fw"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="data_users.php">Users</a></li>
                    <li><a class="dropdown-item" href="#!">Activity Log</a></li>
                    <li><hr class="dropdown-divider" /></li>
                    <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                </ul>
            </li>
        </ul>
    </nav>

    <div id="layoutSidenav">
        <!-- SIDEBAR -->
        <div id="layoutSidenav_nav" class="no-print">
            <nav class="sb-sidenav accordion sb-sidenav-light" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading">Core</div>
                        <a class="nav-link" href="dashboard_staf.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Dashboard
                        </a>
                        
                        <div class="sb-sidenav-menu-heading">Manajemen</div>
                        <a class="nav-link" href="create.buku.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-plus-square"></i></div>
                            Tambah Buku
                        </a>
                        <a class="nav-link" href="dashboard_umum.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-book"></i></div>
                            Katalog Buku
                        </a>
                        <a class="nav-link active" href="laporan_transaksi.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-file-invoice"></i></div>
                            Laporan Transaksi
                        </a>
                        <a class="nav-link" href="edit.buku.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                            Edit Buku
                        </a>
                        <a class="nav-link" href="data_users.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                            Data Users
                        </a>
                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <div class="small">Logged in as:</div>
                    Staf Admin
                </div>
            </nav>
        </div>

        <!-- KONTEN UTAMA -->
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div>
                            <h1 class="mt-4"><i class="fas fa-file-invoice me-2"></i>Laporan Transaksi</h1>
                            <ol class="breadcrumb mb-4">
                                <li class="breadcrumb-item"><a href="dashboard_staf.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Laporan Transaksi</li>
                            </ol>
                        </div>
                        <div class="no-print">
                            <a href="export_csv.php?search=<?php echo urlencode($search_query); ?>" class="btn btn-info">
                                <i class="fas fa-file-csv me-2"></i>Export CSV
                            </a>
                            <button onclick="window.print()" class="btn btn-success">
                                <i class="fas fa-print me-2"></i>Cetak Laporan
                            </button>
                        </div>
                    </div>

                    <!-- Statistik -->
                    <div class="row mb-4">
                        <div class="col-lg-6 col-md-6">
                            <div class="card stats-card shadow-sm mb-4" style="border-left-color: #4e73df;">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Total Transaksi
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold">
                                                <?php echo number_format($stats['total_transaksi']); ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-shopping-cart fa-2x text-primary"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="card stats-card shadow-sm mb-4" style="border-left-color: #1cc88a;">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Total Pendapatan
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold">
                                                Rp<?php echo number_format($stats['total_pendapatan'], 0, ',', '.'); ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-dollar-sign fa-2x text-success"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Transaksi -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-list me-2"></i>Daftar Transaksi
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered" width="100%" cellspacing="0">
                                    <thead class="table-dark">
                                        <tr>
                                            <?php sortableHeader('ID', 'transaksi_id', $sort_column, $sort_order, $search_query); ?>
                                            <?php sortableHeader('Tanggal', 'tanggal_beli', $sort_column, $sort_order, $search_query); ?>
                                            <?php sortableHeader('Customer', 'nama_customer', $sort_column, $sort_order, $search_query); ?>
                                            <?php sortableHeader('Buku', 'judul_buku', $sort_column, $sort_order, $search_query); ?>
                                            <th>Jumlah</th>
                                            <?php sortableHeader('Total', 'total_harga', $sort_column, $sort_order, $search_query); ?>
                                            <th>Metode</th>
                                            <th class="no-print">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ($result_transaksi && mysqli_num_rows($result_transaksi) > 0) {
                                            while($transaksi = mysqli_fetch_assoc($result_transaksi)) {
                                        ?>
                                        <tr>
                                            <td><?php echo $transaksi['transaksi_id']; ?></td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($transaksi['tanggal_beli'])); ?></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($transaksi['nama_customer']); ?></strong><br>
                                                <small class="text-muted"><?php echo htmlspecialchars($transaksi['email_customer']); ?></small>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($transaksi['judul_buku']); ?></strong><br>
                                                <small class="text-muted">by <?php echo htmlspecialchars($transaksi['penulis_buku']); ?></small>
                                            </td>
                                            <td class="text-center"><?php echo $transaksi['jumlah']; ?></td>
                                            <td><strong>Rp<?php echo number_format($transaksi['total_harga'], 0, ',', '.'); ?></strong></td>
                                            <td><?php echo htmlspecialchars($transaksi['metode_pembayaran']); ?></td>
                                            <td class="no-print">
                                                <button type="button" class="btn btn-sm btn-info" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#modalDetail<?php echo $transaksi['transaksi_id']; ?>">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php
                                            }
                                        } else {
                                            $colspan = 8;
                                            if (!empty($search_query)) {
                                                echo "<tr><td colspan='$colspan' class='text-center'>Transaksi dengan kata kunci '<strong>".htmlspecialchars($search_query)."</strong>' tidak ditemukan.</td></tr>";
                                            } else {
                                                echo "<tr><td colspan='$colspan' class='text-center'>Belum ada transaksi</td></tr>";
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <!-- Modal Detail -->
            <?php
            if ($result_transaksi && mysqli_num_rows($result_transaksi) > 0) {
                mysqli_data_seek($result_transaksi, 0);
                while($transaksi = mysqli_fetch_assoc($result_transaksi)) {
            ?>
            <div class="modal fade" id="modalDetail<?php echo $transaksi['transaksi_id']; ?>" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">Detail Transaksi #<?php echo $transaksi['transaksi_id']; ?></h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-4 text-center">
                                    <img src="<?php echo htmlspecialchars($transaksi['gambar_url']); ?>" 
                                         class="img-fluid rounded shadow" alt="Cover Buku">
                                </div>
                                <div class="col-md-8">
                                    <h4><?php echo htmlspecialchars($transaksi['judul_buku']); ?></h4>
                                    <p class="text-muted">by <?php echo htmlspecialchars($transaksi['penulis_buku']); ?></p>
                                    <hr>
                                    
                                    <h6><strong>Data Customer:</strong></h6>
                                    <p>
                                        <i class="fas fa-user me-2"></i><?php echo htmlspecialchars($transaksi['nama_customer']); ?><br>
                                        <i class="fas fa-envelope me-2"></i><?php echo htmlspecialchars($transaksi['email_customer']); ?><br>
                                        <i class="fas fa-phone me-2"></i><?php echo htmlspecialchars($transaksi['telepon_customer']); ?><br>
                                        <i class="fas fa-map-marker-alt me-2"></i><?php echo htmlspecialchars($transaksi['alamat_customer']); ?>
                                    </p>
                                    <hr>
                                    
                                    <h6><strong>Detail Pembelian:</strong></h6>
                                    <table class="table table-sm">
                                        <tr>
                                            <td width="40%">Tanggal Pembelian</td>
                                            <td><?php echo date('d F Y, H:i', strtotime($transaksi['tanggal_beli'])); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Jumlah</td>
                                            <td><?php echo $transaksi['jumlah']; ?> buku</td>
                                        </tr>
                                        <tr>
                                            <td>Metode Pembayaran</td>
                                            <td><?php echo htmlspecialchars($transaksi['metode_pembayaran']); ?></td>
                                        </tr>
                                        <tr class="table-info">
                                            <td><strong>Total Pembayaran</strong></td>
                                            <td>
                                                <strong class="text-primary fs-5">
                                                    Rp<?php echo number_format($transaksi['total_harga'], 0, ',', '.'); ?>
                                                </strong>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php
                }
            }
            mysqli_close($koneksi);
            ?>

            <footer class="py-4 bg-light mt-auto no-print">
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/scripts.js"></script>
</body>
</html>