<?php
require_once '../includes/config.php';

// Validasi Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /projek-ifws/login.php'); exit();
}

// ---------------------------------------------------------
// 1. SETTING PERIODE (FILTER)
// ---------------------------------------------------------
$filter_periode = isset($_GET['filter_periode']) ? $_GET['filter_periode'] : '';
$peserta_where = "WHERE status_ta != 'Bukan_TA'"; 
$webinar_where = "AND w.status = 'finished'"; 
$filter_label = "(Semua Waktu)";

if (!empty($filter_periode)) {
    $parts = explode('-', $filter_periode);
    if (count($parts) == 2) {
        $semester = $parts[0];
        $tahun = $parts[1];

        if ($semester == 'Genap') {
            $start = "$tahun-01-01 00:00:00";
            $end = "$tahun-06-30 23:59:59";
        } else {
            $start = "$tahun-07-01 00:00:00";
            $end = "$tahun-12-31 23:59:59";
        }
        
        $filter_label = "($semester $tahun)";
        $peserta_where .= " AND created_at BETWEEN '$start' AND '$end'";
        $webinar_where .= " AND w.tanggal_direncanakan BETWEEN '$start' AND '$end'";
    }
}

// ---------------------------------------------------------
// 2. QUERY DATA
// ---------------------------------------------------------
// A. Ambil Daftar Peserta
$query_ta = "SELECT * FROM peserta $peserta_where ORDER BY nama_lengkap ASC";
$result_ta = mysqli_query($koneksi, $query_ta);

// B. Hitung Total Kehadiran
$query_total_hadir = "SELECT k.id_peserta, COUNT(k.id_webinar) AS total_hadir 
                      FROM kehadiran k
                      JOIN webinars w ON k.id_webinar = w.id
                      WHERE k.status_kehadiran = 'hadir' 
                      $webinar_where
                      GROUP BY k.id_peserta";
$result_total_hadir = mysqli_query($koneksi, $query_total_hadir);
$total_kehadiran = [];
if($result_total_hadir){
    while($row = mysqli_fetch_assoc($result_total_hadir)){
        $total_kehadiran[$row['id_peserta']] = $row['total_hadir'];
    }
}

// C. Ambil Setting Min IFWS
$query_settings = "SELECT setting_value FROM settings WHERE setting_key = 'min_ifws_ta'";
$res_set = mysqli_query($koneksi, $query_settings);
$min_ifws_ta = (int)(mysqli_fetch_assoc($res_set)['setting_value'] ?? 3);

// D. Generate Opsi Tahun
$query_years = "SELECT DISTINCT YEAR(created_at) as tahun FROM peserta WHERE status_ta != 'Bukan_TA' ORDER BY tahun DESC";
$res_years = mysqli_query($koneksi, $query_years);
$available_years = [];
while($r = mysqli_fetch_assoc($res_years)) { if($r['tahun']) $available_years[] = $r['tahun']; }
if(empty($available_years)) $available_years[] = date('Y');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0" /><title>Admin - Peserta TA</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="/projek-ifws/assets/css/style.css" />
    <link rel="stylesheet" href="/projek-ifws/assets/css/Sekretaris/sekretaris.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    
    <style>
        /* Terapkan Font Poppins */
        body { font-family: 'Poppins', sans-serif; }
        select, input, button { font-family: 'Poppins', sans-serif; }

        .filter-bar { display: flex; justify-content: space-between; align-items: center; background: #fff; padding: 15px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .filter-left { display: flex; align-items: center; gap: 10px; }
        
        /* Container untuk tombol di kanan */
        .right-actions { display: flex; gap: 10px; }

        .filter-select { 
            padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; outline: none; font-size: 14px; color: #333;
        }
        
        .btn-reset { text-decoration: none; color: #666; font-size: 14px; margin-left: 5px; font-weight: 500; }
        
        /* Tombol Import (Hijau) */
        .btn-import { 
            background-color: #10b981; color: white; padding: 9px 16px; border-radius: 6px; text-decoration: none; border: none; cursor: pointer; font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 8px; transition: background 0.3s;
        }
        .btn-import:hover { background-color: #059669; }

        /* Tombol Template (Kuning/Oranye) */
        .btn-template {
            background-color: #f59e0b; color: white; padding: 9px 16px; border-radius: 6px; text-decoration: none; border: none; cursor: pointer; font-size: 14px; font-weight: 500; display: flex; align-items: center; gap: 8px; transition: background 0.3s;
        }
        .btn-template:hover { background-color: #d97706; }
        
        .date-badge { font-size: 11px; color: #888; display: block; margin-top: 2px;}
        label { font-weight: 600; color: #444; font-size: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo-and-title"><img src="/projek-ifws/assets/picture/logo.png" alt="Logo" class="sidebar-logo"/><h2>Informatics<br /><span>Webinar Series</span></h2></div>
                <div class="admin-profile"><small>Admin</small></div>
            </div>
            <nav class="sidebar-nav">
                <div class="nav-section"><p class="section-title">DASHBOARD</p><ul><li><a href="admin.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li></ul></div>
                <div class="nav-section"><p class="section-title">KELOLA DATA IFWS</p><ul>
                    <li><a href="admin_listifws.php"><i class="fas fa-calendar-alt"></i><span>Data Webinar</span></a></li>
                    <li><a href="admin_riwayatifws.php"><i class="fas fa-history"></i><span>Riwayat Webinar</span></a></li>
                    <li><a href="admin_datanarsum.php"><i class="fas fa-user-friends"></i><span>Data Narasumber</span></a></li>
                    <li class="active"><a href="admin_pesertaTA.php"><i class="fas fa-user-friends"></i><span>Peserta Tugas Akhir</span></a></li>
                </ul></div>
                <div class="nav-section"><p class="section-title">KELOLA DATA ANGGOTA IFWS</p><ul><li><a href="admin_dataanggota.php"><i class="fas fa-users-cog"></i><span>Data Anggota IFWS</span></a></li></ul></div>
                <div class="nav-section"><p class="section-title">AKUN</p><ul><li><a href="/projek-ifws/logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li></ul></div>
            </nav>
        </aside>

        <main class="main-content">
            <div class="page-header"><h1>Peserta Tugas Akhir</h1></div>
            
            <?php if(isset($_GET['status']) && $_GET['status'] == 'import_sukses'): ?>
                <div class="alert alert-success">Berhasil mengimport <?= htmlspecialchars($_GET['count']) ?> data peserta!</div>
            <?php endif; ?>

            <div class="filter-bar">
                <form method="GET" action="" class="filter-left">
                    <label for="filter_periode"><i class="fas fa-filter"></i> Tahun Akademik:</label>
                    <select name="filter_periode" id="filter_periode" class="filter-select" onchange="this.form.submit()">
                        <option value="">-- Seluruh --</option>
                        <?php foreach ($available_years as $yr): ?>
                            <option value="Ganjil-<?= $yr ?>" <?= ($filter_periode == "Ganjil-$yr") ? 'selected' : '' ?>>Ganjil - <?= $yr ?> </option>
                            <option value="Genap-<?= $yr ?>" <?= ($filter_periode == "Genap-$yr") ? 'selected' : '' ?>>Genap - <?= $yr ?> </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if(!empty($filter_periode)): ?><a href="admin_pesertaTA.php" class="btn-reset"><i class="fas fa-times"></i> Reset</a><?php endif; ?>
                </form>

                <div class="right-actions">
                    <a href="/projek-ifws/api/download_template.php" class="btn-template">
                        <i class="fas fa-download"></i> Template CSV
                    </a>
                    <button class="btn-import" onclick="document.getElementById('import-overlay').classList.remove('hidden')">
                        <i class="fas fa-file-csv"></i> Import CSV
                    </button>
                </div>
            </div>

            <div class="content-card">
                <table>
                    <thead>
                        <tr>
                            <th>Nama / Tgl Daftar</th>
                            <th>NPM</th>
                            <th>Jenis TA</th>
                            <th>Total IFWS <?= htmlspecialchars($filter_label) ?></th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total_peserta = 0; $total_valid = 0;
                        if ($result_ta && mysqli_num_rows($result_ta) > 0): 
                            while ($peserta = mysqli_fetch_assoc($result_ta)): 
                                $total_peserta++;
                                $jumlah_hadir = $total_kehadiran[$peserta['id']] ?? 0;
                                $isValid = $jumlah_hadir >= $min_ifws_ta;
                                if ($isValid) $total_valid++;
                                $tgl_daftar = isset($peserta['created_at']) ? date('d/m/Y', strtotime($peserta['created_at'])) : '-';
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($peserta['nama_lengkap']) ?><span class="date-badge">Daftar: <?= $tgl_daftar ?></span></td>
                            <td><?= htmlspecialchars($peserta['npm'] ?? '-') ?></td>
                            <td>
                                <span class="ta-badge <?= ($peserta['status_ta'] == 'TA_1') ? 'ta-1' : 'ta-2' ?>">
                                    <?= str_replace('_', ' ', $peserta['status_ta']) ?>
                                </span>
                            </td>
                            <td><strong><?= $jumlah_hadir ?></strong></td>
                            <td>
                                <?php if ($isValid): ?><span class="status-badge status-valid">Valid</span>
                                <?php else: ?><span class="status-badge status-invalid">Tidak Valid</span><?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr><td colspan="5" style="text-align: center;">Tidak ada peserta yang didaftarkan pada periode ini.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                 <div class="table-summary-footer">
                    <p>Total Peserta: <?= $total_peserta ?></p>
                    <p>Total Valid: <?= $total_valid ?></p>
                 </div>
            </div>
        </main>
    </div>

    <div id="import-overlay" class="overlay hidden">
        <div class="overlay-content small">
            <div class="overlay-header">
                <h3 class="overlay-main-title">Import Data Peserta (CSV)</h3>
                <button class="btn-close btn-close-overlay" onclick="document.getElementById('import-overlay').classList.add('hidden')">&times;</button>
            </div>
            <div class="overlay-body">
                <form action="/projek-ifws/api/import_peserta.php" method="POST" enctype="multipart/form-data">
                    <p style="font-size:13px; color:#666; margin-bottom:15px;">
                        Silahkan download <strong>Template CSV</strong> terlebih dahulu.<br>
                        Format: <strong>Nama Lengkap, NPM, Tipe TA (TA1/TA2)</strong>.<br>
                        <em style="color:#d9534f;">*Jika NPM sudah ada, data akan diperbarui ke Tahun Ajaran saat ini.</em>
                    </p>
                    <div class="form-group">
                        <input type="file" name="file_csv" accept=".csv" required style="border:1px solid #ddd; padding:10px; width:100%; border-radius:6px;">
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-success">Import Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>