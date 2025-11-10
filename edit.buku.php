<?php
// Koneksi database
$host = "localhost";
$user = "root";
$pass = "";
$database = "perpus";
$koneksi = mysqli_connect($host, $user, $pass, $database);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Cek apakah sedang mode edit
$mode_edit = isset($_GET['edit']) && isset($_GET['id']);
$buku_edit = null;

if ($mode_edit) {
    $id_edit = intval($_GET['id']);
    $query_edit = "SELECT * FROM buku WHERE id = $id_edit";
    $result_edit = mysqli_query($koneksi, $query_edit);
    
    if ($result_edit && mysqli_num_rows($result_edit) > 0) {
        $buku_edit = mysqli_fetch_assoc($result_edit);
    } else {
        header("Location: dashboard_umum.php");
        exit();
    }
}

// Proses update buku
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_buku'])) {
    $id = intval($_POST['id']);
    $judul = mysqli_real_escape_string($koneksi, trim($_POST['judul']));
    $penulis = mysqli_real_escape_string($koneksi, trim($_POST['penulis']));
    $penerbit = mysqli_real_escape_string($koneksi, trim($_POST['penerbit']));
    $harga = mysqli_real_escape_string($koneksi, trim($_POST['harga']));
    $gambar_url = mysqli_real_escape_string($koneksi, trim($_POST['gambar_url']));
    
    $update_query = "UPDATE buku SET 
                     judul = '$judul',
                     penulis = '$penulis',
                     penerbit = '$penerbit',
                     harga = '$harga',
                     gambar_url = '$gambar_url'
                     WHERE id = $id";
    
    if (mysqli_query($koneksi, $update_query)) {
        header("Location: dashboard_umum.php?status=updated");
        exit();
    } else {
        $error_message = "Error: " . mysqli_error($koneksi);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title><?php echo $mode_edit ? 'Edit Buku' : 'Perpusku'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
    <style>
        .card-product {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border-radius: 0.75rem;
        }
        .card-product:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }
        .card-img-top-container {
            background-color: #f8f9fa;
            width: 123px;
            height: 158px;
            margin: 1rem auto 0;
            overflow: hidden;
            border-radius: 0.5rem;
        }
        .card-img-top-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .card-title-clamp {
            height: 48px;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-box-orient: vertical;
        }
        .btn-action {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            .card-product {
                break-inside: avoid;
            }
        }
        .modal-edit-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1050;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .modal-edit-content {
            background: white;
            border-radius: 10px;
            padding: 30px;
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
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
        <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
            <div class="input-group">
                <input class="form-control" type="text" placeholder="Search for..." aria-label="Search" />
                <button class="btn btn-primary" type="button"><i class="fas fa-search"></i></button>
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
                    <li><a class="dropdown-item" href="#!">Logout</a></li>
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
                        <a class="nav-link active" href="dashboard_umum.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-book"></i></div>
                            Katalog Buku
                        </a>
                    </div>
                </div>
            </nav>
        </div>

        <!-- KONTEN UTAMA -->
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <?php if ($mode_edit): ?>
                        <!-- MODE EDIT -->
                        <div class="modal-edit-overlay">
                            <div class="modal-edit-content">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h3><i class="fas fa-edit text-warning"></i> Edit Buku</h3>
                                    <a href="dashboard_umum.php" class="btn btn-sm btn-secondary">
                                        <i class="fas fa-times"></i> Tutup
                                    </a>
                                </div>

                                <?php if (isset($error_message)): ?>
                                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                                <?php endif; ?>

                                <form method="POST" action="">
                                    <input type="hidden" name="id" value="<?php echo $buku_edit['id']; ?>">
                                    <input type="hidden" name="update_buku" value="1">

                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <img src="<?php echo htmlspecialchars($buku_edit['gambar_url']); ?>" 
                                                 alt="Cover" class="img-fluid rounded shadow">
                                        </div>
                                        <div class="col-md-8">
                                            <div class="mb-3">
                                                <label class="form-label">Judul Buku *</label>
                                                <input type="text" class="form-control" name="judul" 
                                                       value="<?php echo htmlspecialchars($buku_edit['judul']); ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Penulis *</label>
                                                <input type="text" class="form-control" name="penulis" 
                                                       value="<?php echo htmlspecialchars($buku_edit['penulis']); ?>" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Penerbit</label>
                                        <input type="text" class="form-control" name="penerbit" 
                                               value="<?php echo htmlspecialchars($buku_edit['penerbit']); ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Harga (Rp) *</label>
                                        <input type="number" class="form-control" name="harga" 
                                               value="<?php echo htmlspecialchars($buku_edit['harga']); ?>" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">URL Gambar *</label>
                                        <input type="text" class="form-control" name="gambar_url" 
                                               value="<?php echo htmlspecialchars($buku_edit['gambar_url']); ?>" required>
                                    </div>

                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-warning">
                                            <i class="fas fa-save"></i> Update Buku
                                        </button>
                                        <a href="dashboard_umum.php" class="btn btn-secondary">
                                            <i class="fas fa-times"></i> Batal
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- MODE TAMPILAN BIASA -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div>
                            <h1>Buka Baca Buku</h1>
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="dashboard_staf.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Katalog Buku</li>
                            </ol>
                        </div>
                        <button onclick="window.print()" class="btn btn-success no-print">
                            <i class="fas fa-print me-2"></i>Cetak Katalog
                        </button>
                    </div>

                    <?php
                    if (isset($_GET['status'])) {
                        if ($_GET['status'] == 'deleted') {
                            echo '<div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                                    <strong>Berhasil!</strong> Buku telah dihapus.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                  </div>';
                        } elseif ($_GET['status'] == 'updated') {
                            echo '<div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                                    <strong>Berhasil!</strong> Data buku telah diperbarui.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                  </div>';
                        }
                    }
                    ?>

                    <div class="row mt-4">
                        <?php
                        $query = "SELECT * FROM buku ORDER BY id DESC";
                        $result = mysqli_query($koneksi, $query);

                        if ($result && mysqli_num_rows($result) > 0) {
                            while($buku = mysqli_fetch_assoc($result)) {
                        ?>
                        <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                            <div class="card card-product h-100 shadow-sm">
                                <div class="card-img-top-container">
                                    <img src="<?php echo htmlspecialchars($buku['gambar_url']); ?>" 
                                         alt="Cover <?php echo htmlspecialchars($buku['judul']); ?>">
                                </div>
                                <div class="card-body d-flex flex-column text-center pt-2">
                                    <div class="text-muted small"><?php echo htmlspecialchars($buku['penulis']); ?></div>
                                    <h5 class="card-title card-title-clamp fw-semibold mt-1 fs-6">
                                        <a href="#" class="text-decoration-none text-dark">
                                            <?php echo htmlspecialchars($buku['judul']); ?>
                                        </a>
                                    </h5>
                                    <p class="card-text fw-bold fs-5 mt-auto text-primary">
                                        Rp<?php echo number_format($buku['harga'], 0, ',', '.'); ?>
                                    </p>
                                    
                                    <div class="d-flex gap-2 justify-content-center mt-2 no-print">
                                        <a href="?edit=true&id=<?php echo $buku['id']; ?>" class="btn btn-warning btn-action">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="hapus.buku.php?id=<?php echo $buku['id']; ?>" 
                                           class="btn btn-danger btn-action" 
                                           onclick="return confirm('Yakin ingin menghapus: <?php echo htmlspecialchars($buku['judul']); ?>?')">
                                            <i class="fas fa-trash"></i> Hapus
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                            }
                        } else {
                            echo '<div class="col-12"><div class="alert alert-warning">Belum ada buku yang ditambahkan.</div></div>';
                        }
                        mysqli_close($koneksi);
                        ?>
                    </div>
                </div>
            </main>

            <footer class="py-4 bg-light mt-auto no-print">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; Perpustakaan 2025</div>
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