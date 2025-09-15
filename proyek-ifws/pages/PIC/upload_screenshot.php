<?php include '../../includes/auth_check.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Screenshot</title>
    <link rel="stylesheet" href="/proyek-ifws/assets/css/PIC/upload_screenshot.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
    <div class="app-layout">
        
        <?php include '../../includes/sidebar.php'; ?>

        <main class="main-content">
            <h1>Upload Screenshot zoom</h1>

            <div id="webinar-details" class="webinar-details">
                <h2 id="webinar-topic">Memuat Detail Webinar...</h2>
                <p id="webinar-speaker"></p>
            </div>

            <h3 class="section-title">Screenshots</h3>
            
            <form id="upload-form" enctype="multipart/form-data">
                <div class="screenshot-area">
                    <div class="screenshot-actions">
                        <button type="button" id="upload-button" class="btn btn-outline">Pilih Gambar</button>
                        <input type="file" id="file-input" name="screenshots[]" multiple accept="image/*" style="display: none;">
                    </div>
                    <div id="previews-container" class="previews-container">
                        </div>
                </div>

                <div class="page-actions">
                    <a href="riwayat.php" class="btn btn-kembali">Kembali</a>
                    <button type="submit" class="btn btn-simpan">Simpan Screenshot</button>
                </div>
            </form>

        </main>
    </div>
    <script src="/proyek-ifws/assets/js/PIC/upload_screenshot_script.js"></script>
</body>
</html>