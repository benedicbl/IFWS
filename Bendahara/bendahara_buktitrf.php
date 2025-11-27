<?php
require_once '../includes/config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'bendahara') { header('Location: /projek-ifws/login.php'); exit(); }

$query = "SELECT b.*, w.topik, w.tanggal_direncanakan, GROUP_CONCAT(n.nama SEPARATOR ', ') AS daftar_narasumber
          FROM bukti_insentif b
          JOIN webinars w ON b.id_webinar = w.id
          LEFT JOIN webinar_narasumber wn ON w.id = wn.id_webinar
          LEFT JOIN narasumber n ON wn.id_narasumber = n.id
          GROUP BY b.id
          ORDER BY w.tanggal_direncanakan DESC";
$result_bukti = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0" /><title>Bendahara - Arsip Bukti Insentif</title>
    <link rel="stylesheet" href="/projek-ifws/assets/css/style.css" />
    <link rel="stylesheet" href="/projek-ifws/assets/css/Bendahara/bendahara.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <div class="sidebar-header"><div class="logo-and-title"><img src="/projek-ifws/assets/picture/logo.png" alt="Logo Informatics" class="sidebar-logo" /><h2>Informatics<br /><span>Webinar Series</span></h2></div><div class="admin-profile"><small>Bendahara</small></div></div>
            <nav class="sidebar-nav">
                <div class="nav-section"><p class="section-title">DASHBOARD</p><ul><li><a href="/projek-ifws/Bendahara/bendahara.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li></ul></div>
                <div class="nav-section"><p class="section-title">KELOLA DATA IFWS</p><ul><li><a href="/projek-ifws/Bendahara/bendahara_datanarsum.php"><i class="fas fa-user-friends"></i><span>Data Narasumber</span></a></li><li class="active"><a href="/projek-ifws/Bendahara/bendahara_buktitrf.php"><i class="fas fa-file-invoice-dollar"></i><span>Bukti Insentif</span></a></li></ul></div>
                <div class="nav-section"><p class="section-title">AKUN</p><ul><li><a href="/projek-ifws/logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li></ul></div>
            </nav>
        </aside>
        <main class="main-content">
            <div class="page-header"><h1>Arsip Bukti Insentif</h1></div>
            <div class="content-card">
                <table>
                    <thead><tr><th>Tanggal Webinar</th><th>Topik</th><th>Narasumber</th><th>Tanggal Upload</th><th>Aksi</th></tr></thead>
                    <tbody>
                        <?php if ($result_bukti && mysqli_num_rows($result_bukti) > 0): while ($bukti = mysqli_fetch_assoc($result_bukti)): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($bukti['tanggal_direncanakan'])) ?></td>
                            <td><?= htmlspecialchars($bukti['topik']) ?></td>
                            <td><?= htmlspecialchars($bukti['daftar_narasumber'] ?? 'N/A') ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($bukti['tanggal_upload'])) ?></td>
                            <td><button class="btn-icon-only btn-lihat-bukti has-photos" data-bukti="/projek-ifws/<?= htmlspecialchars($bukti['file_path']) ?>"><i class="fas fa-eye"></i></button></td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr><td colspan="5" style="text-align: center;">Belum ada bukti insentif yang diupload.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
    <div id="bukti-overlay" class="overlay hidden"><div class="overlay-content"><div class="overlay-header"><h3 class="overlay-main-title">Preview Bukti</h3><button class="btn-back btn-close-overlay">&lt; Back</button></div><div class="overlay-body"><img id="bukti-preview-img" src="" alt="Bukti Preview" /></div></div></div>
    <script src="/projek-ifws/assets/scripts/Bendahara/bendahara.js"></script>
</body>
</html>