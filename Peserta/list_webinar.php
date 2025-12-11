<?php
require_once '../includes/config.php';
if (!isset($_SESSION['peserta_id'])) { header('Location: /projek-ifws/login.php'); exit(); }
$nama_peserta = htmlspecialchars($_SESSION['peserta_nama']);
$email_peserta = htmlspecialchars($_SESSION['peserta_email']);

$base_query = "SELECT w.*, GROUP_CONCAT(DISTINCT n.nama SEPARATOR ', ') AS daftar_narasumber 
                FROM webinars w 
                LEFT JOIN webinar_narasumber wn ON w.id = wn.id_webinar 
                LEFT JOIN narasumber n ON wn.id_narasumber = n.id";

$query_upcoming = $base_query . " WHERE w.status = 'published' GROUP BY w.id ORDER BY w.tanggal_direncanakan ASC";
$result_upcoming = mysqli_query($koneksi, $query_upcoming);

$query_finished = $base_query . " WHERE w.status = 'finished' GROUP BY w.id ORDER BY w.tanggal_direncanakan DESC";
$result_finished = mysqli_query($koneksi, $query_finished);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0" /><title>List Webinar</title>
    <link rel="stylesheet" href="/projek-ifws/assets/css/style.css" />
    <link rel="stylesheet" href="/projek-ifws/assets/css/Peserta/peserta.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo-and-title"><img src="/projek-ifws/assets/picture/logo.png" alt="Logo Informatics" class="sidebar-logo"/><h2>Informatics<br /><span>Webinar Series</span></h2></div>
                <div class="peserta-profile"><img src="/projek-ifws/assets/picture/default_profile.png" alt="Profile" class="profile-pic" /><span class="profile-name"><?= $nama_peserta ?></span><span class="profile-email"><?= $email_peserta ?></span></div>
            </div>
            <nav class="sidebar-nav">
                <div class="nav-section"><p class="section-title">DASHBOARD</p><ul><li><a href="/projek-ifws/Peserta/dashboard.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li></ul></div>
                <div class="nav-section"><p class="section-title">IFWS</p><ul><li class="active"><a href="/projek-ifws/Peserta/list_webinar.php"><i class="fas fa-calendar-alt"></i><span>List Webinar</span></a></li></ul></div>
                <div class="nav-section"><p class="section-title">RIWAYAT</p><ul><li><a href="/projek-ifws/Peserta/riwayat_ifws.php"><i class="fas fa-history"></i><span>Riwayat IFWS</span></a></li><li><a href="/projek-ifws/Peserta/sertifikat.php"><i class="fas fa-award"></i><span>Sertifikat</span></a></li></ul></div>
                <div class="nav-section"><p class="section-title">PESERTA TUGAS AKHIR</p><ul><li><a href="/projek-ifws/Peserta/progress.php"><i class="fas fa-chart-line"></i><span>Progress</span></a></li></ul></div>
                <div class="nav-section"><p class="section-title">AKUN</p><ul><li><a href="/projek-ifws/logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li></ul></div>
            </nav>
        </aside>
        
        <main class="main-content">
            <div class="page-section">
                <h2>List IFWS</h2>
                <div class="webinar-gallery">
                    <?php if ($result_upcoming && mysqli_num_rows($result_upcoming) > 0): while ($webinar = mysqli_fetch_assoc($result_upcoming)): ?>
                        <div class="webinar-card" 
                             data-topik="<?= htmlspecialchars($webinar['topik']) ?>"
                             data-narasumber="<?= htmlspecialchars($webinar['daftar_narasumber'] ?? 'N/A') ?>"
                             data-kategori="<?= htmlspecialchars($webinar['kategori']) ?>"
                             data-tanggal="<?= date('d F Y', strtotime($webinar['tanggal_direncanakan'])) ?>"
                             data-waktu="<?= date('H:i', strtotime($webinar['jam_mulai'])) ?> - <?= date('H:i', strtotime($webinar['jam_selesai'])) ?> WIB"
                             data-poster="<?= htmlspecialchars($webinar['poster_path']) ?>">
                            
                            <img src="/projek-ifws/<?= htmlspecialchars($webinar['poster_path'] ?? 'assets/picture/default_poster.png') ?>" alt="Poster" class="webinar-card-poster">
<div class="webinar-card-content">
    <h3><?= htmlspecialchars($webinar['topik']) ?></h3>
    <p class="narasumber-name"><?= htmlspecialchars($webinar['daftar_narasumber'] ?? 'N/A') ?></p>
    
    <div class="webinar-info">
        <span class="info-item">
            <i class="fas fa-calendar-alt"></i> 
            <?= date('d M Y', strtotime($webinar['tanggal_direncanakan'])) ?>
        </span>
        <span class="info-item">
            <i class="far fa-clock"></i> 
            <?= date('H:i', strtotime($webinar['jam_mulai'])) ?> - <?= date('H:i', strtotime($webinar['jam_selesai'])) ?> WIB
        </span>
    </div>
    </div>
                            <div class="webinar-card-actions">
                                <button class="btn btn-success btn-open-detail">Detail</button>
                            </div>
                        </div>
                    <?php endwhile; else: ?>
                        <p>Tidak ada webinar yang akan datang.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="page-section">
                <h2>Webinar Sebelumnya</h2>
                <div class="webinar-gallery">
                    <?php if ($result_finished && mysqli_num_rows($result_finished) > 0): while ($webinar = mysqli_fetch_assoc($result_finished)): ?>
                        <div class="webinar-card finished"
                             data-topik="<?= htmlspecialchars($webinar['topik']) ?>"
                             data-narasumber="<?= htmlspecialchars($webinar['daftar_narasumber'] ?? 'N/A') ?>"
                             data-kategori="<?= htmlspecialchars($webinar['kategori']) ?>"
                             data-tanggal="<?= date('d F Y', strtotime($webinar['tanggal_direncanakan'])) ?>"
                             data-waktu="<?= date('H:i', strtotime($webinar['jam_mulai'])) ?> - <?= date('H:i', strtotime($webinar['jam_selesai'])) ?> WIB"
                             data-poster="<?= htmlspecialchars($webinar['poster_path']) ?>">
                            <img src="/projek-ifws/<?= htmlspecialchars($webinar['poster_path'] ?? 'assets/picture/default_poster.png') ?>" alt="Poster" class="webinar-card-poster">
                            <div class="webinar-card-content">
                                <h3><?= htmlspecialchars($webinar['topik']) ?></h3>
                                <p><?= htmlspecialchars($webinar['daftar_narasumber'] ?? 'N/A') ?></p>
                            </div>
                            <div class="webinar-card-actions">
                                <button class="btn btn-disabled" disabled>Selesai</button>
                            </div>
                        </div>
                    <?php endwhile; else: ?>
                        <p>Belum ada riwayat webinar.</p>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <div id="webinar-detail-overlay" class="overlay hidden webinar-detail-overlay">
        <div class="overlay-content">
            <div class="overlay-header">
                <h3 class="overlay-main-title">Detail Webinar</h3>
                <button class="btn-close btn-close-overlay">&times;</button>
            </div>
            <div class="overlay-body">
                <div class="detail-content-left">
                    <img id="detail-poster" src="" alt="Poster Webinar">
                </div>
                <div class="detail-content-right">
                    <h2 id="detail-title"></h2>
                    <div class="detail-info">
                        <div class="detail-info-item"><strong>Narasumber :</strong><span id="detail-narasumber"></span></div>
                        <div class="detail-info-item"><strong>Kategori :</strong><span id="detail-kategori"></span></div>
                        <div class="detail-info-item"><strong>Tanggal :</strong><span id="detail-tanggal"></span></div>
                        <div class="detail-info-item"><strong>Jam :</strong><span id="detail-waktu"></span></div>
                    </div>
                    <div class="detail-actions">
                        <a href="https://tiny.cc/ifws" class="btn btn-success"> Daftar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="/projek-ifws/assets/scripts/Peserta/peserta.js"></script>
</body>
</html>
