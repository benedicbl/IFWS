<?php include '../../includes/auth_check.php'; ?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List IFWS - Teknisi</title>
    <link rel="stylesheet" href="/proyek-ifws/assets/css/Teknisi/list_ifws_teknisi.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="page-list-ifws">
    <div class="app-layout">
        <?php include '../../includes/sidebar.php'; // Memanggil sidebar ?>
        <main class="main-content">
            <header class="main-header">
                <h1>List IFWS & Link Zoom</h1>
                <div class="dropdown">
                    <button id="pilihTahunBtn" class="dropdown-btn">Pilih Tahun Akademik <i class="fa-solid fa-chevron-down"></i></button>
                    <div id="tahunDropdown" class="dropdown-content"></div>
                </div>
            </header>
            <div id="data-section" class="table-container">                <table>
            <table>        
                <thead>
                    <tr>
                        <th>Topik Webinar</th>
                        <th>Narasumber</th>
                        <th>Tanggal</th>
                        <th>Link Zoom</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="webinar-table-body">
                    <tr>
                        <td colspan="5" style="text-align:center;">Silakan pilih tahun akademik untuk menampilkan data.</td>
                    </tr>
                </tbody>
                </table>
            </div>
        </main>
    </div>

    <div id="zoom-link-modal" class="modal-overlay hidden">
        <div class="modal-content">
            <h3>Update Link Zoom</h3>
            <p id="modal-webinar-title"></p>
            <div class="form-group">
                <label for="zoom-link-input">Link Zoom</label>
                <input type="text" id="zoom-link-input" placeholder="https://zoom.us/j/...">
            </div>
            <div class="modal-actions">
                <button id="cancel-modal-btn" class="btn btn-kembali">Batal</button>
                <button id="save-link-btn" class="btn btn-simpan">Simpan</button>
            </div>
        </div>
    </div>
    <script src="/proyek-ifws/assets/js/Teknisi/list_ifws_teknisi_script.js"></script>
    
</body>
</html>