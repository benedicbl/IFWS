<?php
require_once '../includes/config.php';
// Proteksi session
if (!isset($_SESSION['peserta_id'])) {
    header('Location: /projek-ifws/login.php');
    exit();
}
// Ambil data dari session
$nama_peserta = htmlspecialchars($_SESSION['peserta_nama']);
$email_peserta = htmlspecialchars($_SESSION['peserta_email']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0" /><title>Dashboard Peserta</title>
    <link rel="stylesheet" href="/projek-ifws/assets/css/style.css" />
    <link rel="stylesheet" href="/projek-ifws/assets/css/Peserta/peserta.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo-and-title">
                    <img src="/projek-ifws/assets/picture/logo.png" alt="Logo Informatics" class="sidebar-logo"/>
                    <h2>Informatics<br /><span>Webinar Series</span></h2>
                </div>
                <div class="peserta-profile">
                    <img src="/projek-ifws/assets/picture/default_profile.png" alt="Profile" class="profile-pic" />
                    <span class="profile-name"><?= $nama_peserta ?></span>
                    <span class="profile-email"><?= $email_peserta ?></span>
                </div>
            </div>
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <p class="section-title">DASHBOARD</p>
                    <ul><li class="active"><a href="/projek-ifws/Peserta/dashboard.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li></ul>
                </div>
                <div class="nav-section">
                    <p class="section-title">IFWS</p>
                    <ul><li><a href="/projek-ifws/Peserta/list_webinar.php"><i class="fas fa-calendar-alt"></i><span>List Webinar</span></a></li></ul>
                </div>
                <div class="nav-section">
                    <p class="section-title">RIWAYAT</p>
                    <ul><li><a href="/projek-ifws/Peserta/riwayat_ifws.php"><i class="fas fa-history"></i><span>Riwayat IFWS</span></a></li><li><a href="/projek-ifws/Peserta/sertifikat.php"><i class="fas fa-award"></i><span>Sertifikat</span></a></li></ul>
                </div>
                 <div class="nav-section">
                    <p class="section-title">PESERTA TUGAS AKHIR</p>
                    <ul><li><a href="/projek-ifws/Peserta/progress.php"><i class="fas fa-chart-line"></i><span>Progress</span></a></li></ul>
                </div>
                <div class="nav-section">
                    <p class="section-title">AKUN</p>
                    <ul><li><a href="/projek-ifws/logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li></ul>
                </div>
            </nav>
        </aside>
        <main class="main-content">
            <h1>Selamat Datang, <?= $nama_peserta ?>!</h1>
            <p>Pilih menu di sebelah kiri untuk melihat daftar webinar, riwayat, dan progress Anda.</p>
        </main>
    </div>
</body>
</html>