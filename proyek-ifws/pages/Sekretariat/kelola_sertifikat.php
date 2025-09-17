<?php include '../../includes/auth_check.php'; ?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Sertifikat</title>
    <link rel="stylesheet" href="/proyek-ifws/assets/css/Sekretariat/kelola_sertifikat.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="page-sertifikat">
    <div class="app-layout">
        <?php include '../../includes/sidebar.php'; // Memanggil sidebar ?>
        <main class="main-content">
            <div class="content-wrapper">
                <h1>Kelola Sertifikat</h1>
                <div class="webinar-info-header">
                    <p id="info-narasumber">Memuat...</p>
                    <p id="info-topik"></p>
                </div>

                <div class="template-section">
                    <div class="template-box">
                        <label>Template Sertifikat Narasumber</label>
                        <button id="upload-narsum-btn" class="btn btn-outline">Upload</button>
                        <span id="narsum-file-name" class="file-name"></span>
                        <input type="file" id="narsum-file-input" class="hidden-input">
                    </div>
                    <div class="template-box">
                        <label>Template Sertifikat Peserta</label>
                        <button id="upload-peserta-btn" class="btn btn-outline">Upload</button>
                        <span id="peserta-file-name" class="file-name"></span>
                        <input type="file" id="peserta-file-input" class="hidden-input">
                    </div>
                </div>

                <h3 class="table-title">Narasumber</h3>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr><th>Nama Narasumber</th><th>Email</th><th>Sertifikat</th></tr>
                        </thead>
                        <tbody id="narasumber-table-body"></tbody>
                    </table>
                </div>

                <h3 class="table-title">Peserta</h3>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr><th>Nama Peserta</th><th>Email</th><th>Status Kehadiran</th><th>Sertifikat</th></tr>
                        </thead>
                        <tbody id="peserta-table-body"></tbody>
                    </table>
                </div>
            </div>

            <div class="page-actions">
                <button id="generate-btn" class="btn btn-secondary">Generate Sertifikat</button>
                <button id="send-btn" class="btn btn-secondary" disabled>Kirim Email</button>
                <a href="sertifikat.php" id="save-btn" class="btn btn-simpan">Simpan & Kembali</a>
            </div>
        </main>
    </div>
    <script src="/proyek-ifws/assets/js/Sekretariat/kelola_sertifikat_script.js"></script>
</body>
</html>