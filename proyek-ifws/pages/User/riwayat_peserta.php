<?php include '../../includes/auth_check.php'; ?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat IFWS</title>
    <link rel="stylesheet" href="/proyek-ifws/assets/css/User/riwayat_peserta_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
    <div class="app-layout">
        <?php include '../../includes/sidebar.php'; // Memanggil sidebar ?>
        <main class="main-content">
            <div id="info-ta-section" class="info-ta hidden">
                <div class="info-ta-header">
                    <h2>Peserta Tugas Akhir</h2>
                </div>
                <div class="info-ta-content">
                    <div class="info-item">
                        <span>Nama Peserta</span>
                        <p id="nama-peserta"></p>
                    </div>
                    <div class="info-item">
                        <span>NPM</span>
                        <p id="npm-peserta"></p>
                    </div>
                    <div class="info-item">
                        <span>Total IFWS</span>
                        <p id="total-ifws"></p>
                    </div>
                </div>
            </div>

            <div class="riwayat-container">
                <div class="riwayat-header">
                    <h2>Riwayat IFWS</h2>
                    <select id="filter-tahun"></select>
                </div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Status Kehadiran</th>
                                <th>Topik Webinar</th>
                                <th>Sertifikat</th>
                            </tr>
                        </thead>
                        <tbody id="riwayat-table-body">
                            </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
    <script src="/proyek-ifws/assets/js/User/riwayat_peserta_script.js"></script>
</body>
</html>