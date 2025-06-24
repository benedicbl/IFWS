<?php include '../includes/auth_check.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Anggota</title>
    <link rel="stylesheet" href="../assets/css/form_anggota_style.css">
</head>
<body>
    <div class="app-layout">

        <?php include '../includes/sidebar.php'; ?>

        <main class="main-content">
            <h1>Edit Data Anggota</h1>
            <div class="form-container">
                <form id="form-anggota">
                    <input type="hidden" id="anggotaId" name="anggotaId">

                    <div class="form-group">
                        <label for="nama">Nama Anggota</label>
                        <input type="text" id="nama" name="nama" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="jabatan">Jabatan</label>
                        <select id="jabatan" name="jabatan" required></select>
                    </div>
                    <div class="form-actions">
                        <a href="list_anggota.php" class="btn btn-kembali">Kembali</a>
                        <button type="submit" class="btn btn-simpan">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
    <script src="../assets/js/edit_anggota_script.js"></script>
</body>
</html>