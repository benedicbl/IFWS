<?php include '../includes/auth_check.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Webinar</title>
    <link rel="stylesheet" href="../assets/css/tambah_webinar_style.css">
    <link rel="stylesheet" href="../assets/css/pilih_peserta.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>.hidden { display: none !important; }</style>
</head>
<body>
    <div class="app-layout">
        <?php include '../includes/sidebar.php'; ?>
        <main class="main-content">
            <div id="form-webinar-wrapper">
                <h1>Edit Data Webinar</h1>
                <div class="form-container">
                    <form id="webinar-form">
                        <input type="hidden" id="webinarId" name="webinarId">
                        <div class="form-group"><label for="tanggal">Tanggal</label><input type="date" id="tanggal" name="tanggal" required></div>
                        <div class="form-group"><label for="jenis-ifws">Jenis IFWS</label><select id="jenis-ifws" name="id_jenis_ifws" required><option value="">Memuat...</option></select></div>
                        <div class="time-container">
                            <div class="form-group"><label for="jam-mulai">Jam Mulai</label><input type="time" id="jam-mulai" name="jam-mulai" required></div>
                            <div class="form-group"><label for="jam-selesai">Jam Selesai</label><input type="time" id="jam-selesai" name="jam-selesai" required></div>
                        </div>
                        <div class="form-group"><label for="topik">Topik Webinar</label><textarea id="topik" name="topik" rows="4" required></textarea></div>
                        <div class="page-actions">
                            <a href="list_IFWS.php" class="btn btn-kembali">Kembali ke List</a>
                            <button type="button" id="edit-peserta-btn" class="btn btn-tambah-inline">Edit Peserta</button>
                            <button type="submit" class="btn btn-simpan">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>

            <div id="pilih-peserta-wrapper" class="hidden">
                <h1>Edit Peserta untuk Webinar</h1>
                <p id="judul-webinar-terpilih" style="color: #555; margin-top: -1.5rem; margin-bottom: 2rem; font-weight: 500;"></p>
                <section class="selection-section"><header class="section-header"><h2>Narasumber</h2><button id="add-narasumber-btn" class="btn btn-add">+ Tambah</button></header><div id="narasumber-list" class="list-container"></div></section>
                <section class="selection-section"><header class="section-header"><h2>Panitia</h2><button id="add-panitia-btn" class="btn btn-add">+ Tambah</button></header><div class="table-wrapper"><table class="panitia-table"><thead><tr><th>Panitia</th><th>Peran</th><th>Aksi</th></tr></thead><tbody id="panitia-list-body"></tbody></table></div></section>
                <div class="page-actions">
                    <button type="button" id="kembali-ke-form-utama-btn" class="btn btn-kembali">Kembali ke Detail</button>
                    <button id="save-peserta-btn" class="btn btn-simpan">Simpan Perubahan Peserta</button>
                </div>
            </div>
        </main>
    </div>

    <div id="selection-modal" class="modal-overlay hidden"><div class="modal-content"><h3 id="modal-title"></h3><input type="text" id="modal-search" placeholder="Cari nama..."><div id="modal-list" class="modal-list"></div><div id="role-input-container" class="form-group hidden"><label for="panitia-role">Peran Panitia</label><input type="text" id="panitia-role" placeholder="Contoh: Moderator"></div><div class="modal-actions"><button id="close-modal-btn" class="btn btn-kembali">Tutup</button></div></div></div>
    
    <script src="../assets/js/edit_webinar_script.js"></script>
</body>
</html>