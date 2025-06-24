<?php include 'includes/auth_check.php'; ?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page - IFWS</title>
    <link rel="stylesheet" href="assets/css/homepage_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>

    <div class="container">
        <header class="page-header">
            <div class="user-info">
                <p class="user-name">BENEDICTUS BENNY LUSWANA</p>
                <p class="user-email">81XXXXXXXX@student.unpar.ac.id</p>
            </div>
            <div class="header-icons">
                <a href="#" class="icon-btn user-icon"><i class="fa-solid fa-user"></i></a>
                <a href="#" class="icon-btn logout-icon"><i class="fa-solid fa-right-from-bracket"></i></a>
            </div>
        </header>

        <main class="main-section">
            <div class="logo-area">
                <img src="assets/images/Logo.jpg" alt="Informatics Webinar Series Logo" class="main-logo">
                <div class="logo-text">
                    <h2>Informatics</h2>
                    <h2>Webinar</h2>
                    <h2>Series</h2>
                </div>
            </div>

            <nav class="menu-navigation">
                <a href="pages/list_IFWS.php" class="menu-card">
                    <i class="fa-solid fa-list-ul"></i>
                    <span>List IFWS</span>
                </a>
                <a href="pages/riwayat.php" class="menu-card">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    <span>Riwayat IFWS</span>
                </a>
                <a href="pages/list_narasumber.php" class="menu-card">
                    <i class="fa-solid fa-chalkboard-user"></i>
                    <span>List Narasumber</span>
                </a>
                <a href="pages/list_anggota.php" class="menu-card">
                    <i class="fa-solid fa-users"></i>
                    <span>Anggota IFWS</span>
                </a>
            </nav>
        </main>
    </div>

</body>
</html>