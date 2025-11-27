<?php
require_once '../includes/config.php';
if (!isset($_SESSION['peserta_id'])) { header('Location: /projek-ifws/login.php'); exit(); }
$id_peserta = $_SESSION['peserta_id'];
$nama_peserta = htmlspecialchars($_SESSION['peserta_nama']);
$email_peserta = htmlspecialchars($_SESSION['peserta_email']);

// 1. Cek apakah peserta ini adalah peserta TA
$query_status_ta = "SELECT status_ta FROM peserta WHERE id = ?";
$stmt_ta = mysqli_prepare($koneksi, $query_status_ta);
mysqli_stmt_bind_param($stmt_ta, "i", $id_peserta);
mysqli_stmt_execute($stmt_ta);
$result_ta = mysqli_stmt_get_result($stmt_ta);
$status_ta = mysqli_fetch_assoc($result_ta)['status_ta'] ?? 'Bukan_TA';

// 2. Ambil syarat minimal IFWS
$query_settings = "SELECT setting_value FROM settings WHERE setting_key = 'min_ifws_ta'";
$result_settings = mysqli_query($koneksi, $query_settings);
$min_ifws_ta = (int)(mysqli_fetch_assoc($result_settings)['setting_value'] ?? 3); // Default 3

// 3. Hitung jumlah kehadiran VALID (dari tabel kehadiran baru)
$query_total_hadir = "SELECT COUNT(id) AS total_hadir FROM kehadiran WHERE id_peserta = ? AND status_kehadiran = 'hadir' AND role = 'peserta'";
$stmt_hadir = mysqli_prepare($koneksi, $query_total_hadir);
mysqli_stmt_bind_param($stmt_hadir, "i", $id_peserta);
mysqli_stmt_execute($stmt_hadir);
$total_hadir = (int)(mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_hadir))['total_hadir'] ?? 0);

// 4. Tentukan status eligibilitas
$is_eligible = $total_hadir >= $min_ifws_ta;

// 5. Ambil daftar riwayat yang valid (untuk ditampilkan di tabel)
$query_riwayat = "SELECT w.tanggal_direncanakan, w.topik, k.durasi_kehadiran,
                         GROUP_CONCAT(n.nama SEPARATOR ', ') AS daftar_narasumber
                  FROM kehadiran k
                  JOIN webinars w ON k.id_webinar = w.id
                  LEFT JOIN webinar_narasumber wn ON w.id = wn.id_webinar
                  LEFT JOIN narasumber n ON wn.id_narasumber = n.id
                  WHERE k.id_peserta = ? AND k.status_kehadiran = 'hadir' AND k.role = 'peserta'
                  GROUP BY w.id
                  ORDER BY w.tanggal_direncanakan DESC";
$stmt_riwayat = mysqli_prepare($koneksi, $query_riwayat);
mysqli_stmt_bind_param($stmt_riwayat, "i", $id_peserta);
mysqli_stmt_execute($stmt_riwayat);
$result_riwayat = mysqli_stmt_get_result($stmt_riwayat);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0" /><title>Progress TA</title>
    <link rel="stylesheet" href="/projek-ifws/assets/css/style.css" />
    <link rel="stylesheet" href="/projek-ifws/assets/css/Peserta/peserta.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <div class="sidebar-header"><div class="logo-and-title"><img src="/projek-ifws/assets/picture/logo.png" alt="Logo Informatics" class="sidebar-logo"/><h2>Informatics<br /><span>Webinar Series</span></h2></div><div class="peserta-profile"><img src="/projek-ifws/assets/picture/default_profile.png" alt="Profile" class="profile-pic" /><span class="profile-name"><?= $nama_peserta ?></span><span class="profile-email"><?= $email_peserta ?></span></div></div>
            <nav class="sidebar-nav">
                <div class="nav-section"><p class="section-title">DASHBOARD</p><ul><li><a href="/projek-ifws/Peserta/dashboard.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li></ul></div>
                <div class="nav-section"><p class="section-title">IFWS</p><ul><li><a href="/projek-ifws/Peserta/list_webinar.php"><i class="fas fa-calendar-alt"></i><span>List Webinar</span></a></li></ul></div>
                <div class="nav-section"><p class="section-title">RIWAYAT</p><ul><li><a href="/projek-ifws/Peserta/riwayat_ifws.php"><i class="fas fa-history"></i><span>Riwayat IFWS</span></a></li><li><a href="/projek-ifws/Peserta/sertifikat.php"><i class="fas fa-award"></i><span>Sertifikat</span></a></li></ul></div>
                <div class="nav-section"><p class="section-title">PESERTA TUGAS AKHIR</p><ul><li class="active"><a href="/projek-ifws/Peserta/progress.php"><i class="fas fa-chart-line"></i><span>Progress</span></a></li></ul></div>
                <div class="nav-section"><p class="section-title">AKUN</p><ul><li><a href="/projek-ifws/logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li></ul></div>
            </nav>
        </aside>
        <main class="main-content">
            <h1>Progress Tugas Akhir</h1>
            
            <?php if ($status_ta == 'Bukan_TA'): ?>
                <div class="progress-card">
                    <div class="progress-message status-invalid">
                        <i class="fas fa-info-circle"></i> Akun Anda tidak terdaftar sebagai peserta Tugas Akhir.
                    </div>
                </div>
            <?php else: ?>
                <div class="progress-card">
                    <div class="progress-card-header">
                        <h2>Progress Prasyarat Sidang (<?= htmlspecialchars(str_replace('_', ' ', $status_ta)) ?>)</h2>
                        <span class="progress-count"><?= $total_hadir ?> / <?= $min_ifws_ta ?></span>
                    </div>
                    <?php if ($is_eligible): ?>
                        <div class="progress-message status-valid">
                            <i class="fas fa-check-circle"></i> Selamat! Anda sudah memenuhi prasyarat sidang.
                        </div>
                    <?php else: ?>
                        <div class="progress-message status-invalid">
                            <i class="fas fa-exclamation-triangle"></i> Anda belum memenuhi prasyarat sidang. (Diperlukan kehadiran <?= $min_ifws_ta - $total_hadir ?> lagi)
                        </div>
                    <?php endif; ?>
                </div>

                <div class="page-section">
                    <h2>Riwayat IFWS (Hadir & Valid)</h2>
                    <div class="content-card">
                        <table>
                            <thead><tr><th>Tanggal</th><th>Topik</th><th>Narasumber</th><th>Durasi</th></tr></thead>
                            <tbody>
                                <?php if ($result_riwayat && mysqli_num_rows($result_riwayat) > 0): mysqli_data_seek($result_riwayat, 0); while ($row = mysqli_fetch_assoc($result_riwayat)): ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($row['tanggal_direncanakan'])) ?></td>
                                    <td><?= htmlspecialchars($row['topik']) ?></td>
                                    <td><?= htmlspecialchars($row['daftar_narasumber'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($row['durasi_kehadiran']) ?> menit</td>
                                </tr>
                                <?php endwhile; else: ?>
                                <tr><td colspan="4" style="text-align: center;">Anda belum memiliki riwayat kehadiran webinar yang valid.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

        </main>
    </div>
</body>
</html>