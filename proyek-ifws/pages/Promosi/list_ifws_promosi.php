<?php include '../../includes/auth_check.php'; ?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Upload Poster IFWS</title>
    <link rel="stylesheet" href="/proyek-ifws/assets/css/Promosi/list_ifws_promosi.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
    <div class="app-layout">
        <?php include '../../includes/sidebar.php'; // Memanggil sidebar ?>
        <main class="main-content">
            <header class="main-header">
                <h1>Upload Poster IFWS</h1>
                <div class="dropdown">
                    <button id="pilihTahunBtn" class="dropdown-btn">Pilih Tahun Akademik <i class="fa-solid fa-chevron-down"></i></button>
                    <div id="tahunDropdown" class="dropdown-content"></div>
                </div>
            </header>

            <div id="initial-message" class="initial-message">
                <p>Silakan pilih tahun akademik untuk menampilkan data.</p>
            </div>

            <div id="data-section" class="table-container hidden">
                <table>
                    <thead>
                        <tr>
                            <th>Topik Webinar</th>
                            <th>Tanggal</th>
                            <th>Status Poster</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="webinar-table-body"></tbody>
                </table>
            </div>
        </main>
    </div>

    <div id="poster-modal" class="modal-overlay hidden">
        <div class="modal-content">
            <h3>Upload Poster</h3>
            <p id="modal-webinar-title"></p>
            <div class="form-group">
                <label for="poster-file-input">Pilih file gambar (JPG, PNG):</label>
                <input type="file" id="poster-file-input" accept="image/jpeg, image/png">
            </div>
            <div class="modal-actions">
                <button id="cancel-modal-btn" class="btn btn-kembali">Batal</button>
                <button id="save-poster-btn" class="btn btn-simpan">Simpan</button>
            </div>
        </div>
    </div>

    <div id="image-viewer-modal" class="modal-overlay hidden">
        <span class="close-modal-btn">&times;</span>
        <img class="modal-content-image" id="full-poster-image">
    </div>
    <script src="/proyek-ifws/assets/js/Promosi/list_ifws_promosi_script.js"></script>
</body>
</html>