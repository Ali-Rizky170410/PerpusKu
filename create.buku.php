<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Buku - Perpusku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
    <link href="styles.css" rel="stylesheet" /> 
    <style>
        .preview-image {
            max-width: 200px;
            max-height: 250px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            display: none;
        }
        .category-icon {
            font-size: 1.2rem;
            margin-right: 8px;
        }
    </style>
</head>
<body class="sb-nav-fixed">
    <!-- NAVBAR -->
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand ps-3 d-flex align-items-center" href="dashboard_staf.php">
            <img src="logo perpus.ico" alt="Perpus Icon" width="60" height="60" class="me-2">
            <span style="font-size: 1.25rem; font-weight: 600;">Buka Baca Buku</span>
        </a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle"><i class="fas fa-bars"></i></button>
        <ul class="navbar-nav ms-auto me-3">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown">
                    <i class="fas fa-user fa-fw"></i> Admin
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                </ul>
            </li>
        </ul>
    </nav>

    <div id="layoutSidenav">
        <!-- SIDEBAR -->
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-light" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading">Core</div>
                        <a class="nav-link" href="dashboard_staf.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Dashboard
                        </a>
                        
                        <div class="sb-sidenav-menu-heading">Manajemen</div>
                        <a class="nav-link active" href="create.buku.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-plus-square"></i></div>
                            Tambah Buku
                        </a>
                        <a class="nav-link" href="dashboard_umum.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-book"></i></div>
                            Katalog Buku
                        </a>
                        <a class="nav-link" href="laporan_transaksi.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-file-invoice"></i></div>
                            Laporan Transaksi
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
                    <h1 class="mt-4"><i class="fas fa-plus-circle me-2 text-success"></i>Tambah Buku Baru</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="dashboard_staf.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Tambah Buku</li>
                    </ol>

                    <?php
                    // Menampilkan notifikasi jika ada setelah submit form
                    if (isset($_GET['status'])) {
                        if ($_GET['status'] == 'sukses') {
                            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong>Berhasil!</strong> Buku baru telah ditambahkan ke katalog.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>';
                        } else if ($_GET['status'] == 'gagal') {
                            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    <strong>Gagal!</strong> Terjadi kesalahan saat menambahkan buku.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>';
                        }
                    }
                    ?>
                    
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card shadow">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-book-open me-2"></i>
                                        Formulir Penambahan Buku
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <!-- Form akan mengirim data ke proses.tambah.buku.php -->
                                    <form action="proses.tambah.buku.php" method="POST" id="formTambahBuku">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label for="judul" class="form-label">
                                                        <i class="fas fa-heading text-primary"></i> Judul Buku *
                                                    </label>
                                                    <input type="text" class="form-control" id="judul" name="judul" 
                                                           placeholder="Masukkan judul buku" required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="penulis" class="form-label">
                                                        <i class="fas fa-pen text-primary"></i> Penulis *
                                                    </label>
                                                    <input type="text" class="form-control" id="penulis" name="penulis" 
                                                           placeholder="Nama penulis" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="penerbit" class="form-label">
                                                        <i class="fas fa-building text-primary"></i> Penerbit
                                                    </label>
                                                    <input type="text" class="form-control" id="penerbit" name="penerbit" 
                                                           placeholder="Nama penerbit (opsional)">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="kategori" class="form-label">
                                                        <i class="fas fa-tags text-primary"></i> Kategori *
                                                    </label>
                                                    <select class="form-select" id="kategori" name="kategori" required>
                                                        <option value="">-- Pilih Kategori --</option>
                                                        <option value="Novel">
                                                            <span class="category-icon">📖</span> Novel
                                                        </option>
                                                        <option value="Komik">
                                                            <span class="category-icon">📚</span> Komik
                                                        </option>
                                                        <option value="Majalah">
                                                            <span class="category-icon">📰</span> Majalah
                                                        </option>
                                                        <option value="Edukasi">
                                                            <span class="category-icon">🎓</span> Edukasi
                                                        </option>
                                                    </select>
                                                    <div class="form-text">
                                                        Pilih kategori yang sesuai dengan jenis buku
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="harga" class="form-label">
                                                        <i class="fas fa-money-bill-wave text-primary"></i> Harga (Rp) *
                                                    </label>
                                                    <input type="number" class="form-control" id="harga" name="harga" 
                                                           placeholder="Contoh: 50000" min="0" required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="gambar_url" class="form-label">
                                                <i class="fas fa-image text-primary"></i> URL Gambar Sampul *
                                            </label>
                                            <input type="url" class="form-control" id="gambar_url" name="gambar_url" 
                                                   placeholder="https://example.com/gambar.jpg" 
                                                   onchange="previewImage(this.value)" required>
                                            <div class="form-text">
                                                Masukkan link URL gambar dari internet (format: https://...)
                                            </div>
                                        </div>

                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>
                                            <strong>Catatan:</strong> Field yang ditandai dengan (*) wajib diisi.
                                        </div>

                                        <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                                            <button type="submit" class="btn btn-success btn-lg">
                                                <i class="fas fa-save me-2"></i>Simpan Buku
                                            </button>
                                            <a href="dashboard_umum.php" class="btn btn-secondary btn-lg">
                                                <i class="fas fa-eye me-2"></i>Lihat Katalog
                                            </a>
                                            <button type="reset" class="btn btn-outline-danger btn-lg">
                                                <i class="fas fa-redo me-2"></i>Reset Form
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Preview Gambar -->
                        <div class="col-lg-4">
                            <div class="card shadow">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-image me-2"></i>Preview Gambar
                                    </h5>
                                </div>
                                <div class="card-body text-center">
                                    <img id="imagePreview" class="preview-image" alt="Preview Gambar">
                                    <p id="noPreview" class="text-muted mt-3">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Masukkan URL gambar untuk melihat preview
                                    </p>
                                </div>
                            </div>

                            <!-- Info Kategori -->
                            <div class="card shadow mt-3">
                                <div class="card-header bg-warning">
                                    <h6 class="mb-0">
                                        <i class="fas fa-lightbulb me-2"></i>Panduan Kategori
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <i class="fas fa-book text-primary me-2"></i>
                                            <strong>Novel:</strong> Cerita fiksi/non-fiksi
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-book-open text-success me-2"></i>
                                            <strong>Komik:</strong> Buku bergambar/manga
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-newspaper text-info me-2"></i>
                                            <strong>Majalah:</strong> Publikasi berkala
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-graduation-cap text-warning me-2"></i>
                                            <strong>Edukasi:</strong> Buku pembelajaran
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/scripts.js"></script>
    
    <script>
        // Preview gambar saat URL dimasukkan
        function previewImage(url) {
            const preview = document.getElementById('imagePreview');
            const noPreview = document.getElementById('noPreview');
            
            if (url && url.startsWith('http')) {
                preview.src = url;
                preview.style.display = 'block';
                noPreview.style.display = 'none';
                
                // Handle error jika gambar gagal load
                preview.onerror = function() {
                    preview.style.display = 'none';
                    noPreview.style.display = 'block';
                    noPreview.innerHTML = '<i class="fas fa-exclamation-triangle text-warning me-2"></i>Gagal memuat gambar. Periksa URL.';
                };
            } else {
                preview.style.display = 'none';
                noPreview.style.display = 'block';
                noPreview.innerHTML = '<i class="fas fa-info-circle me-2"></i>Masukkan URL gambar untuk melihat preview';
            }
        }

        // Format input harga dengan pemisah ribuan
        document.getElementById('harga').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            e.target.value = value;
        });

        // Validasi form sebelum submit
        document.getElementById('formTambahBuku').addEventListener('submit', function(e) {
            const kategori = document.getElementById('kategori').value;
            const harga = document.getElementById('harga').value;
            
            if (!kategori) {
                e.preventDefault();
                alert('Kategori harus dipilih!');
                document.getElementById('kategori').focus();
                return false;
            }
            
            if (harga <= 0) {
                e.preventDefault();
                alert('Harga harus lebih dari 0!');
                document.getElementById('harga').focus();
                return false;
            }
            
            return true;
        });
    </script>
</body>
</html>