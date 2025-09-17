<?php include '../../includes/auth_check.php'; ?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Peserta Webinar</title>
    <link rel="stylesheet" href="/proyek-ifws/assets/css/Sekretariat/rekap_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
    <div class="app-layout">
        <?php include '../../includes/sidebar.php'; // Memanggil sidebar ?>
        <main class="main-content">
            <header class="main-header">
                <h1>Rekap Peserta Webinar</h1>
                <button id="import-csv-btn" class="btn btn-import"><i class="fa-solid fa-upload"></i> Import CSV</button>
                <input type="file" id="csv-file-input" accept=".csv" style="display: none;">
            </header>

            <div class="webinar-info-header">
                <p id="info-narasumber">Memuat data webinar...</p>
                <p id="info-topik"></p>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Nama Peserta</th>
                            <th>Email</th>
                            <th>Total Duration</th>
                            <th>Jenis</th>
                            <th>Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody id="peserta-table-body">
                        </tbody>
                </table>
                <div id="empty-state-message" class="empty-state">
                    <p>Belum ada data. Silakan impor file CSV dari Zoom untuk memulai.</p>
                </div>
            </div>

            <div class="page-actions">
                <button id="save-btn" class="btn btn-simpan">Simpan</button>
            </div>
        </main>
    </div>
    <script src="/proyek-ifws/assets/js/Sekretariat/rekap_peserta_script.js"></script>
</body>
</html>