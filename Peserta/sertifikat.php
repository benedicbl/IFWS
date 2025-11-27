<?php
require_once '../includes/config.php';
if (!isset($_SESSION['peserta_id'])) { header('Location: /projek-ifws/login.php'); exit(); }
$id_peserta = $_SESSION['peserta_id'];
$nama_peserta = htmlspecialchars($_SESSION['peserta_nama']);
$email_peserta = htmlspecialchars($_SESSION['peserta_email']);

// Ambil sertifikat yang tersedia untuk peserta ini
$query_sertif = "SELECT w.topik, k.path_sertifikat
                 FROM kehadiran k
                 JOIN webinars w ON k.id_webinar = w.id
                 WHERE k.id_peserta = ? AND k.status_kehadiran = 'hadir' AND k.path_sertifikat IS NOT NULL";
$stmt = mysqli_prepare($koneksi, $query_sertif);
mysqli_stmt_bind_param($stmt, "i", $id_peserta);
mysqli_stmt_execute($stmt);
$result_sertif = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0" /><title>Sertifikat</title>
    <link rel="stylesheet" href="/projek-ifws/assets/css/style.css" /><link rel="stylesheet" href="/projek-ifws/assets/css/Peserta/peserta.css" /><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <div class="sidebar-header"><div class="logo-and-title"><img src="/projek-ifws/assets/picture/logo.png" alt="Logo Informatics" class="sidebar-logo"/><h2>Informatics<br /><span>Webinar Series</span></h2></div><div class="peserta-profile"><img src="/projek-ifws/assets/picture/default_profile.png" alt="Profile" class="profile-pic" /><span class="profile-name"><?= $nama_peserta ?></span><span class="profile-email"><?= $email_peserta ?></span></div></div>
            <nav class="sidebar-nav">
                <div class="nav-section"><p class="section-title">DASHBOARD</p><ul><li><a href="/projek-ifws/Peserta/dashboard.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li></ul></div>
                <div class="nav-section"><p class="section-title">IFWS</p><ul><li><a href="/projek-ifws/Peserta/list_webinar.php"><i class="fas fa-calendar-alt"></i><span>List Webinar</span></a></li></ul></div>
                <div class="nav-section"><p class="section-title">RIWAYAT</p><ul><li><a href="/projek-ifws/Peserta/riwayat_ifws.php"><i class="fas fa-history"></i><span>Riwayat IFWS</span></a></li><li class="active"><a href="/projek-ifws/Peserta/sertifikat.php"><i class="fas fa-award"></i><span>Sertifikat</span></a></li></ul></div>
                <div class="nav-section"><p class="section-title">PESERTA TUGAS AKHIR</p><ul><li><a href="/projek-ifws/Peserta/progress.php"><i class="fas fa-chart-line"></i><span>Progress</span></a></li></ul></div>
                <div class="nav-section"><p class="section-title">AKUN</p><ul><li><a href="/projek-ifws/logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li></ul></div>
            </nav>
        </aside>
        <main class="main-content">
            <h1>Sertifikat Anda</h1>
            <div class="content-card">
                <table>
                    <thead><tr><th>Topik Webinar</th><th>Aksi</th></tr></thead>
                    <tbody>
                        <?php if ($result_sertif && mysqli_num_rows($result_sertif) > 0): while ($row = mysqli_fetch_assoc($result_sertif)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['topik']) ?></td>
                            <td>
                                <a href="/projek-ifws/<?= htmlspecialchars($row['path_sertifikat']) ?>" target="_blank" class="btn btn-success"><i class="fas fa-download"></i> Unduh</a>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr><td colspan="2" style="text-align: center;">Anda belum memiliki sertifikat.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>