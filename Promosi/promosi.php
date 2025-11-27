<?php
require_once '../includes/config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'promosi') {
    header('Location: /projek-ifws/login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0" /><title>Dashboard Promosi</title>
    <link rel="stylesheet" href="/projek-ifws/assets/css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <div class="sidebar-header"><div class="logo-and-title"><img src="/projek-ifws/assets/picture/logo.png" alt="Logo Informatics" class="sidebar-logo" /><h2>Informatics<br /><span>Webinar Series</span></h2></div><div class="admin-profile"><small>Promosi</small></div></div>
            <nav class="sidebar-nav">
                <div class="nav-section"><p class="section-title">DASHBOARD</p><ul><li class="active"><a href="/projek-ifws/Promosi/promosi.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li></ul></div>
                <div class="nav-section"><p class="section-title">KELOLA DATA IFWS</p><ul><li><a href="/projek-ifws/Promosi/promosi_datawebinar.php"><i class="fas fa-calendar-alt"></i><span>Data Webinar</span></a></li></ul></div>
                <div class="nav-section"><p class="section-title">AKUN</p><ul><li><a href="/projek-ifws/logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li></ul></div>
            </nav>
        </aside>
        <main class="main-content">
            <h1>Selamat Datang, Tim Promosi!</h1>
            <p>Pilih menu di sebelah kiri untuk mengelola poster webinar.</p>
        </main>
    </div>
</body>
</html>