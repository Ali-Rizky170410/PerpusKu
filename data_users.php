<?php
session_start();
include 'connect.php';

// Cek akses (hanya staf yang bisa akses halaman ini)
if (!isset($_SESSION['username']) || $_SESSION['status'] != 'staf') {
    header("Location: data_users.php");
    exit;
}

// PROSES TAMBAH USER
if (isset($_POST['tambah'])) {
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $status   = $_POST['status'];

    $foto = null;
    if (!empty($_FILES['foto']['name'])) {
        $target_dir  = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_name   = time() . "_" . basename($_FILES["foto"]["name"]);
        $target_file = $target_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $allowed = ["jpg","jpeg","png","gif"];
        if (in_array($imageFileType, $allowed)) {
            if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
                $foto = $file_name;
            }
        }
    }

    $stmt = $conn->prepare("INSERT INTO users (username, password, status, foto) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $password, $status, $foto);

    if ($stmt->execute()) {
        echo "<script>alert('User berhasil ditambahkan!'); window.location='data_users.php';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

// PROSES EDIT USER
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $username = trim($_POST['username']);
    $status   = $_POST['status'];
    
    // Ambil foto lama
    $query_old = mysqli_query($conn, "SELECT foto FROM users WHERE id = '$id'");
    $old_data = mysqli_fetch_assoc($query_old);
    $foto = $old_data['foto'];

    // Upload foto baru jika ada
    if (!empty($_FILES['foto']['name'])) {
        $target_dir  = "uploads/";
        $file_name   = time() . "_" . basename($_FILES["foto"]["name"]);
        $target_file = $target_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $allowed = ["jpg","jpeg","png","gif"];
        if (in_array($imageFileType, $allowed)) {
            if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
                // Hapus foto lama jika ada
                if ($foto && file_exists("uploads/" . $foto)) {
                    unlink("uploads/" . $foto);
                }
                $foto = $file_name;
            }
        }
    }

    // Update dengan/tanpa password baru
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET username=?, password=?, status=?, foto=? WHERE id=?");
        $stmt->bind_param("ssssi", $username, $password, $status, $foto, $id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET username=?, status=?, foto=? WHERE id=?");
        $stmt->bind_param("sssi", $username, $status, $foto, $id);
    }

    if ($stmt->execute()) {
        echo "<script>alert('User berhasil diupdate!'); window.location='data_users.php';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

// PROSES HAPUS USER
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    
    // Ambil nama foto untuk dihapus
    $query = mysqli_query($conn, "SELECT foto FROM users WHERE id = '$id'");
    $data = mysqli_fetch_assoc($query);
    
    if ($data['foto'] && file_exists("uploads/" . $data['foto'])) {
        unlink("uploads/" . $data['foto']);
    }
    
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo "<script>alert('User berhasil dihapus!'); window.location='data_users.php';</script>";
    }
    $stmt->close();
}

// Ambil semua data users
$result = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Data Users - Perpustakaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="css/styles.css" rel="stylesheet" />
</head>
<body class="sb-nav-fixed">
    <!-- NAVBAR -->
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand ps-3" href="dashboard_staf.php">Perpustakaan</a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle"><i class="fas fa-bars"></i></button>
        <ul class="navbar-nav ms-auto me-3">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown">
                    <i class="fas fa-user fa-fw"></i> <?= $_SESSION['username']; ?>
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
                        <a class="nav-link" href="dashboard_staf.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Dashboard
                        </a>
                        <a class="nav-link active" href="data_users.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                            Data Users
                        </a>
                        <a class="nav-link" href="data_buku.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-book"></i></div>
                            Data Buku
                        </a>
                    </div>
                </div>
            </nav>
        </div>

        <!-- CONTENT -->
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Data Users</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="dashboard_staf.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Data Users</li>
                    </ol>

                    <!-- TOMBOL TAMBAH -->
                    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalTambah">
                        <i class="fas fa-plus"></i> Tambah User
                    </button>

                    <!-- TABEL DATA -->
                    <div class="card shadow mb-4">
                        <div class="card-header bg-primary text-white">
                            <i class="fas fa-table me-1"></i> Daftar Users
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">No</th>
                                        <th>Foto</th>
                                        <th>Username</th>
                                        <th>Status</th>
                                        <th width="150">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $no = 1;
                                    while ($row = mysqli_fetch_assoc($result)) { 
                                    ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td>
                                            <?php if ($row['foto']) { ?>
                                                <img src="uploads/<?= $row['foto']; ?>" width="50" height="50" class="rounded">
                                            <?php } else { ?>
                                                <i class="fas fa-user-circle fa-3x text-muted"></i>
                                            <?php } ?>
                                        </td>
                                        <td><?= htmlspecialchars($row['username']); ?></td>
                                        <td>
                                            <span class="badge bg-<?= $row['status'] == 'staf' ? 'success' : 'info'; ?>">
                                                <?= ucfirst($row['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['id']; ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <a href="?hapus=<?= $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus user ini?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>

                                    <!-- MODAL EDIT -->
                                    <div class="modal fade" id="modalEdit<?= $row['id']; ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header bg-warning text-white">
                                                    <h5 class="modal-title">Edit User</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form method="POST" enctype="multipart/form-data">
                                                    <div class="modal-body">
                                                        <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                                        
                                                        <div class="mb-3">
                                                            <label>Username</label>
                                                            <input type="text" name="username" class="form-control" value="<?= $row['username']; ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label>Password Baru (kosongkan jika tidak diubah)</label>
                                                            <input type="password" name="password" class="form-control">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label>Status</label>
                                                            <select name="status" class="form-control" required>
                                                                <option value="umum" <?= $row['status'] == 'umum' ? 'selected' : ''; ?>>Umum</option>
                                                                <option value="staf" <?= $row['status'] == 'staf' ? 'selected' : ''; ?>>Staf</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label>Foto Saat Ini</label><br>
                                                            <?php if ($row['foto']) { ?>
                                                                <img src="uploads/<?= $row['foto']; ?>" width="100" class="mb-2">
                                                            <?php } ?>
                                                            <input type="file" name="foto" class="form-control" accept="image/*">
                                                            <small class="text-muted">Kosongkan jika tidak ingin mengubah foto</small>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" name="edit" class="btn btn-warning">Update</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>

            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="text-muted text-center">Copyright &copy; Perpustakaan 2025</div>
                </div>
            </footer>
        </div>
    </div>

    <!-- MODAL TAMBAH -->
    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Tambah User Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Status</label>
                            <select name="status" class="form-control" required>
                                <option value="umum">Umum</option>
                                <option value="staf">Staf</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Foto (opsional)</label>
                            <input type="file" name="foto" class="form-control" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="tambah" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/scripts.js"></script>
</body>
</html>