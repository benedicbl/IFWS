<?php
require_once '../includes/config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'sekretaris') { header('Location: /projek-ifws/login.php'); exit(); }
if (!isset($_GET['id_webinar'])) { header('Location: /projek-ifws/Sekretaris/sekretaris_kelolasertif.php'); exit(); }
$id_webinar = (int)$_GET['id_webinar'];

// 1. Ambil detail webinar
$query_webinar = "SELECT w.*, GROUP_CONCAT(n.nama SEPARATOR ', ') AS daftar_narasumber FROM webinars w LEFT JOIN webinar_narasumber wn ON w.id = wn.id_webinar LEFT JOIN narasumber n ON wn.id_narasumber = n.id WHERE w.id = ? GROUP BY w.id";
$stmt_webinar = mysqli_prepare($koneksi, $query_webinar); mysqli_stmt_bind_param($stmt_webinar, "i", $id_webinar); mysqli_stmt_execute($stmt_webinar);
$result_webinar = mysqli_stmt_get_result($stmt_webinar); $webinar = mysqli_fetch_assoc($result_webinar);
if (!$webinar) { header('Location: sekretaris_kelolasertif.php'); exit(); }

// 2. Ambil data narasumber
$query_narsum = "SELECT * FROM kehadiran WHERE id_webinar = ? AND role = 'narasumber' ORDER BY nama_lengkap ASC";
$stmt_narsum = mysqli_prepare($koneksi, $query_narsum); mysqli_stmt_bind_param($stmt_narsum, "i", $id_webinar); mysqli_stmt_execute($stmt_narsum);
$result_narsum = mysqli_stmt_get_result($stmt_narsum);

// 3. Ambil data panitia
$query_panitia = "SELECT * FROM kehadiran WHERE id_webinar = ? AND role = 'panitia' ORDER BY nama_lengkap ASC";
$stmt_panitia = mysqli_prepare($koneksi, $query_panitia); mysqli_stmt_bind_param($stmt_panitia, "i", $id_webinar); mysqli_stmt_execute($stmt_panitia);
$result_panitia = mysqli_stmt_get_result($stmt_panitia);

// 4. Ambil data peserta
$query_peserta = "SELECT * FROM kehadiran WHERE id_webinar = ? AND role = 'peserta' ORDER BY nama_lengkap ASC";
$stmt_peserta = mysqli_prepare($koneksi, $query_peserta); mysqli_stmt_bind_param($stmt_peserta, "i", $id_webinar); mysqli_stmt_execute($stmt_peserta);
$result_peserta = mysqli_stmt_get_result($stmt_peserta);

// 5. Cek apakah ada yang eligible (hadir)
$can_generate = false;
$query_check = "SELECT id FROM kehadiran WHERE id_webinar = ? AND status_kehadiran = 'hadir' LIMIT 1";
$stmt_check = mysqli_prepare($koneksi, $query_check); mysqli_stmt_bind_param($stmt_check, "i", $id_webinar); mysqli_stmt_execute($stmt_check);
$result_check = mysqli_stmt_get_result($stmt_check);
if (mysqli_num_rows($result_check) > 0) { $can_generate = true; }
mysqli_stmt_close($stmt_check);

// 6. Cek apakah file template gambar ada
$template_ada = file_exists('../assets/picture/templates/template_sertifikat.jpg');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0" /><title>Detail Sertifikat</title>
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
                <div class="nav-section"><p class="section-title">KELOLA DATA IFWS</p><ul><li><a href="/projek-ifws/Sekretaris/sekretaris_datawebinar.php"><i class="fas fa-calendar-alt"></i><span>Data Webinar</span></a></li><li><a href="/projek-ifws/Sekretaris/sekretaris_pesertaTA.php"><i class="fas fa-user-friends"></i><span>Peserta Tugas Akhir</span></a></li><li class="active"><a href="/projek-ifws/Sekretaris/sekretaris_kelolasertif.php"><i class="fas fa-award"></i><span>Kelola Sertifikat</span></a></li></ul></div>
                <div class="nav-section"><p class="section-title">AKUN</p><ul><li><a href="/projek-ifws/logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li></ul></div>
            </nav>
        </aside>
        <main class="main-content">
            <h1>Kelola Sertifikat</h1>
            <div id="upload-alert" class="alert hidden"></div>
            
            <div class="recap-info-header">
                <p><strong>Topik IFWS :</strong> <?= htmlspecialchars($webinar['topik']) ?></p>
            </div>
            
            <div class="template-section">
                <div class="template-card">
                    <h3>Template Sertifikat (JPG/PNG)</h3>
                    <span class="template-status">
                        <?php if ($template_ada): ?>
                            <i class="fas fa-check-circle icon-success" title="Template sudah diupload"></i>
                        <?php endif; ?>
                        <button class="btn btn-secondary btn-open-upload" data-type="template"><i class="fas fa-upload"></i> Upload</button>
                    </span>
                </div>
            </div>

            <div class="table-section">
                <h2>Narasumber</h2>
                <div class="content-card">
                    <table>
                        <thead><tr><th>Nama</th><th>Email</th><th>Sertifikat</th><th></th></tr></thead>
                        <tbody>
                            <?php if ($result_narsum && mysqli_num_rows($result_narsum) > 0): mysqli_data_seek($result_narsum, 0); while ($row = mysqli_fetch_assoc($result_narsum)): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><?php if (!empty($row['path_sertifikat'])): ?><span class="status-text status-generated">Sudah</span><?php else: ?><span class="status-text status-pending">Belum</span><?php endif; ?></td>
                                <td><?php if (!empty($row['path_sertifikat'])): ?><a href="/projek-ifws/<?= htmlspecialchars($row['path_sertifikat']) ?>" target="_blank" class="btn btn-primary">Preview</a><?php else: ?><a href="#" class="btn btn-disabled">Preview</a><?php endif; ?></td>
                            </tr>
                            <?php endwhile; else: ?> <tr><td colspan="4" style="text-align: center;">Tidak ada data narasumber.</td></tr> <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="table-section">
                <h2>Panitia</h2>
                <div class="content-card">
                     <table>
                        <thead><tr><th>Nama</th><th>Email</th><th>Durasi</th><th>Status</th><th>Sertifikat</th><th>Aksi</th></tr></thead>
                        <tbody>
                            <?php if ($result_panitia && mysqli_num_rows($result_panitia) > 0): mysqli_data_seek($result_panitia, 0); while ($row = mysqli_fetch_assoc($result_panitia)): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><?= htmlspecialchars($row['durasi_kehadiran']) ?> mnt</td>
                                <td>
                                    <span class="status-badge status-valid">Valid</span>
                                    <?php if ($row['override_sertifikat'] == 1): ?><i class="fas fa-exclamation-circle icon-override" title="Status di-override"></i><?php endif; ?>
                                </td>
                                <td><?php if (!empty($row['path_sertifikat'])): ?><span class="status-text status-generated">Sudah</span><?php else: ?><span class="status-text status-pending">Belum</span><?php endif; ?></td>
                                <td>
                                    <?php if (!empty($row['path_sertifikat'])): ?>
                                        <a href="/projek-ifws/<?= htmlspecialchars($row['path_sertifikat']) ?>" target="_blank" class="btn btn-primary">Preview</a>
                                    <?php elseif ($row['status_kehadiran'] == 'tidak_hadir'): ?>
                                        <button class="btn btn-override btn-buka-override" data-id="<?= $row['id'] ?>" data-nama="<?= htmlspecialchars($row['nama_lengkap']) ?>">Izinkan</button>
                                    <?php else: ?>
                                        <a href="#" class="btn btn-disabled">Preview</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; else: ?> <tr><td colspan="6" style="text-align: center;">Tidak ada data panitia.</td></tr> <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="table-section">
                <h2>Peserta</h2>
                <div class="content-card">
                    <table>
                        <thead><tr><th>Nama</th><th>Email</th><th>Durasi</th><th>Status</th><th>Sertifikat</th><th>Aksi</th></tr></thead>
                        <tbody>
                            <?php if ($result_peserta && mysqli_num_rows($result_peserta) > 0): mysqli_data_seek($result_peserta, 0); while ($row = mysqli_fetch_assoc($result_peserta)): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><?= htmlspecialchars($row['durasi_kehadiran']) ?> mnt</td>
                                <td>
                                    <?php if ($row['status_kehadiran'] == 'hadir'): ?>
                                        <span class="status-badge status-valid">Valid</span>
                                        <?php if ($row['override_sertifikat'] == 1): ?><i class="fas fa-exclamation-circle icon-override" title="Status di-override"></i><?php endif; ?>
                                    <?php else: ?>
                                        <span class="status-badge status-invalid">Tidak Valid</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php if (!empty($row['path_sertifikat'])): ?><span class="status-text status-generated">Sudah</span><?php else: ?><span class="status-text status-pending">Belum</span><?php endif; ?></td>
                                <td>
                                    <?php if (!empty($row['path_sertifikat'])): ?>
                                        <a href="/projek-ifws/<?= htmlspecialchars($row['path_sertifikat']) ?>" target="_blank" class="btn btn-primary">Preview</a>
                                    <?php elseif ($row['status_kehadiran'] == 'tidak_hadir'): ?>
                                        <button class="btn btn-override btn-buka-override" data-id="<?= $row['id'] ?>" data-nama="<?= htmlspecialchars($row['nama_lengkap']) ?>">Izinkan</button>
                                    <?php else: ?>
                                        <a href="#" class="btn btn-disabled">Preview</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr><td colspan="6" style="text-align: center;">Belum ada data kehadiran.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="page-actions">
                <form id="form-generate" action="/projek-ifws/api/generate_sertifikat.php" method="POST" style="display:inline;"><input type="hidden" name="id_webinar" value="<?= $id_webinar ?>"><button type="submit" id="btn-generate" class="btn btn-primary <?= !($can_generate && $template_ada) ? 'btn-disabled' : '' ?>" <?= !($can_generate && $template_ada) ? 'disabled' : '' ?>><span id="generate-spinner"></span><span id="generate-text">Generate Sertifikat</span></button></form>
                <a href="#" class="btn btn-secondary btn-disabled">Kirim Email</a>
                <a href="/projek-ifws/Sekretaris/sekretaris_kelolasertif.php" class="btn btn-secondary">Kembali</a>
            </div>
        </main>
    </div>

    <div id="upload-template-overlay" class="overlay hidden">
        <div class="overlay-content small">
            <div class="overlay-header"><h3 class="overlay-main-title" id="upload-template-title">Upload Template</h3><button class="btn-close btn-close-overlay">&times;</button></div>
            <div class="overlay-body">
                <p>Pilih file template gambar (.jpg atau .png). File lama akan ditimpa.</p>
                <form id="form-upload-template" enctype="multipart/form-data">
                    <input type="hidden" name="id_webinar" value="<?= $id_webinar ?>">
                    <input type="hidden" name="template_type" id="template_type_input" value="template"> <div class="form-group"><label for="template_file">File Template</label><input type="file" name="template_file" id="template_file" accept="image/jpeg, image/png" required></div>
                    <div class="form-actions"><button type="submit" id="btn-submit-template" class="btn btn-success">Simpan Template</button></div>
                </form>
            </div>
        </div>
    </div>
    
    <div id="override-confirm-overlay" class="overlay hidden">
        <div class="overlay-content small">
            <div class="overlay-header"><h3 class="overlay-main-title">Konfirmasi Override</h3><button class="btn-close btn-close-overlay">&times;</button></div>
            <div class="overlay-body">
                <p id="override-confirm-text">Apakah Anda yakin?</p>
                <form id="form-override" action="/projek-ifws/api/override_sertifikat.php" method="POST">
                    <input type="hidden" name="id_kehadiran" id="override_kehadiran_id">
                    <div class="form-actions confirm-actions">
                        <button type="button" class="btn btn-secondary btn-close-overlay"><i class="fas fa-times"></i> Tidak</button>
                        <button type="submit" id="btn-confirm-override" class="btn btn-success"><i class="fas fa-check"></i> Ya, Izinkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="/projek-ifws/assets/scripts/Sekretaris/detailsertif.js"></script>
</body>
</html>