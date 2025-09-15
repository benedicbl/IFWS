<?php include '../../includes/auth_check.php'; ?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Bendahara IFWS</title>
    <link rel="stylesheet" href="/proyek-ifws/assets/css/homepage_style.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
    <div class="container">
        <header class="page-header">
            <div class="user-info">
                <?php 
                    echo '<p class="user-name">' . htmlspecialchars($_SESSION['nama']) . '</p>';
                    echo '<p class="user-email">' . htmlspecialchars($_SESSION['email']) . '</p>';
                ?>
            </div>
            <div class="header-icons">
                <a href="#" class="icon-btn user-icon"><i class="fa-solid fa-user"></i></a>
                <a href="/proyek-ifws/login.php" class="icon-btn logout-icon"><i class="fa-solid fa-right-from-bracket"></i></a>
            </div>
        </header>

        <main class="main-section">
            <div class="logo-area">
                <img src="/proyek-ifws/assets/images/Logo.jpg" alt="Logo IFWS" class="main-logo">
                <div class="logo-text">
                    <h2>Informatics</h2>
                    <h2>Webinar</h2>
                    <h2>Series</h2>
                </div>
            </div>

            <nav class="menu-navigation">
                <a href="upload_pembayaran.php" class="menu-card">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                    <span>Upload Bukti Pembayaran</span>
                </a>
            </nav>
        </main>
    </div>
</body>
</html>