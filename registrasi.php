<?php
include 'connect.php';

$error_message = '';
$success_message = '';

if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $alamat = trim($_POST['alamat']);
    $status = $_POST['status'] ?? 'umum';

    // Validasi input
    if (empty($username)) {
        $error_message = "Username tidak boleh kosong!";
    } elseif (empty($email)) {
        $error_message = "Email tidak boleh kosong!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Format email tidak valid!";
    } elseif (empty($password)) {
        $error_message = "Password tidak boleh kosong!";
    } elseif (strlen($password) < 6) {
        $error_message = "Password minimal 6 karakter!";
    } elseif (empty($alamat)) {
        $error_message = "Alamat tidak boleh kosong!";
    } else {
        // Cek apakah username atau email sudah terdaftar
        $stmt_check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt_check->bind_param("ss", $username, $email);
        $stmt_check->execute();
        $result = $stmt_check->get_result();

        if ($result->num_rows > 0) {
            $error_message = "Username atau Email sudah terdaftar!";
        } else {
            // Hash password
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Proses foto
            $foto = null;
            if (!empty($_FILES['foto']['name'])) {
                $target_dir = "uploads/";
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }

                $file_name = time() . "_" . basename($_FILES["foto"]["name"]);
                $target_file = $target_dir . $file_name;
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                $allowed = ["jpg", "jpeg", "png", "gif"];
                if (in_array($imageFileType, $allowed)) {
                    if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
                        $foto = $file_name;
                    }
                } else {
                    $error_message = "Tipe file foto harus JPG, JPEG, PNG, atau GIF!";
                }
            }

            if (empty($error_message)) {
                // GUNAKAN PREPARED STATEMENT untuk INSERT
                $stmt = $conn->prepare("INSERT INTO users (username, email, password, alamat, status, foto) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssss", $username, $email, $password_hash, $alamat, $status, $foto);

                if ($stmt->execute()) {
                    $success_message = "Registrasi berhasil! Silakan login.";
                    // Reset form
                    $_POST = [];
                    echo "<script>setTimeout(function(){ window.location='login.php'; }, 2000);</script>";
                } else {
                    $error_message = "Error: " . $stmt->error;
                }
                $stmt->close();
            }
        }
        $stmt_check->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Akun - Perpustakaan Digital</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header Navigation */
        header {
            background-color: #2c3e50;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .logo {
            color: white;
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin-left: 30px;
            font-size: 14px;
            transition: color 0.3s;
        }

        nav a:hover {
            color: #3498db;
        }

        /* Main Content */
        main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }

        .register-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            padding: 50px 40px;
            max-width: 500px;
            width: 100%;
        }

        .register-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .company-logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            color: white;
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        }

        .register-header h1 {
            font-size: 24px;
            color: #2c3e50;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .register-header p {
            color: #7f8c8d;
            font-size: 14px;
        }

        /* Alert Messages */
        .alert {
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
            border-left: 4px solid;
        }

        .alert-error {
            background-color: #ffebee;
            color: #c62828;
            border-left-color: #c62828;
        }

        .alert-success {
            background-color: #e8f5e9;
            color: #2e7d32;
            border-left-color: #2e7d32;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #2c3e50;
            font-size: 13px;
            font-weight: 500;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="file"],
        select,
        textarea {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s, box-shadow 0.3s;
            font-family: inherit;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        input[type="file"]:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .form-row.full {
            grid-template-columns: 1fr;
        }

        /* Button Styles */
        .btn-register {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-top: 10px;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(52, 152, 219, 0.3);
        }

        .btn-register:active {
            transform: translateY(0);
        }

        /* Login Link */
        .login-link {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ecf0f1;
        }

        .login-link a {
            color: #3498db;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .login-link a:hover {
            color: #2980b9;
        }

        /* Footer */
        footer {
            background-color: #2c3e50;
            color: #bdc3c7;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
        }

        footer a {
            color: #3498db;
            text-decoration: none;
            margin-left: 20px;
            transition: color 0.3s;
        }

        footer a:hover {
            color: #5dade2;
        }

        /* Responsive */
        @media (max-width: 600px) {
            header, footer {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }

            nav a {
                margin-left: 15px;
            }

            .register-container {
                padding: 30px 20px;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .register-header h1 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">PERPUSDIK-TARBA</div>
        <nav>
            <a href="index.php">Home</a>
            <a href="#about">About</a>
            <a href="#contact">Contact</a>
        </nav>
    </header>

    <main>
        <div class="register-container">
            <div class="register-header">
                <div class="company-logo">📚</div>
                <h1>Registrasi Akun</h1>
                <p>Perpustakaan Digital</p>
            </div>

            <?php if (!empty($error_message)): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" required>
                            <option value="umum" <?php echo (isset($_POST['status']) && $_POST['status'] == 'umum') ? 'selected' : ''; ?>>Umum</option>
                            <option value="staf" <?php echo (isset($_POST['status']) && $_POST['status'] == 'staf') ? 'selected' : ''; ?>>Staf</option>
                        </select>
                    </div>
                </div>

                <div class="form-group form-row full">
                    <label for="alamat">Alamat</label>
                    <textarea id="alamat" name="alamat" required><?php echo isset($_POST['alamat']) ? htmlspecialchars($_POST['alamat']) : ''; ?></textarea>
                </div>

                <div class="form-group">
                    <label for="foto">Foto (Opsional)</label>
                    <input type="file" id="foto" name="foto" accept="image/*">
                </div>

                <button type="submit" name="register" class="btn-register">Register</button>

                <div class="login-link">
                    Sudah punya akun? <a href="login.php">Login!</a>
                </div>
            </form>
        </div>
    </main>

    <footer>
        <div>Copyright © Your Website 2023</div>
        <div>
            <a href="#">Privacy Policy</a>
            <a href="#">Terms & Conditions</a>
        </div>
    </footer>
</body>
</html>