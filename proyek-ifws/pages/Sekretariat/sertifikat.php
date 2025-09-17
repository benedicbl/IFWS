<?php include '../../includes/auth_check.php'; ?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Sertifikat</title>
    <link rel="stylesheet" href="/proyek-ifws/assets/css/Sekretariat/sertifikat.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="page-sertifikat"> 
    <div class="app-layout">
        <?php include '../../includes/sidebar.php'; // Memanggil sidebar ?>
        <main class="main-content">
            <header class="main-header">
                <h1>Manajemen Sertifikat</h1>
                <div class="dropdown">
                    <button id="pilihTahunBtn" class="dropdown-btn">Pilih Tahun Akademik <i class="fa-solid fa-chevron-down"></i></button>
                    <div id="tahunDropdown" class="dropdown-content">
                        <a href="#" data-tahun="2025-Ganjil">2025-Ganjil</a>
                        <a href="#" data-tahun="2024-Genap">2024-Genap</a>
                    </div>
                </div>
            </header>

            <div id="initial-message" class="initial-message">
                <p>Silakan pilih tahun akademik untuk menampilkan daftar webinar.</p>
            </div>

            <div id="webinar-section" class="hidden">
                <div class="webinar-header">
                    <h2 id="tahunAkademikTitle"></h2>
                </div>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Narasumber</th>
                                <th>Tanggal</th>
                                <th>Jenis IFWS</th>
                                <th>Topik Webinar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="webinar-table-body"></tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
    <script src="/proyek-ifws/assets/js/Sekretariat/sertifikat_script.js"></script>
    <script src="sidebar.js"></script>
</body>
</html>