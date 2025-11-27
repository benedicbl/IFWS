<?php
require_once '../includes/config.php';
// Proteksi Session
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'bendahara') { 
    header('Location: /projek-ifws/login.php'); 
    exit(); 
}

// ---------------------------------------------------------
// 1. LOGIKA FILTER TAHUN AJARAN
// ---------------------------------------------------------
// Ambil tahun-tahun yang tersedia di database (dari webinar yang sudah finished)
$query_years = "SELECT DISTINCT YEAR(tanggal_direncanakan) as tahun 
                FROM webinars 
                WHERE status = 'finished' 
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
$where_clause = "WHERE w.status = 'finished'"; // Default condition

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
        $where_clause .= " AND w.tanggal_direncanakan BETWEEN '$start_date' AND '$end_date'";
    }
}

// ---------------------------------------------------------
// 2. QUERY UTAMA
// ---------------------------------------------------------
// Ambil semua webinar yang sudah selesai (Filtered)
$query = "SELECT w.*, GROUP_CONCAT(n.nama SEPARATOR ', ') AS daftar_narasumber 
          FROM webinars w 
          LEFT JOIN webinar_narasumber wn ON w.id = wn.id_webinar 
          LEFT JOIN narasumber n ON wn.id_narasumber = n.id 
          $where_clause
          GROUP BY w.id 
          ORDER BY w.tanggal_direncanakan DESC";
$result_webinars = mysqli_query($koneksi, $query);

// Ambil semua bukti insentif yang sudah ada
$query_bukti = "SELECT * FROM bukti_insentif";
$result_bukti = mysqli_query($koneksi, $query_bukti);
$bukti_by_webinar = [];
if($result_bukti) {
    while($row = mysqli_fetch_assoc($result_bukti)) {
        $bukti_by_webinar[$row['id_webinar']] = $row['file_path'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0" /><title>Bendahara - Data Narasumber</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="/projek-ifws/assets/css/style.css" />
    <link rel="stylesheet" href="/projek-ifws/assets/css/Bendahara/bendahara.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>
    
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
            <div class="sidebar-header"><div class="logo-and-title"><img src="/projek-ifws/assets/picture/logo.png" alt="Logo Informatics" class="sidebar-logo" /><h2>Informatics<br /><span>Webinar Series</span></h2></div><div class="admin-profile"><small>Bendahara</small></div></div>
            <nav class="sidebar-nav">
                <div class="nav-section"><p class="section-title">DASHBOARD</p><ul><li><a href="/projek-ifws/Bendahara/bendahara.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li></ul></div>
                <div class="nav-section"><p class="section-title">KELOLA DATA IFWS</p><ul><li class="active"><a href="/projek-ifws/Bendahara/bendahara_datanarsum.php"><i class="fas fa-user-friends"></i><span>Data Narasumber</span></a></li><li><a href="/projek-ifws/Bendahara/bendahara_buktitrf.php"><i class="fas fa-file-invoice-dollar"></i><span>Bukti Insentif</span></a></li></ul></div>
                <div class="nav-section"><p class="section-title">AKUN</p><ul><li><a href="/projek-ifws/logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li></ul></div>
            </nav>
        </aside>
        <main class="main-content">
            <div class="page-header"><h1>Kelola Insentif Narasumber</h1></div>
            
            <form method="GET" action="" class="filter-container">
                <label for="filter_periode"><i class="fas fa-filter"></i> Tahun Akademik:</label>
                <select name="filter_periode" id="filter_periode" class="filter-select" onchange="this.form.submit()">
                    <option value="">-- Tampilkan Semua --</option>
                    <?php foreach ($available_years as $yr): ?>
                        <option value="Ganjil-<?= $yr ?>" <?= ($filter_periode == "Ganjil-$yr") ? 'selected' : '' ?>>Ganjil - <?= $yr ?> </option>
                        <option value="Genap-<?= $yr ?>" <?= ($filter_periode == "Genap-$yr") ? 'selected' : '' ?>>Genap - <?= $yr ?> </option>
                    <?php endforeach; ?>
                </select>
                <?php if(!empty($filter_periode)): ?><a href="bendahara_datanarsum.php" class="btn-reset"><i class="fas fa-times"></i> Reset</a><?php endif; ?>
            </form>

            <div class="content-card">
                <table>
                    <thead><tr><th>Topik Webinar</th><th>Narasumber</th><th>Tanggal</th><th>Bukti Insentif</th></tr></thead>
                    <tbody>
                        <?php if ($result_webinars && mysqli_num_rows($result_webinars) > 0): while ($webinar = mysqli_fetch_assoc($result_webinars)): ?>
                            <?php $adaBukti = isset($bukti_by_webinar[$webinar['id']]); ?>
                            <tr>
                                <td><?= htmlspecialchars($webinar['topik']) ?></td>
                                <td><?= htmlspecialchars($webinar['daftar_narasumber'] ?? 'N/A') ?></td>
                                <td><?= date('d/m/Y', strtotime($webinar['tanggal_direncanakan'])) ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-upload btn-buka-upload" data-id="<?= $webinar['id'] ?>" data-topik="<?= htmlspecialchars($webinar['topik']) ?>">
                                            <i class="fas fa-upload"></i> <?= $adaBukti ? 'Ganti' : 'Upload' ?>
                                        </button>
                                        <?php if ($adaBukti): ?>
                                            <button class="btn-icon-only btn-lihat-bukti has-photos" data-bukti="/projek-ifws/<?= htmlspecialchars($bukti_by_webinar[$webinar['id']]) ?>">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; else: ?>
                            <tr><td colspan="4" style="text-align: center;">Tidak ada webinar yang sudah selesai pada periode ini.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <div id="bukti-overlay" class="overlay hidden"><div class="overlay-content"><div class="overlay-header"><h3 class="overlay-main-title">Preview Bukti</h3><button class="btn-back btn-close-overlay">&lt; Back</button></div><div class="overlay-body"><img id="bukti-preview-img" src="" alt="Bukti Preview" /></div></div></div>
    <div id="upload-overlay" class="overlay hidden"><div class="overlay-content"><div class="overlay-header"><h3 class="overlay-main-title">Upload Bukti Insentif</h3><button class="btn-close btn-close-overlay">&times;</button></div><form action="/projek-ifws/api/upload_insentif.php" method="POST" enctype="multipart/form-data"><div class="overlay-body"><p id="upload-subtitle" class="overlay-subtitle"></p><input type="hidden" name="webinar_id" id="upload_webinar_id"><div class="form-group"><label for="bukti_file">File Bukti (JPG, PNG, PDF, maks 2MB)</label><input type="file" name="bukti_file" id="bukti_file" accept="image/jpeg, image/png, application/pdf" required></div></div><div class="overlay-footer"><button type="submit" class="btn btn-success">Upload Bukti</button></div></form></div></div>

    <script src="/projek-ifws/assets/scripts/Bendahara/bendahara.js"></script>
</body>
</html>