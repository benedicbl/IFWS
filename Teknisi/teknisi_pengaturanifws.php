<?php
require_once '../includes/config.php';
// ... (Kode PHP di atas tidak berubah) ...
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teknisi') { header('Location: /projek-ifws/login.php'); exit(); }
$query = "SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('min_duration', 'min_ifws_ta')"; $result = mysqli_query($koneksi, $query); $settings = []; if($result){ while ($row = mysqli_fetch_assoc($result)) { $settings[$row['setting_key']] = $row['setting_value']; } } else { error_log("Gagal mengambil pengaturan: " . mysqli_error($koneksi)); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0" /><title>Teknisi - Pengaturan IFWS</title>
    <link rel="stylesheet" href="/projek-ifws/assets/css/style.css" />
    <link rel="stylesheet" href="/projek-ifws/assets/css/Teknisi/teknisi_pengaturanifws.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
</head>
<body>
    <div class="container">
        <aside class="sidebar">
             <div class="sidebar-header">
                <div class="logo-and-title"><img src="/projek-ifws/assets/picture/logo.png" alt="Logo Informatics" class="sidebar-logo" /><h2>Informatics<br /><span>Webinar Series</span></h2></div>
                <div class="admin-profile"><small>Teknisi</small></div>
            </div>
            <nav class="sidebar-nav">
                <div class="nav-section"><p class="section-title">DASHBOARD</p><ul><li><a href="/projek-ifws/Teknisi/teknisi.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li></ul></div>
                <div class="nav-section"><p class="section-title">KELOLA DATA IFWS</p><ul><li><a href="/projek-ifws/Teknisi/teknisi_datawebinar.php"><i class="fas fa-calendar-alt"></i><span>Data Webinar</span></a></li><li class="active"><a href="/projek-ifws/Teknisi/teknisi_pengaturanifws.php"><i class="fas fa-cogs"></i><span>Pengaturan IFWS</span></a></li></ul></div>
                <div class="nav-section"><p class="section-title">AKUN</p><ul><li><a href="/projek-ifws/logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li></ul></div>
            </nav>
        </aside>
        <main class="main-content">
            <h1 class="page-title">Pengaturan IFWS</h1>
            <?php if(isset($_GET['status']) && $_GET['status'] == 'sukses'): ?><div class="alert alert-success">Pengaturan berhasil disimpan!</div><?php elseif(isset($_GET['status']) && $_GET['status'] == 'gagal'): ?><div class="alert alert-danger">Gagal menyimpan pengaturan.</div><?php endif; ?>
            <form action="/projek-ifws/api/simpan_pengaturan.php" method="POST" id="form-pengaturan">
                <div class="setting-section"><h2 class="setting-title">Pengaturan Durasi Kehadiran</h2><div class="content-card setting-card"><div class="setting-item"><label for="min-duration">Durasi minimal kehadiran</label><div class="input-group"><input type="number" name="min_duration" id="min-duration" value="<?= htmlspecialchars($settings['min_duration'] ?? 45) ?>" min="0" required /><span>menit</span></div></div></div></div>
                <div class="setting-section"><h2 class="setting-title">Pengaturan Syarat Sidang TA</h2><div class="content-card setting-card"><div class="setting-item"><label for="min-ifws">Jumlah minimal IFWS</label><div class="input-group"><input type="number" name="min_ifws_ta" id="min-ifws" value="<?= htmlspecialchars($settings['min_ifws_ta'] ?? 3) ?>" min="0" required /><span>IFWS</span></div></div></div></div>
                <div class="page-actions"><button type="button" id="btn-simpan-pengaturan" class="btn btn-primary">Simpan Pengaturan</button></div>
            </form>
        </main>
    </div>
    <div id="simpan-confirm-overlay" class="overlay hidden"><div class="overlay-content small"><div class="overlay-header"><h3 class="overlay-main-title">Konfirmasi Simpan</h3><button class="btn-close btn-close-overlay">&times;</button></div><div class="overlay-body"><p>Yakin simpan pengaturan?</p><div class="form-actions confirm-actions"><button type="button" class="btn btn-secondary btn-close-overlay"><i class="fas fa-times"></i> Tidak</button><button type="button" id="btn-confirm-simpan" class="btn btn-success"><i class="fas fa-check"></i> Ya, Simpan</button></div></div></div></div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnSimpan = document.getElementById('btn-simpan-pengaturan'); const formPengaturan = document.getElementById('form-pengaturan'); const confirmOverlay = document.getElementById('simpan-confirm-overlay'); const btnConfirmSimpan = document.getElementById('btn-confirm-simpan');
            if (btnSimpan && formPengaturan && confirmOverlay && btnConfirmSimpan) {
                btnSimpan.addEventListener('click', function() { confirmOverlay.classList.remove('hidden'); });
                btnConfirmSimpan.addEventListener('click', function() { formPengaturan.submit(); });
                confirmOverlay.addEventListener('click', function(event) { if (event.target.closest('.btn-close-overlay') || event.target === confirmOverlay) { confirmOverlay.classList.add('hidden'); } });
            }
        });
    </script>
</body>
</html>