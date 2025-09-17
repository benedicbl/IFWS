<?php include '../../includes/auth_check.php'; ?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Rekap Peserta</title>
    <link rel="stylesheet" href="/proyek-ifws/assets/css/Sekretariat/rekap_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="page-list-ifws-sekretariat">
    <div class="app-layout">
        <?php include '../../includes/sidebar.php'; // Memanggil sidebar ?>
        <main class="main-content">
            <header class="main-header">
                <h1>Edit Rekap Webinar</h1>
            </header>
            <div class="webinar-info-header">
                <p id="info-narasumber">Memuat...</p>
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
                            <th>Aksi</th> </tr>
                    </thead>
                    <tbody id="peserta-table-body"></tbody>
                </table>
            </div>
            <div class="page-actions">
                <a id="kembali-btn" href="#" class="btn btn-kembali">Kembali</a>
                <button id="save-btn" class="btn btn-simpan">Simpan</button>
            </div>
        </main>
    </div>
    <script src="/proyek-ifws/assets/js/Sekretariat/edit_rekap.js"></script>
</body>
</html>