<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perpustakaan Digital</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
        }

        /* Header Navigation */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem 3rem;
            background-color: #1a1f26;
            color: white;
        }

        .logo {
            font-size: 1.3rem;
            font-weight: 600;
        }

        nav a {
            color: #a0a8b3;
            text-decoration: none;
            margin-left: 2rem;
            transition: color 0.3s;
        }

        nav a:hover {
            color: white;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #1a1f26 0%, #2d3139 100%);
            color: white;
            padding: 5rem 2rem;
            text-align: center;
        }

        .hero h1 {
            font-size: 3rem;
            margin-bottom: 1.5rem;
            font-weight: 700;
        }

        .hero p {
            font-size: 1rem;
            color: #b0b8c1;
            max-width: 800px;
            margin: 0 auto 2rem;
            line-height: 1.8;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 0.75rem 2rem;
            border-radius: 0.5rem;
            text-decoration: none;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
        }

        .btn-primary {
            background-color: #1e88e5;
            color: white;
        }

        .btn-primary:hover {
            background-color: #1565c0;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background-color: transparent;
            color: white;
            border: 2px solid white;
        }

        .btn-secondary:hover {
            background-color: white;
            color: #1a1f26;
            transform: translateY(-2px);
        }

        /* Features Section */
        .features {
            padding: 4rem 2rem;
            background-color: #f8f9fa;
        }

        .features-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 3rem;
        }

        .feature-card {
            text-align: center;
            padding: 2rem;
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            background-color: #1e88e5;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2rem;
        }

        .feature-card h3 {
            font-size: 1.3rem;
            margin-bottom: 1rem;
            color: #1a1f26;
        }

        .feature-card p {
            color: #5a5f66;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .feature-link {
            color: #1e88e5;
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            transition: all 0.3s;
        }

        .feature-link:hover {
            gap: 0.5rem;
        }

        .feature-link::after {
            content: " →";
            margin-left: 0.25rem;
        }

        /* Footer */
        footer {
            background-color: #2d3139;
            color: #a0a8b3;
            text-align: center;
            padding: 2rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            header {
                flex-direction: column;
                gap: 1.5rem;
                padding: 1rem;
            }

            nav a {
                margin-left: 1rem;
            }

            .hero h1 {
                font-size: 2rem;
            }

            .hero-buttons {
                flex-direction: column;
                align-items: center;
            }

            .btn {
                width: 100%;
                max-width: 300px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="logo">Perpustakaan Digital</div>
        <nav>
            <a href="#home">Home</a>
            <a href="#about">About</a>
            <a href="#contact">Contact</a>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <h1>Selamat Datang di<br>Aplikasi Perpustakaan Digital</h1>
        <p>Quickly design and customize responsive mobile-first sites with Bootstrap, the world's most popular front-end open source toolkit! Lorem ipsum dolor sit amet, consectetur adipisicing elit. Vitae, et laborum beatae dignissimos enim quas labore tempore obcaecati quaerat</p>
        <div class="hero-buttons">
            <a href="login.php" class="btn btn-primary">Login</a>
            <a href="registrasi.php" class="btn btn-secondary">SignUp</a>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="features-container">
            <!-- Feature 1 -->
            <div class="feature-card">
                <div class="feature-icon">📚</div>
                <h3>Cara Peminjaman</h3>
                <p>Paragraph of text beneath the heading to explain the heading. We'll add onto it with another sentence and probably just keep going until we run out of words.</p>
                <a href="#" class="feature-link">Call to action</a>
            </div>

            <!-- Feature 2 -->
            <div class="feature-card">
                <div class="feature-icon">🏢</div>
                <h3>Peraturan Peminjaman</h3>
                <p>Paragraph of text beneath the heading to explain the heading. We'll add onto it with another sentence and probably just keep going until we run out of words.</p>
                <a href="#" class="feature-link">Call to action</a>
            </div>

            <!-- Feature 3 -->
            <div class="feature-card">
                <div class="feature-icon">👥</div>
                <h3>FAQ - Pertanyaan</h3>
                <p>Paragraph of text beneath the heading to explain the heading. We'll add onto it with another sentence and probably just keep going until we run out of words.</p>
                <a href="#" class="feature-link">Call to action</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <p>Copyright © Your Website 2023</p>
    </footer>
</body>
</html>