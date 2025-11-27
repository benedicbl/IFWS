<?php
require_once '../includes/config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header('Location: /projek-ifws/login.php'); exit(); }

// Query dasar dengan JOIN dan GROUP_CONCAT
$base_query = "SELECT w.*, GROUP_CONCAT(DISTINCT n.nama SEPARATOR ', ') AS daftar_narasumber 
               FROM webinars w 
               LEFT JOIN webinar_narasumber wn ON w.id = wn.id_webinar 
               LEFT JOIN narasumber n ON wn.id_narasumber = n.id";

// Query untuk Rencana Webinar
$query_rencana = $base_query . " WHERE w.status = 'rencana' GROUP BY w.id ORDER BY w.tanggal_direncanakan DESC";
$result_rencana = mysqli_query($koneksi, $query_rencana);

// Query untuk List Data Webinar (Published)
$query_published = $base_query . " WHERE w.status = 'published' GROUP BY w.id ORDER BY w.tanggal_direncanakan DESC";
$result_published = mysqli_query($koneksi, $query_published);

// Query untuk form dropdown/checkbox narasumber
$query_all_narasumber = "SELECT id, nama FROM narasumber ORDER BY nama ASC";
$result_all_narasumber = mysqli_query($koneksi, $query_all_narasumber);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0" /><title>Admin - Data Webinar</title>
    <link rel="stylesheet" href="/projek-ifws/assets/css/style.css"/>
    <link rel="stylesheet" href="/projek-ifws/assets/css/PIC/admin_listifws.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />

    <style>
        .checkboxes-container label {
            display: flex !important;
            align-items: center !important;
            justify-content: flex-start !important;
            padding: 0.4rem 0.75rem !important;
        }
        .checkboxes-container label input[type="checkbox"] {
            margin-right: 0.75rem !important;
            margin-left: 0 !important;
            width: auto !important;
            position: static !important;
        }
    </style>
    </head>
<body>
    <div class="container">
        <aside class="sidebar">
            <div class="sidebar-header"><div class="logo-and-title"><img src="/projek-ifws/assets/picture/logo.png" alt="Logo Informatics" class="sidebar-logo" /><h2>Informatics<br /><span>Webinar Series</span></h2></div><div class="admin-profile"><small>Admin</small></div></div>
            <nav class="sidebar-nav">
                <div class="nav-section"><p class="section-title">DASHBOARD</p><ul><li><a href="/projek-ifws/PIC/admin.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li></ul></div>
                
                <div class="nav-section">
                    <p class="section-title">KELOLA DATA IFWS</p>
                    <ul>
                        <li class="active"><a href="/projek-ifws/PIC/admin_listifws.php"><i class="fas fa-calendar-alt"></i><span>Data Webinar</span></a></li>
                        <li><a href="/projek-ifws/PIC/admin_riwayatifws.php"><i class="fas fa-history"></i><span>Riwayat Webinar</span></a></li>
                        <li><a href="/projek-ifws/PIC/admin_datanarsum.php"><i class="fas fa-user-friends"></i><span>Data Narasumber</span></a></li>
                        <li><a href="admin_pesertaTA.php"><i class="fas fa-user-friends"></i><span>Peserta Tugas Akhir</span></a></li>
                    </ul>
                </div>
                
                <div class="nav-section"><p class="section-title">KELOLA DATA ANGGOTA IFWS</p><ul><li><a href="/projek-ifws/PIC/admin_dataanggota.php"><i class="fas fa-users-cog"></i><span>Data Anggota IFWS</span></a></li></ul></div>
                <div class="nav-section"><p class="section-title">AKUN</p><ul><li><a href="/projek-ifws/logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li></ul></div>
            </nav>
        </aside>

        <main class="main-content">
            <div class="page-section">
                <div class="page-header"><h2>Rencana Webinar</h2><button id="btn-tambah" class="btn btn-success"><i class="fas fa-plus"></i> Tambah</button></div>
                <div class="content-card">
                    <table>
                        <thead><tr><th>Narasumber</th><th>Tanggal</th><th>Topik</th><th>Aksi</th></tr></thead>
                        <tbody>
                            <?php if ($result_rencana && mysqli_num_rows($result_rencana) > 0): while ($webinar = mysqli_fetch_assoc($result_rencana)): ?>
                                <tr>
                                    <td><?= htmlspecialchars($webinar['daftar_narasumber'] ?? 'N/A') ?></td>
                                    <td><?= date('d/m/Y', strtotime($webinar['tanggal_direncanakan'])) ?></td>
                                    <td><?= htmlspecialchars($webinar['topik']) ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn btn-icon-only btn-warning btn-update" data-id="<?= $webinar['id'] ?>"><i class="fas fa-pencil-alt"></i></button>
                                            <form action="/projek-ifws/api/hapus_webinar.php" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus webinar ini?');" style="display:inline;"><input type="hidden" name="webinar_id" value="<?= $webinar['id'] ?>"><button type="submit" class="btn btn-icon-only btn-danger"><i class="fas fa-trash"></i></button></form>
                                            <?php $posterAda = !empty($webinar['poster_path']); $linkAda = !empty($webinar['link_akses']); $bisaPublish = $posterAda && $linkAda; ?>
                                            <button class="btn-icon-only info-tooltip-trigger" data-poster-status="<?= $posterAda ? 'ada' : 'kosong' ?>" data-link-status="<?= $linkAda ? 'ada' : 'kosong' ?>" data-poster-path="<?= htmlspecialchars($webinar['poster_path']) ?>" data-link-url="<?= htmlspecialchars($webinar['link_akses']) ?>"><i class="fas fa-info-circle"></i></button>
                                            <?php if ($bisaPublish): ?>
                                                <button class="btn btn-success btn-publish" data-id="<?= $webinar['id'] ?>" data-topik="<?= htmlspecialchars($webinar['topik']) ?>">Publish</button>
                                            <?php else: ?>
                                                <button class="btn btn-disabled" disabled>Publish</button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; else: ?>
                                <tr><td colspan="4" style="text-align: center;">Tidak ada webinar yang direncanakan.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="page-section">
                <div class="page-header"><h2>List Data Webinar (Published)</h2></div>
                <div class="content-card">
                    <table>
                        <thead><tr><th>Narasumber</th><th>Tanggal</th><th>Waktu</th><th>Topik</th><th>Aksi</th></tr></thead>
                        <tbody>
                            <?php if ($result_published && mysqli_num_rows($result_published) > 0): while ($webinar = mysqli_fetch_assoc($result_published)): ?>
                            <tr>
                                <td><?= htmlspecialchars($webinar['daftar_narasumber'] ?? 'N/A') ?></td>
                                <td><?= date('d/m/Y', strtotime($webinar['tanggal_direncanakan'])) ?></td>
                                <td><?= date('H:i', strtotime($webinar['jam_mulai'])) ?> - <?= date('H:i', strtotime($webinar['jam_selesai'])) ?></td>
                                <td><?= htmlspecialchars($webinar['topik']) ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-icon-only btn-secondary btn-reschedule" 
                                                data-id="<?= $webinar['id'] ?>" 
                                                data-tanggal="<?= $webinar['tanggal_direncanakan'] ?>" 
                                                data-mulai="<?= $webinar['jam_mulai'] ?>" 
                                                data-selesai="<?= $webinar['jam_selesai'] ?>">
                                            <i class="fas fa-calendar-alt"></i>
                                        </button>
                                        <button class="btn btn-primary btn-selesai" 
                                                data-id="<?= $webinar['id'] ?>"
                                                data-topik="<?= htmlspecialchars($webinar['topik']) ?>">
                                            Selesai
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr><td colspan="5" style="text-align: center;">Tidak ada webinar yang sudah di-publish.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <div id="tambah-overlay" class="overlay hidden">
        <div class="overlay-content">
            <div class="overlay-header"><h3 class="overlay-main-title">Tambah Rencana Webinar</h3><button class="btn-close btn-close-overlay">&times;</button></div>
            <div class="overlay-body">
                <form action="/projek-ifws/api/tambah_webinar.php" method="POST">
                    <div class="form-group"><label>Narasumber (bisa pilih lebih dari satu)</label><div class="multi-select-container"><div class="select-box"><span class="select-box-text">Pilih Narasumber...</span></div><div id="narasumber-checkboxes" class="checkboxes-container"><?php if ($result_all_narasumber && mysqli_num_rows($result_all_narasumber) > 0): mysqli_data_seek($result_all_narasumber, 0); while ($narsum = mysqli_fetch_assoc($result_all_narasumber)): ?><label><input type="checkbox" name="narasumber_ids[]" value="<?= $narsum['id'] ?>" data-name="<?= htmlspecialchars($narsum['nama']) ?>" /><?= htmlspecialchars($narsum['nama']) ?></label><?php endwhile; else: ?><label>Tidak ada data narasumber</label><?php endif; ?></div></div></div>
                    <div class="form-group"><label for="tanggal">Tanggal Direncanakan</label><input type="date" id="tanggal" name="tanggal_direncanakan" required></div>
                    <div class="form-group-row"><div class="form-group"><label for="jam_mulai">Jam Mulai</label><input type="time" id="jam_mulai" name="jam_mulai" required></div><div class="form-group"><label for="jam_selesai">Jam Selesai</label><input type="time" id="jam_selesai" name="jam_selesai" required></div></div>
                    <div class="form-group"><label for="kategori">Kategori IFWS</label><select id="kategori" name="kategori" required><option value="" disabled selected>Pilih Kategori</option><option value="Umum">Umum</option><option value="Dosen">Dosen</option><option value="Internal">Internal</option></select></div>
                    <div class="form-group"><label for="topik">Topik</label><textarea id="topik" name="topik" rows="4" required></textarea></div>
                    <div classs="form-actions"><button type="submit" class="btn btn-success">Simpan</button></div>
                </form>
            </div>
        </div>
    </div>
    
    <div id="info-tooltip" class="tooltip-box hidden"><p class="tooltip-title">Perihal yang diperlukan:</p><ul class="tooltip-list"><li id="poster-check"><i class=""></i> Poster <span id="poster-status"></span><a href="#" id="view-poster-btn" class="btn-icon-only hidden"><i class="fas fa-eye"></i></a></li><li id="link-check"><i class=""></i> Link Akses <span id="link-status"></span><a href="#" id="view-link-btn" class="btn-icon-only hidden"><i class="fas fa-eye"></i></a></li></ul></div>
    <div id="poster-overlay" class="overlay hidden"><div class="overlay-content"><div class="overlay-header"><h3 class="overlay-main-title">Preview Poster</h3><button class="btn-back btn-close-overlay">&lt; Back</button></div><div class="overlay-body"><img id="poster-preview-img" src="" alt="Poster Preview" /></div></div></div>
    <div id="link-preview-overlay" class="overlay hidden"><div class="overlay-content"><div class="overlay-header"><h3 class="overlay-main-title">Link Akses Webinar</h3><button class="btn-close btn-close-overlay">&times;</button></div><div class="overlay-body"><div class="form-group"><label for="link-preview-text">Link dapat disalin di bawah ini:</label><input type="text" id="link-preview-text" readonly></div></div></div></div>
    <div id="publish-confirm-overlay" class="overlay hidden"><div class="overlay-content small"><div class="overlay-header"><h3 class="overlay-main-title">Konfirmasi Publish</h3><button class="btn-close btn-close-overlay">&times;</button></div><div class="overlay-body"><p id="publish-confirm-text">Apakah Anda yakin?</p><form id="publish-confirm-form" action="/projek-ifws/api/publish_webinar.php" method="POST"><input type="hidden" name="webinar_id" id="publish_webinar_id"><div class="form-actions confirm-actions"><button type="button" class="btn btn-secondary btn-close-overlay"><i class="fas fa-times"></i> Tidak</button><button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Ya</button></div></form></div></div></div>
    <div id="reschedule-overlay" class="overlay hidden"><div class="overlay-content small"><div class="overlay-header"><h3 class="overlay-main-title">Reschedule Webinar</h3><button class="btn-close btn-close-overlay">&times;</button></div><div class="overlay-body"><form action="/projek-ifws/api/reschedule_webinar.php" method="POST"><input type="hidden" name="webinar_id" id="reschedule_webinar_id"><div class="form-group"><label for="reschedule_tanggal">Tanggal Baru</label><input type="date" id="reschedule_tanggal" name="tanggal_direncanakan" required></div><div class="form-group-row"><div class="form-group"><label for="reschedule_jam_mulai">Jam Mulai Baru</label><input type="time" id="reschedule_jam_mulai" name="jam_mulai" required></div><div class="form-group"><label for="reschedule_jam_selesai">Jam Selesai Baru</label><input type="time" id="reschedule_jam_selesai" name="jam_selesai" required></div></div><div class="form-actions"><button type="submit" class="btn btn-success">Update Jadwal</button></div></form></div></div></div>
    <div id="selesai-confirm-overlay" class="overlay hidden"><div class="overlay-content small"><div class="overlay-header"><h3 class="overlay-main-title">Konfirmasi Penyelesaian</h3><button class="btn-close btn-close-overlay">&times;</button></div><div class="overlay-body"><p id="selesai-confirm-text">Apakah Anda yakin?</p><form id="selesai-confirm-form" action="/projek-ifws/api/selesaikan_webinar.php" method="POST"><input type="hidden" name="webinar_id" id="selesai_webinar_id"><div class="form-actions confirm-actions"><button type="button" class="btn btn-secondary btn-close-overlay"><i class="fas fa-times"></i> Tidak</button><button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Ya, Selesaikan</button></div></form></div></div></div>
    
    <script src="../assets/scripts/PIC/admin_listifws.js"></script>
</body>
</html>