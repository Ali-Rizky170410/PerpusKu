<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Perpustakaan</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center center;
            background-attachment: fixed;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            position: relative;
            flex-direction: column;
        }
        body::before {
            content: "";
            position: absolute; 
            top: 0; 
            left: 0;
            width: 100%; 
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: -1;
        }
        
        /* Navbar Styling */
        .navbar-custom {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
            padding: 1rem ;
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }
        
        .navbar-custom .container-fluid {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .navbar-brand-custom {
            display: flex;
            align-items: center;
            gap: 10px;
            color: white;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.3rem;
        }
        
        .navbar-brand-custom img {
            width: 40px;
            height: 40px;
        }
        
        .navbar-brand-custom:hover {
            color: #3498db;
            transition: all 0.3s ease;
        }
        
        .navbar-menu {
            display: flex;
            gap: 30px;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        
        .navbar-menu a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .navbar-menu a:hover {
            color: #3498db;
        }
        
        body {
            padding-top: 80px;
        }
        
        .login-card {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            padding: 20px;
            max-width: 400px;
            width: 90%;
            text-align: center;
            z-index: 1;
            margin-top: 40px;
        }
        .login-card h2 { color: #2c3e50; margin-bottom: 25px; font-weight: 700; }
        .login-card .logo-img { width: 120px; margin-bottom: 20px; }
        .form-control:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 0.25rem rgba(52,152,219,0.25);
        }
        .btn-primary { background-color: #3498db; border-color: #3498db; padding: 10px 20px; font-weight: 600; }
        .btn-primary:hover { background-color: #2980b9; border-color: #2980b9; }
        
        @media (max-width: 768px) {
            .navbar-menu {
                gap: 15px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar-custom">
    <div class="container-fluid px-4">
        <a href="index.php" class="navbar-brand-custom">
            <span>PUSPENDIK-TARUNA</span>
        </a>
        <ul class="navbar-menu">
            <li><a href="index.php">Beranda</a></li>
            <li><a href="tentang.php">Tentang</a></li>
            <li><a href="kontak.php">Kontak</a></li>
        </ul>
    </div>
</nav>

<!-- Login Card -->
<div class="login-card">
    <img src="logo perpus.ico" alt="Logo Perpustakaan" class="logo-img">
    <h2>Masuk Perpustakaan</h2>
    <p class="text-muted mb-4">Silakan login dengan ID Anggota Anda.</p>

    <form action="prosesLogin.php" method="POST">
        <div class="mb-3">
            <input type="text" name="username" class="form-control" placeholder="ID Anggota / Username" required>
        </div>
        <div class="mb-4">
            <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>
        <div class="d-grid mb-3">
            <button type="submit" name="login" class="btn btn-primary">Login</button>
        </div>
        <p class="mt-3">Belum punya akun? <a href="registrasi.php">Daftar di sini</a></p>
    </form>
</div>

</body>
</html>