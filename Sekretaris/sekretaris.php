<?php
require_once '../includes/config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'sekretaris') {
    header('Location: /projek-ifws/login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0" /><title>Dashboard Sekretaris</title>
    <link rel="stylesheet" href="/projek-ifws/assets/css/style.css" />
    <link rel="stylesheet" href="/projek-ifws/assets/css/Sekretaris/sekretaris.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo-and-title"><img src="/projek-ifws/assets/picture/logo.png" alt="Logo Informatics" class="sidebar-logo"/><h2>Informatics<br /><span>Webinar Series</span></h2></div>
                <div class="admin-profile"><small>Sekretaris</small></div>
            </div>
            <nav class="sidebar-nav">
                <div class="nav-section"><p class="section-title">DASHBOARD</p><ul><li class="active"><a href="/projek-ifws/Sekretaris/sekretaris.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li></ul></div>
                <div class="nav-section"><p class="section-title">KELOLA DATA IFWS</p><ul><li><a href="/projek-ifws/Sekretaris/sekretaris_datawebinar.php"><i class="fas fa-calendar-alt"></i><span>Data Webinar</span></a></li><li><a href="/projek-ifws/Sekretaris/sekretaris_pesertaTA.php"><i class="fas fa-user-friends"></i><span>Peserta Tugas Akhir</span></a></li><li><a href="/projek-ifws/Sekretaris/sekretaris_kelolasertif.php"><i class="fas fa-award"></i><span>Kelola Sertifikat</span></a></li></ul></div>
                <div class="nav-section"><p class="section-title">AKUN</p><ul><li><a href="/projek-ifws/logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li></ul></div>
            </nav>
        </aside>
        <main class="main-content">
            <h1>Selamat Datang di Dashboard Sekretaris!</h1>
            <p>Pilih menu di sebelah kiri untuk mengelola data.</p>
        </main>
    </div>
</body>
</html>