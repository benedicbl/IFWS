<?php
require_once '../includes/config.php';

// Proteksi session
if (!isset($_SESSION['peserta_id'])) {
    header('Location: /projek-ifws/login.php');
    exit();
}

$id_peserta = $_SESSION['peserta_id'];
$nama_peserta = htmlspecialchars($_SESSION['peserta_nama']);
$email_peserta = htmlspecialchars($_SESSION['peserta_email']);

// --- 1. LOGIKA WIDGET TA ---
// Cek status TA peserta
$query_status = "SELECT status_ta FROM peserta WHERE id = ?";
$stmt = mysqli_prepare($koneksi, $query_status);
mysqli_stmt_bind_param($stmt, "i", $id_peserta);
mysqli_stmt_execute($stmt);
$res_status = mysqli_stmt_get_result($stmt);
$status_ta = mysqli_fetch_assoc($res_status)['status_ta'] ?? 'Bukan_TA';

$total_hadir = 0;
$min_ifws_ta = 3; // Default fallback
$is_eligible = false;

if ($status_ta != 'Bukan_TA') {
    // Ambil syarat minimal dari settings
    $q_sett = "SELECT setting_value FROM settings WHERE setting_key = 'min_ifws_ta'";
    $r_sett = mysqli_query($koneksi, $q_sett);
    $min_ifws_ta = (int)(mysqli_fetch_assoc($r_sett)['setting_value'] ?? 3);

    // Hitung kehadiran valid
    $q_count = "SELECT COUNT(id) as total FROM kehadiran WHERE id_peserta = ? AND status_kehadiran = 'hadir' AND role = 'peserta'";
    $stmt_c = mysqli_prepare($koneksi, $q_count);
    mysqli_stmt_bind_param($stmt_c, "i", $id_peserta);
    mysqli_stmt_execute($stmt_c);
    $total_hadir = (int)mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_c))['total'];
    
    $is_eligible = $total_hadir >= $min_ifws_ta;
}

// --- 2. LOGIKA UPCOMING WEBINAR (ATTRACT) ---
// Ambil 1 webinar yang akan datang (status published, tanggal >= hari ini)
$query_upcoming = "SELECT * FROM webinars 
                   WHERE status = 'published' AND tanggal_direncanakan >= CURDATE() 
                   ORDER BY tanggal_direncanakan ASC LIMIT 1";
$res_upcoming = mysqli_query($koneksi, $query_upcoming);
$upcoming_webinar = mysqli_fetch_assoc($res_upcoming);

// --- 3. LOGIKA KOLEKSI SERTIFIKAT TERBARU (Limit 3) ---
$query_sertif = "SELECT w.topik, k.path_sertifikat 
                 FROM kehadiran k JOIN webinars w ON k.id_webinar = w.id 
                 WHERE k.id_peserta = ? AND k.path_sertifikat IS NOT NULL 
                 ORDER BY w.tanggal_direncanakan DESC LIMIT 3";
$stmt_s = mysqli_prepare($koneksi, $query_sertif);
mysqli_stmt_bind_param($stmt_s, "i", $id_peserta);
mysqli_stmt_execute($stmt_s);
$res_sertif = mysqli_stmt_get_result($stmt_s);

// --- 4. LOGIKA RIWAYAT TERBARU (Limit 3) ---
$query_history = "SELECT w.topik, w.tanggal_direncanakan, k.status_kehadiran 
                  FROM kehadiran k JOIN webinars w ON k.id_webinar = w.id 
                  WHERE k.id_peserta = ? 
                  ORDER BY w.tanggal_direncanakan DESC LIMIT 3";
$stmt_h = mysqli_prepare($koneksi, $query_history);
mysqli_stmt_bind_param($stmt_h, "i", $id_peserta);
mysqli_stmt_execute($stmt_h);
$res_history = mysqli_stmt_get_result($stmt_h);
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
                <div class="logo-and-title"><img src="/projek-ifws/assets/picture/logo.png" alt="Logo Informatics" class="sidebar-logo"/><h2>Informatics<br /><span>Webinar Series</span></h2></div>
                <div class="peserta-profile"><img src="/projek-ifws/assets/picture/default_profile.png" alt="Profile" class="profile-pic" /><span class="profile-name"><?= $nama_peserta ?></span><span class="profile-email"><?= $email_peserta ?></span></div>
            </div>
            <nav class="sidebar-nav">
                <div class="nav-section"><p class="section-title">DASHBOARD</p><ul><li class="active"><a href="/projek-ifws/Peserta/dashboard.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li></ul></div>
                <div class="nav-section"><p class="section-title">IFWS</p><ul><li><a href="/projek-ifws/Peserta/list_webinar.php"><i class="fas fa-calendar-alt"></i><span>List Webinar</span></a></li></ul></div>
                <div class="nav-section"><p class="section-title">RIWAYAT</p><ul><li><a href="/projek-ifws/Peserta/riwayat_ifws.php"><i class="fas fa-history"></i><span>Riwayat IFWS</span></a></li><li><a href="/projek-ifws/Peserta/sertifikat.php"><i class="fas fa-award"></i><span>Sertifikat</span></a></li></ul></div>
                <div class="nav-section"><p class="section-title">PESERTA TUGAS AKHIR</p><ul><li><a href="/projek-ifws/Peserta/progress.php"><i class="fas fa-chart-line"></i><span>Progress</span></a></li></ul></div>
                <div class="nav-section"><p class="section-title">AKUN</p><ul><li><a href="/projek-ifws/logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li></ul></div>
            </nav>
        </aside>
        
        <main class="main-content">
            <h1>Selamat Datang, <?= $nama_peserta ?>! 👋</h1>
            
            <div class="dashboard-grid">
                <div class="dashboard-left">
                    
                    <?php if ($status_ta != 'Bukan_TA'): ?>
                        <div class="dashboard-card widget-ta <?= $is_eligible ? 'bg-green' : 'bg-blue' ?>">
                            <div class="widget-content">
                                <h3>Progress Syarat Sidang</h3>
                                <div class="progress-bar-container">
                                    <div class="progress-info">
                                        <span><?= htmlspecialchars(str_replace('_', ' ', $status_ta)) ?></span>
                                        <span class="progress-fraction"><?= $total_hadir ?> / <?= $min_ifws_ta ?></span>
                                    </div>
                                    <?php $percent = min(100, ($total_hadir / $min_ifws_ta) * 100); ?>
                                    <div class="progress-track">
                                        <div class="progress-fill" style="width: <?= $percent ?>%;"></div>
                                    </div>
                                </div>
                                <p class="widget-note">
                                    <?php if($is_eligible): ?>
                                        <i class="fas fa-check-circle"></i> Syarat terpenuhi!
                                    <?php else: ?>
                                        <i class="fas fa-info-circle"></i> Ikuti <?= $min_ifws_ta - $total_hadir ?> webinar lagi.
                                    <?php endif; ?>
                                </p>
                                <a href="progress.php" class="btn-text-white">Lihat Detail &rarr;</a>
                            </div>
                            <div class="widget-icon"><i class="fas fa-user-graduate"></i></div>
                        </div>
                    <?php endif; ?>

                    <div class="section-header"><h2>Webinar Akan Datang</h2></div>
                    <?php if ($upcoming_webinar): ?>
                        <div class="dashboard-card upcoming-card">
                            <div class="upcoming-image">
                                <img src="/projek-ifws/<?= htmlspecialchars($upcoming_webinar['poster_path'] ?? 'assets/picture/default_poster.png') ?>" alt="Poster">
                            </div>
                            <div class="upcoming-details">
                                <span class="badge badge-umum"><?= htmlspecialchars($upcoming_webinar['kategori']) ?></span>
                                <h3><?= htmlspecialchars($upcoming_webinar['topik']) ?></h3>
                                <div class="upcoming-meta">
                                    <span><i class="far fa-calendar"></i> <?= date('d M Y', strtotime($upcoming_webinar['tanggal_direncanakan'])) ?></span>
                                    <span><i class="far fa-clock"></i> <?= date('H:i', strtotime($upcoming_webinar['jam_mulai'])) ?> WIB</span>
                                </div>
                                <a href="list_webinar.php" class="btn btn-primary">Lihat & Daftar</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="empty-state-card">
                            <p>Belum ada webinar baru yang dijadwalkan.</p>
                        </div>
                    <?php endif; ?>

                </div>

                <div class="dashboard-right">
                    
                    <div class="dashboard-card mini-list-card">
                        <div class="mini-card-header">
                            <h3>Sertifikat Terbaru</h3>
                            <a href="sertifikat.php">Lihat Semua</a>
                        </div>
                        <ul class="mini-list">
                            <?php if ($res_sertif && mysqli_num_rows($res_sertif) > 0): while ($row = mysqli_fetch_assoc($res_sertif)): ?>
                                <li>
                                    <div class="mini-list-icon bg-yellow"><i class="fas fa-award"></i></div>
                                    <div class="mini-list-info">
                                        <span class="mini-title"><?= htmlspecialchars($row['topik']) ?></span>
                                        <a href="/projek-ifws/<?= htmlspecialchars($row['path_sertifikat']) ?>" target="_blank" class="download-link">Unduh PDF</a>
                                    </div>
                                </li>
                            <?php endwhile; else: ?>
                                <li class="empty-item">Belum ada sertifikat.</li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <div class="dashboard-card mini-list-card">
                        <div class="mini-card-header">
                            <h3>Riwayat Terakhir</h3>
                            <a href="riwayat_ifws.php">Lihat Semua</a>
                        </div>
                        <ul class="mini-list">
                            <?php if ($res_history && mysqli_num_rows($res_history) > 0): while ($row = mysqli_fetch_assoc($res_history)): ?>
                                <li>
                                    <div class="mini-list-icon <?= $row['status_kehadiran'] == 'hadir' ? 'bg-green-light' : 'bg-gray' ?>">
                                        <i class="fas <?= $row['status_kehadiran'] == 'hadir' ? 'fa-check' : 'fa-times' ?>"></i>
                                    </div>
                                    <div class="mini-list-info">
                                        <span class="mini-title"><?= htmlspecialchars($row['topik']) ?></span>
                                        <span class="mini-date"><?= date('d M Y', strtotime($row['tanggal_direncanakan'])) ?></span>
                                    </div>
                                </li>
                            <?php endwhile; else: ?>
                                <li class="empty-item">Belum ada riwayat.</li>
                            <?php endif; ?>
                        </ul>
                    </div>

                </div>
            </div>
        </main>
    </div>
</body>
</html>