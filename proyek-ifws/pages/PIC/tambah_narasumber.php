<?php include '../../includes/auth_check.php'; ?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Narasumber</title>
    <link rel="stylesheet" href="/proyek-ifws/assets/css/PIC/form_narasumber_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
    <div class="app-layout">
        
        <?php include '../../includes/sidebar.php'; // Memanggil sidebar ?>

        <main class="main-content">
            <h1>Tambah Data Narasumber</h1>
            <div class="form-container">
                <form id="tambah-narasumber-form">
                    <div class="form-group">
                        <label for="nama">Narasumber</label>
                        <input type="text" id="nama" name="nama" required placeholder="Masukkan nama lengkap...">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required placeholder="Masukkan alamat email...">
                    </div>
                    <div class="form-actions">
                        <a href="list_narasumber.php" class="btn btn-kembali">Kembali</a>
                        <button type="submit" class="btn btn-simpan">Simpan</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
    <script src="/proyek-ifws/assets/js/PIC/tambah_narasumber_script.js"></script>
</body>
</html>