<?php include '../../includes/auth_check.php'; ?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peserta Tugas Akhir</title>
    <link rel="stylesheet" href="/proyek-ifws/assets/css/Sekretariat/peserta_ta.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="page-peserta-ta">
    <div class="app-layout">
        <?php include '../../includes/sidebar.php'; // Memanggil sidebar ?>
        <main class="main-content">
            <header class="main-header">
                <h1>Peserta Tugas Akhir</h1>
                <div class="header-actions">
                    <div class="dropdown">
                        <button id="pilihTahunBtn" class="dropdown-btn">Pilih Tahun Akademik <i class="fa-solid fa-chevron-down"></i></button>
                        <div id="tahunDropdown" class="dropdown-content">
                            <a href="#" data-tahun="2025-Ganjil">2025-Ganjil</a>
                            <a href="#" data-tahun="2024-Genap">2024-Genap</a>
                        </div>
                    </div>
                    <a href="upload_peserta_ta.php" class="btn btn-outline"><i class="fa-solid fa-upload"></i> Upload CSV</a>
                    <button id="download-csv-btn" class="btn btn-primary"><i class="fa-solid fa-download"></i> Download CSV</button>
                </div>
            </header>

            <div id="data-section" class="hidden">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>NPM</th>
                                <th>Jenis TA</th>
                                <th>Total IFWS</th>
                                <th>Valid</th>
                            </tr>
                        </thead>
                        <tbody id="peserta-ta-table-body"></tbody>
                    </table>
                </div>
                <div id="summary-info" class="summary-info"></div>
            </div>

            <div id="empty-state-message" class="initial-message">
                <p>Silakan pilih tahun akademik untuk menampilkan data.</p>
            </div>
        </main>
    </div>
    <script src="/proyek-ifws/assets/js/Sekretariat/peserta_ta_script.js"></script>
</body>
</html>