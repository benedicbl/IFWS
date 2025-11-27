<?php
require_once '../includes/config.php';

// Validasi Role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { 
    header('Location: /projek-ifws/login.php'); 
    exit(); 
}

// ---------------------------------------------------------
// 1. LOGIKA FILTER TAHUN AJARAN
// ---------------------------------------------------------

// Ambil tahun-tahun yang tersedia di database (dari yang sudah finished)
$query_years = "SELECT DISTINCT YEAR(tanggal_direncanakan) as tahun 
                FROM webinars 
                WHERE status = 'finished' 
                ORDER BY tahun DESC";
$result_years = mysqli_query($koneksi, $query_years);
$available_years = [];
if ($result_years) {
    while ($row = mysqli_fetch_assoc($result_years)) {
        $available_years[] = $row['tahun'];
    }
}

// Proses Filter jika ada Input
$filter_periode = isset($_GET['filter_periode']) ? $_GET['filter_periode'] : '';
$where_clause = "WHERE w.status = 'finished'"; // Default condition

if (!empty($filter_periode)) {
    // Format value: "Genap-2025" atau "Ganjil-2025"
    $parts = explode('-', $filter_periode);
    if (count($parts) == 2) {
        $semester = $parts[0]; // Genap atau Ganjil
        $tahun = $parts[1];    // 2025, 2024, dll

        if ($semester == 'Genap') {
            // Genap: Januari (01) s/d Juni (06)
            $start_date = "$tahun-01-01";
            $end_date = "$tahun-06-30";
        } else {
            // Ganjil: Juli (07) s/d Desember (12)
            $start_date = "$tahun-07-01";
            $end_date = "$tahun-12-31";
        }

        $where_clause .= " AND w.tanggal_direncanakan BETWEEN '$start_date' AND '$end_date'";
    }
}

// ---------------------------------------------------------
// 2. QUERY UTAMA (Dengan Filter)
// ---------------------------------------------------------
$query_webinars_finished = "SELECT w.*, GROUP_CONCAT(DISTINCT n.nama SEPARATOR ', ') AS daftar_narasumber 
                            FROM webinars w 
                            LEFT JOIN webinar_narasumber wn ON w.id = wn.id_webinar 
                            LEFT JOIN narasumber n ON wn.id_narasumber = n.id 
                            $where_clause 
                            GROUP BY w.id 
                            ORDER BY w.tanggal_direncanakan DESC";

$result_webinars_finished = mysqli_query($koneksi, $query_webinars_finished);

// Ambil foto-foto
$query_fotos = "SELECT * FROM foto_pelaksanaan";
$result_fotos = mysqli_query($koneksi, $query_fotos);
$fotos_by_webinar = [];
if ($result_fotos) { 
    while ($foto = mysqli_fetch_assoc($result_fotos)) { 
        $fotos_by_webinar[$foto['id_webinar']][] = ['id' => $foto['id'], 'path' => $foto['file_path']]; 
    } 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin - Riwayat Webinar</title>
    <link rel="stylesheet" href="/projek-ifws/assets/css/style.css" />
    <link rel="stylesheet" href="/projek-ifws/assets/css/PIC/admin_riwayatifws.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>
    <style>
        /* Style Tambahan untuk Filter */
        .filter-container {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        .filter-container label { font-weight: 600; color: #555; }
        .filter-select {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            outline: none;
            font-family: inherit;
        }
        .btn-reset {
            text-decoration: none;
            color: #666;
            font-size: 14px;
            margin-left: 5px;
        }
        .btn-reset:hover { color: #d9534f; }
    </style>
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo-and-title">
                    <img src="/projek-ifws/assets/picture/logo.png" alt="Logo Informatics" class="sidebar-logo"/>
                    <h2>Informatics<br /><span>Webinar Series</span></h2>
                </div>
                <div class="admin-profile"><small>Admin</small></div>
            </div>
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <p class="section-title">DASHBOARD</p>
                    <ul><li><a href="admin.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li></ul>
                </div>
                <div class="nav-section">
                    <p class="section-title">KELOLA DATA IFWS</p>
                    <ul>
                        <li><a href="admin_listifws.php"><i class="fas fa-calendar-alt"></i><span>Data Webinar</span></a></li>
                        <li class="active"><a href="admin_riwayatifws.php"><i class="fas fa-history"></i><span>Riwayat Webinar</span></a></li>
                        <li><a href="admin_datanarsum.php"><i class="fas fa-user-friends"></i><span>Data Narasumber</span></a></li>
                        <li><a href="/projek-ifws/PIC/admin_pesertaTA.php"><i class="fas fa-user-friends"></i><span>Peserta Tugas Akhir</span></a></li>
                    </ul>
                </div>
                <div class="nav-section">
                    <p class="section-title">KELOLA DATA ANGGOTA IFWS</p>
                    <ul><li><a href="admin_dataanggota.php"><i class="fas fa-users-cog"></i><span>Data Anggota IFWS</span></a></li></ul>
                </div>
                <div class="nav-section">
                    <p class="section-title">AKUN</p>
                    <ul><li><a href="/projek-ifws/logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li></ul>
                </div>
            </nav>
        </aside>

        <main class="main-content">
            <div class="page-header"><h1>Riwayat Pelaksanaan Webinar</h1></div>
            
            <?php if(isset($_GET['status']) && $_GET['status'] == 'upload_sukses'): ?>
                <div class="alert alert-success">Foto berhasil diupload!</div>
            <?php endif; ?>

            <form method="GET" action="" class="filter-container">
                <label for="filter_periode"><i class="fas fa-filter"></i> Tahun Akademik:</label>
                <select name="filter_periode" id="filter_periode" class="filter-select" onchange="this.form.submit()">
                    <option value="">-- Tampilkan Semua --</option>
                    <?php foreach ($available_years as $yr): ?>
                        <option value="Ganjil-<?= $yr ?>" <?= ($filter_periode == "Ganjil-$yr") ? 'selected' : '' ?>>
                            Ganjil - <?= $yr ?>
                        </option>
                        <option value="Genap-<?= $yr ?>" <?= ($filter_periode == "Genap-$yr") ? 'selected' : '' ?>>
                            Genap - <?= $yr ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if(!empty($filter_periode)): ?>
                    <a href="admin_riwayatifws.php" class="btn-reset"><i class="fas fa-times"></i> Reset Filter</a>
                <?php endif; ?>
            </form>

            <div class="content-card">
                <table>
                    <thead><tr><th>Narasumber</th><th>Tanggal</th><th>Topik</th><th>Foto Pelaksanaan</th></tr></thead>
                    <tbody>
                        <?php if($result_webinars_finished && mysqli_num_rows($result_webinars_finished) > 0): 
                            while ($row = mysqli_fetch_assoc($result_webinars_finished)): 
                                $adaFoto = isset($fotos_by_webinar[$row['id']]); 
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($row['daftar_narasumber'] ?? 'N/A') ?></td>
                                <td><?= date('d/m/Y', strtotime($row['tanggal_direncanakan'])) ?></td>
                                <td><?= htmlspecialchars($row['topik']) ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <input type="file" id="file-input-<?= $row['id'] ?>" class="file-input-hidden" data-id="<?= $row['id'] ?>" multiple accept="image/jpeg, image/png">
                                        <button class="btn btn-upload btn-open-initial-upload" data-id="<?= $row['id'] ?>"><i class="fas fa-upload"></i> Upload</button>
                                        <button class="btn-icon-only btn-view-photos <?= $adaFoto ? 'has-photos' : '' ?>" data-webinar-id="<?= $row['id'] ?>" data-images='<?= $adaFoto ? htmlspecialchars(json_encode($fotos_by_webinar[$row['id']])) : '[]' ?>'><i class="fas fa-eye"></i></button>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; else: ?>
                            <tr><td colspan="4" style="text-align: center;">Tidak ada riwayat webinar yang ditemukan untuk periode ini.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <div id="initial-upload-overlay" class="overlay hidden">
        <div class="overlay-content large">
            <div class="overlay-header"><h3 class="overlay-main-title">Upload Foto Pelaksanaan</h3><button class="btn-close btn-close-overlay">&times;</button></div>
            <div class="overlay-body">
                <p class="upload-notification"><i class="fas fa-info-circle"></i> Anda dapat menambah foto secara bertahap.</p>
                <input type="file" id="initial-file-input" class="file-input-hidden" multiple accept="image/jpeg, image/png">
                <input type="hidden" id="initial_webinar_id">
                <div id="initial-image-preview-container"><p class="no-images-selected">Belum ada gambar yang dipilih.</p></div>
            </div>
            <div class="overlay-footer initial-footer">
                 <button type="button" class="btn btn-secondary btn-close-overlay">Batal</button>
                 <button type="button" id="btn-trigger-file-input" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Foto</button>
                 <button type="button" id="btn-lanjutkan-upload" class="btn btn-success hidden"><i class="fas fa-check"></i> Upload Sekarang</button>
            </div>
        </div>
    </div>

    <div id="view-slider-overlay" class="overlay hidden"><div class="overlay-content large slider-content"><button class="btn-close-slider btn-close-overlay">&times;</button><button class="slider-nav" id="slider-prev">&lt;</button><div class="slider-image-container"><img src="" id="slider-image" alt="Foto Pelaksanaan"><button class="btn-icon-only btn-danger btn-delete-photo" id="btn-delete-current-photo" title="Hapus foto ini"><i class="fas fa-trash"></i></button></div><button class="slider-nav" id="slider-next">&gt;</button></div></div>

    <script src="/projek-ifws/assets/scripts/PIC/admin_riwayatifws.js"></script>
</body>
</html>