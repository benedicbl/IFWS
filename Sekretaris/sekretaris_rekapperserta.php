<?php
require_once '../includes/config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'sekretaris') { header('Location: /projek-ifws/login.php'); exit(); }
if (!isset($_GET['id_webinar']) || empty($_GET['id_webinar'])) { header('Location: /projek-ifws/Sekretaris/sekretaris_datawebinar.php'); exit(); }

$id_webinar = (int)$_GET['id_webinar'];

// 1. Ambil detail webinar
$query_webinar = "SELECT w.*, GROUP_CONCAT(n.nama SEPARATOR ', ') AS daftar_narasumber FROM webinars w LEFT JOIN webinar_narasumber wn ON w.id = wn.id_webinar LEFT JOIN narasumber n ON wn.id_narasumber = n.id WHERE w.id = ? GROUP BY w.id";
$stmt_webinar = mysqli_prepare($koneksi, $query_webinar); mysqli_stmt_bind_param($stmt_webinar, "i", $id_webinar); mysqli_stmt_execute($stmt_webinar);
$result_webinar = mysqli_stmt_get_result($stmt_webinar); $webinar = mysqli_fetch_assoc($result_webinar);
if (!$webinar) { header('Location: /projek-ifws/Sekretaris/sekretaris_datawebinar.php'); exit(); }

// 2. Ambil data kehadiran PESERTA
$query_peserta = "SELECT * FROM kehadiran WHERE id_webinar = ? AND role = 'peserta' ORDER BY nama_lengkap ASC";
$stmt_peserta = mysqli_prepare($koneksi, $query_peserta); mysqli_stmt_bind_param($stmt_peserta, "i", $id_webinar); mysqli_stmt_execute($stmt_peserta);
$result_peserta = mysqli_stmt_get_result($stmt_peserta);

// 3. Ambil data kehadiran PANITIA
$query_panitia = "SELECT * FROM kehadiran WHERE id_webinar = ? AND role = 'panitia' ORDER BY nama_lengkap ASC";
$stmt_panitia = mysqli_prepare($koneksi, $query_panitia); mysqli_stmt_bind_param($stmt_panitia, "i", $id_webinar); mysqli_stmt_execute($stmt_panitia);
$result_panitia = mysqli_stmt_get_result($stmt_panitia);

// 4. Ambil data kehadiran NARASUMBER
$query_narsum = "SELECT * FROM kehadiran WHERE id_webinar = ? AND role = 'narasumber' ORDER BY nama_lengkap ASC";
$stmt_narsum = mysqli_prepare($koneksi, $query_narsum); mysqli_stmt_bind_param($stmt_narsum, "i", $id_webinar); mysqli_stmt_execute($stmt_narsum);
$result_narsum = mysqli_stmt_get_result($stmt_narsum);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0" /><title>Rekap Peserta Webinar</title>
    <link rel="stylesheet" href="/projek-ifws/assets/css/style.css" />
    <link rel="stylesheet" href="/projek-ifws/assets/css/Sekretaris/sekretaris.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <div class="sidebar-header"><div class="logo-and-title"><img src="/projek-ifws/assets/picture/logo.png" alt="Logo Informatics" class="sidebar-logo"/><h2>Informatics<br /><span>Webinar Series</span></h2></div><div class="admin-profile"><small>Sekretaris</small></div></div>
            <nav class="sidebar-nav">
                <div class="nav-section"><p class="section-title">DASHBOARD</p><ul><li><a href="/projek-ifws/Sekretaris/sekretaris.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li></ul></div>
                <div class="nav-section"><p class="section-title">KELOLA DATA IFWS</p><ul><li class="active"><a href="/projek-ifws/Sekretaris/sekretaris_datawebinar.php"><i class="fas fa-calendar-alt"></i><span>Data Webinar</span></a></li><li><a href="/projek-ifws/Sekretaris/sekretaris_pesertaTA.php"><i class="fas fa-user-friends"></i><span>Peserta Tugas Akhir</span></a></li><li><a href="/projek-ifws/Sekretaris/sekretaris_kelolasertif.php"><i class="fas fa-award"></i><span>Kelola Sertifikat</span></a></li></ul></div>
                <div class="nav-section"><p class="section-title">AKUN</p><ul><li><a href="/projek-ifws/logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li></ul></div>
            </nav>
        </aside>
        <main class="main-content">
            <div class="page-header">
                <h1>Rekap Kehadiran Webinar</h1>
                <button id="btn-buka-upload" class="btn btn-secondary"><i class="fas fa-upload"></i> Import CSV Kehadiran</button>
            </div>
            
            <?php if(isset($_GET['status']) && $_GET['status'] == 'csv_sukses'): ?>
                <div class="alert alert-success">Berhasil mengimpor dan memperbarui <?= (int)($_GET['total'] ?? 0) ?> data kehadiran! (Narasumber ditambahkan dari database)</div>
            <?php elseif(isset($_GET['status'])): ?>
                 <div class="alert alert-danger">Terjadi kesalahan saat mengupload CSV.</div>
            <?php endif; ?>

            <div class="recap-info-header">
                <p><strong>Topik IFWS :</strong> <?= htmlspecialchars($webinar['topik']) ?></p>
            </div>
            
            <div class="table-section">
                <h2>Narasumber</h2>
                <div class="content-card">
                    <table>
                        <thead><tr><th>Nama</th><th>Email</th><th>Durasi</th><th>Status</th></tr></thead>
                        <tbody>
                            <?php if ($result_narsum && mysqli_num_rows($result_narsum) > 0): while ($row = mysqli_fetch_assoc($result_narsum)): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td>-</td>
                                <td><span class="status-badge status-valid">Valid</span></td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr><td colspan="4" style="text-align: center;">Belum ada data narasumber.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="table-section">
                <h2>Panitia</h2>
                <div class="content-card">
                    <table>
                        <thead><tr><th>Nama</th><th>Email</th><th>Durasi</th><th>Status</th></tr></thead>
                        <tbody>
                            <?php if ($result_panitia && mysqli_num_rows($result_panitia) > 0): while ($row = mysqli_fetch_assoc($result_panitia)): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><?= htmlspecialchars($row['durasi_kehadiran']) ?> menit</td>
                                <td><span class="status-badge status-valid">Valid</span></td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr><td colspan="4" style="text-align: center;">Belum ada data panitia.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="table-section">
                <h2>Peserta</h2>
                <div class="content-card">
                    <table>
                        <thead><tr><th>Nama Peserta</th><th>Email</th><th>Durasi</th><th>Status</th></tr></thead>
                        <tbody>
                            <?php if ($result_peserta && mysqli_num_rows($result_peserta) > 0): while ($row = mysqli_fetch_assoc($result_peserta)): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><?= htmlspecialchars($row['durasi_kehadiran']) ?> menit</td>
                                <td>
                                    <?php if ($row['status_kehadiran'] == 'hadir'): ?>
                                        <span class="status-badge status-valid">Valid</span>
                                    <?php else: ?>
                                        <span class="status-badge status-invalid">Tidak Valid</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr><td colspan="4" style="text-align: center;">Belum ada data peserta.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
             <div class="page-actions">
                <a href="/projek-ifws/Sekretaris/sekretaris_datawebinar.php" class="btn btn-primary">Kembali</a>
            </div>
        </main>
    </div>
    
    <div id="upload-csv-overlay" class="overlay hidden">
        <div class="overlay-content small">
            <div class="overlay-header"><h3 class="overlay-main-title">Import CSV Kehadiran</h3><button class="btn-close btn-close-overlay">&times;</button></div>
            <div class="overlay-body">
                <p>Upload file CSV (format tab-delimited) dari Zoom.</p>
                <form action="/projek-ifws/api/upload_kehadiran.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id_webinar" value="<?= $id_webinar ?>">
                    <div class="form-group">
                        <label for="file_csv">Pilih File CSV</label>
                        <input type="file" name="file_csv" id="file_csv" accept=".csv" required>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-success">Upload dan Proses</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="/projek-ifws/assets/scripts/Sekretaris/sekretaris.js"></script>
</body>
</html>