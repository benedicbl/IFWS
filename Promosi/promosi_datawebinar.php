<?php
require_once '../includes/config.php';
// Proteksi Session
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'promosi') { 
    header('Location: /projek-ifws/login.php'); 
    exit(); 
}

// ---------------------------------------------------------
// 1. LOGIKA FILTER TAHUN AJARAN
// ---------------------------------------------------------
// Ambil tahun-tahun yang tersedia di database
$query_years = "SELECT DISTINCT YEAR(tanggal_direncanakan) as tahun 
                FROM webinars 
                ORDER BY tahun DESC";
$result_years = mysqli_query($koneksi, $query_years);
$available_years = [];
if ($result_years) {
    while ($row = mysqli_fetch_assoc($result_years)) {
        if($row['tahun']) $available_years[] = $row['tahun'];
    }
}
if(empty($available_years)) $available_years[] = date('Y');

// Proses Filter
$filter_periode = isset($_GET['filter_periode']) ? $_GET['filter_periode'] : '';
$where_clause = ""; // Default kosong (ambil semua)

if (!empty($filter_periode)) {
    $parts = explode('-', $filter_periode);
    if (count($parts) == 2) {
        $semester = $parts[0];
        $tahun = $parts[1];

        if ($semester == 'Genap') {
            // Genap: Jan - Jun
            $start_date = "$tahun-01-01";
            $end_date = "$tahun-06-30";
        } else {
            // Ganjil: Jul - Des
            $start_date = "$tahun-07-01";
            $end_date = "$tahun-12-31";
        }
        $where_clause = "WHERE w.tanggal_direncanakan BETWEEN '$start_date' AND '$end_date'";
    }
}

// ---------------------------------------------------------
// 2. QUERY UTAMA
// ---------------------------------------------------------
$query = "SELECT w.id, w.tanggal_direncanakan, w.topik, w.poster_path, w.status, 
          GROUP_CONCAT(n.nama SEPARATOR ', ') AS daftar_narasumber 
          FROM webinars w 
          LEFT JOIN webinar_narasumber wn ON w.id = wn.id_webinar 
          LEFT JOIN narasumber n ON wn.id_narasumber = n.id 
          $where_clause
          GROUP BY w.id 
          ORDER BY w.tanggal_direncanakan DESC";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0" /><title>Promosi - Data Webinar</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="/projek-ifws/assets/css/style.css" />
    <link rel="stylesheet" href="/projek-ifws/assets/css/Promosi/promosi_datawebinar.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />

    <style>
        /* Terapkan Font Poppins */
        body, select, input, button { font-family: 'Poppins', sans-serif; }

        /* Style Filter */
        .filter-container { display: flex; align-items: center; gap: 10px; background: #fff; padding: 15px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .filter-container label { font-weight: 600; color: #555; font-size: 15px; }
        .filter-select { padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; outline: none; font-size: 14px; color: #333; }
        .btn-reset { text-decoration: none; color: #666; font-size: 14px; margin-left: 5px; font-weight: 500; }
        .btn-reset:hover { color: #d9534f; }
    </style>
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <div class="sidebar-header"><div class="logo-and-title"><img src="/projek-ifws/assets/picture/logo.png" alt="Logo Informatics" class="sidebar-logo" /><h2>Informatics<br /><span>Webinar Series</span></h2></div><div class="admin-profile"><small>Promosi</small></div></div>
            <nav class="sidebar-nav">
                <div class="nav-section"><p class="section-title">DASHBOARD</p><ul><li><a href="/projek-ifws/Promosi/promosi.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li></ul></div>
                <div class="nav-section"><p class="section-title">KELOLA DATA IFWS</p><ul><li class="active"><a href="/projek-ifws/Promosi/promosi_datawebinar.php"><i class="fas fa-calendar-alt"></i><span>Data Webinar</span></a></li></ul></div>
                <div class="nav-section"><p class="section-title">AKUN</p><ul><li><a href="/projek-ifws/logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li></ul></div>
            </nav>
        </aside>
        <main class="main-content">
            <div class="page-header"><h1>Kelola Poster Webinar</h1></div>
            
            <?php if(isset($_GET['status']) && $_GET['status'] == 'upload_sukses'): ?>
                <div class="alert alert-success">Poster berhasil diupload!</div>
            <?php endif; ?>

            <div class="content-card">
                <table>
                    <thead><tr><th>Topik Webinar</th><th>Narasumber</th><th>Tanggal</th><th>Status Poster</th><th>Aksi</th></tr></thead>
                    <tbody>
                        <?php if ($result && mysqli_num_rows($result) > 0): while ($webinar = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?= htmlspecialchars($webinar['topik']) ?></td>
                                <td><?= htmlspecialchars($webinar['daftar_narasumber'] ?? 'N/A') ?></td>
                                <td><?= date('d/m/Y', strtotime($webinar['tanggal_direncanakan'])) ?></td>
                                <td><?= $webinar['poster_path'] ? '<strong>Sudah diupload</strong>' : '<em>Belum ada</em>' ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-upload btn-buka-upload <?= ($webinar['status'] == 'finished') ? 'btn-disabled' : '' ?>" data-id="<?= $webinar['id'] ?>" data-topik="<?= htmlspecialchars($webinar['topik']) ?>" <?= ($webinar['status'] == 'finished') ? 'disabled' : '' ?>><i class="fas fa-upload"></i> Upload</button>
                                        <?php if ($webinar['poster_path']): ?>
                                            <button class="btn-icon-only btn-lihat-poster has-photos" data-poster="/projek-ifws/<?= htmlspecialchars($webinar['poster_path']) ?>"><i class="fas fa-eye"></i></button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; else: ?>
                            <tr><td colspan="5" style="text-align: center;">Tidak ada data webinar pada periode ini.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
    <div id="poster-overlay" class="overlay hidden"><div class="overlay-content"><div class="overlay-header"><h3 class="overlay-main-title">Preview Poster</h3><button class="btn-back btn-close-overlay">&lt; Back</button></div><div class="overlay-body"><img id="poster-preview-img" src="" alt="Poster Preview" /></div></div></div>
    <div id="upload-overlay" class="overlay hidden"><div class="overlay-content"><div class="overlay-header"><h3 class="overlay-main-title">Upload Poster</h3><button class="btn-close btn-close-overlay">&times;</button></div><form action="/projek-ifws/api/upload_poster.php" method="POST" enctype="multipart/form-data"><div class="overlay-body"><p id="upload-subtitle" class="overlay-subtitle"></p><input type="hidden" name="webinar_id" id="upload_webinar_id"><div class="form-group"><label for="poster_file">File (JPG, PNG, maks 2MB)</label><input type="file" name="poster_file" id="poster_file" accept="image/jpeg, image/png" required></div></div><div class="overlay-footer"><button type="submit" class="btn btn-success">Upload Poster</button></div></form></div></div>
    <script src="../assets/scripts/Promosi/promosi_datawebinar.js"></script> 
</body>
</html>