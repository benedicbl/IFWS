<?php
require_once '../includes/config.php';
if (!isset($_SESSION['peserta_id'])) { header('Location: /projek-ifws/login.php'); exit(); }
$id_peserta = $_SESSION['peserta_id'];
$nama_peserta = htmlspecialchars($_SESSION['peserta_nama']);
$email_peserta = htmlspecialchars($_SESSION['peserta_email']);

// Ambil riwayat kehadiran untuk peserta ini
$query_riwayat = "SELECT w.tanggal_direncanakan, w.topik, k.status_kehadiran, k.durasi_kehadiran,
                         GROUP_CONCAT(n.nama SEPARATOR ', ') AS daftar_narasumber
                  FROM webinars w
                  JOIN kehadiran k ON w.id = k.id_webinar AND k.id_peserta = ?
                  LEFT JOIN webinar_narasumber wn ON w.id = wn.id_webinar
                  LEFT JOIN narasumber n ON wn.id_narasumber = n.id
                  WHERE w.status = 'finished'
                  GROUP BY w.id
                  ORDER BY w.tanggal_direncanakan DESC";
$stmt = mysqli_prepare($koneksi, $query_riwayat);
mysqli_stmt_bind_param($stmt, "i", $id_peserta);
mysqli_stmt_execute($stmt);
$result_riwayat = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0" /><title>Riwayat IFWS</title>
    <link rel="stylesheet" href="/projek-ifws/assets/css/style.css" /><link rel="stylesheet" href="/projek-ifws/assets/css/Peserta/peserta.css" /><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <div class="sidebar-header"><div class="logo-and-title"><img src="/projek-ifws/assets/picture/logo.png" alt="Logo Informatics" class="sidebar-logo"/><h2>Informatics<br /><span>Webinar Series</span></h2></div><div class="peserta-profile"><img src="/projek-ifws/assets/picture/default_profile.png" alt="Profile" class="profile-pic" /><span class="profile-name"><?= $nama_peserta ?></span><span class="profile-email"><?= $email_peserta ?></span></div></div>
            <nav class="sidebar-nav">
                <div class="nav-section"><p class="section-title">DASHBOARD</p><ul><li><a href="/projek-ifws/Peserta/dashboard.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li></ul></div>
                <div class="nav-section"><p class="section-title">IFWS</p><ul><li><a href="/projek-ifws/Peserta/list_webinar.php"><i class="fas fa-calendar-alt"></i><span>List Webinar</span></a></li></ul></div>
                <div class="nav-section"><p class="section-title">RIWAYAT</p><ul><li class="active"><a href="/projek-ifws/Peserta/riwayat_ifws.php"><i class="fas fa-history"></i><span>Riwayat IFWS</span></a></li><li><a href="/projek-ifws/Peserta/sertifikat.php"><i class="fas fa-award"></i><span>Sertifikat</span></a></li></ul></div>
                <div class="nav-section"><p class="section-title">PESERTA TUGAS AKHIR</p><ul><li><a href="/projek-ifws/Peserta/progress.php"><i class="fas fa-chart-line"></i><span>Progress</span></a></li></ul></div>
                <div class="nav-section"><p class="section-title">AKUN</p><ul><li><a href="/projek-ifws/logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li></ul></div>
            </nav>
        </aside>
        <main class="main-content">
            <h1>Riwayat Kehadiran Webinar</h1>
            <div class="content-card">
                <table>
                    <thead><tr><th>Tanggal</th><th>Topik</th><th>Narasumber</th><th>Durasi</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php if ($result_riwayat && mysqli_num_rows($result_riwayat) > 0): while ($row = mysqli_fetch_assoc($result_riwayat)): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($row['tanggal_direncanakan'])) ?></td>
                            <td><?= htmlspecialchars($row['topik']) ?></td>
                            <td><?= htmlspecialchars($row['daftar_narasumber'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($row['durasi_kehadiran'] ?? '0') ?> menit</td>
                            <td>
                                <?php if ($row['status_kehadiran'] == 'hadir'): ?>
                                    <span class="status-badge status-valid">Hadir</span>
                                <?php else: ?>
                                    <span class="status-badge status-invalid">Tidak Hadir</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr><td colspan="5" style="text-align: center;">Anda belum memiliki riwayat webinar.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>