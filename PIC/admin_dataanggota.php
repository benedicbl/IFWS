<?php
require_once '../includes/config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /projek-ifws/login.php');
    exit();
}

$query_anggota = "SELECT * FROM anggota_ifws WHERE role != 'admin' ORDER BY role ASC";
$result_anggota = mysqli_query($koneksi, $query_anggota);

$form_error_tambah = $_SESSION['form_error'] ?? null; 
$form_data_tambah = $_SESSION['form_data'] ?? null;
$form_error_edit = $_SESSION['form_error_edit'] ?? null; 
$form_data_edit = $_SESSION['form_data_edit'] ?? null;
$edit_error_id = isset($_GET['edit_error']) ? (int)$_GET['id'] : null;

unset($_SESSION['form_error'], $_SESSION['form_data'], $_SESSION['form_error_edit'], $_SESSION['form_data_edit']); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin - Data Anggota</title>
    <link rel="stylesheet" href="/projek-ifws/assets/css/style.css" />
    <link rel="stylesheet" href="/projek-ifws/assets/css/PIC/admin_dataanggota.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo-and-title">
                    <img src="/projek-ifws/assets/picture/logo.png" alt="Logo Informatics" class="sidebar-logo"/>
                    <h2>Informatics<br /><span>Webinar Series</span></h2>
                </div>
                <div class="admin-profile"><small>Admin</small></div>
            </div>
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <p class="section-title">DASHBOARD</p>
                    <ul><li><a href="admin.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li></ul>
                </div>
                
                <div class="nav-section">
                    <p class="section-title">KELOLA DATA IFWS</p>
                    <ul>
                        <li><a href="admin_listifws.php"><i class="fas fa-calendar-alt"></i><span>Data Webinar</span></a></li>
                        <li><a href="admin_riwayatifws.php"><i class="fas fa-history"></i><span>Riwayat Webinar</span></a></li>
                        <li><a href="admin_datanarsum.php"><i class="fas fa-user-friends"></i><span>Data Narasumber</span></a></li>
                        <li><a href="admin_pesertaTA.php"><i class="fas fa-user-friends"></i><span>Peserta Tugas Akhir</span></a></li>
                    </ul>
                </div>

                <div class="nav-section">
                    <p class="section-title">KELOLA DATA ANGGOTA IFWS</p>
                    <ul><li class="active"><a href="admin_dataanggota.php"><i class="fas fa-users-cog"></i><span>Data Anggota IFWS</span></a></li></ul>
                </div>
                <div class="nav-section">
                    <p class="section-title">AKUN</p>
                    <ul><li><a href="/projek-ifws/logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li></ul>
                </div>
            </nav>
        </aside>
        <main class="main-content">
            <div class="page-header">
                <h1>Data Anggota IFWS</h1>
                <button id="btn-tambah-anggota" class="btn btn-success"><i class="fas fa-plus"></i> Tambah Anggota</button>
            </div>

            <?php if(isset($_GET['status']) && $_GET['status'] == 'tambah_sukses'): ?>
                <div class="alert alert-success">Anggota baru berhasil ditambahkan!</div>
            <?php endif; ?>
            
            <?php if(isset($_GET['status']) && $_GET['status'] == 'update_sukses'): ?>
                <div class="alert alert-success">Data anggota berhasil diperbarui!</div>
            <?php endif; ?>

            <?php if(isset($_GET['status']) && $_GET['status'] == 'hapus_sukses'): ?>
                <div class="alert alert-success">Data anggota berhasil dihapus!</div>
            <?php endif; ?>
            <?php if(isset($_GET['status']) && $_GET['status'] == 'hapus_gagal'): ?>
                <div class="alert alert-danger">Gagal menghapus data anggota.</div>
            <?php endif; ?>

            <div class="page-section">
                <h2>List Anggota IFWS</h2>
                <div class="content-card">
                    <table>
                        <thead><tr><th>Nama Anggota</th><th>Email</th><th>Role</th><th>Aksi</th></tr></thead>
                        <tbody>
                            <?php if ($result_anggota && mysqli_num_rows($result_anggota) > 0): 
                                mysqli_data_seek($result_anggota, 0); 
                                while ($row = mysqli_fetch_assoc($result_anggota)): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><?= strtoupper(htmlspecialchars($row['role'])) ?></td>
                                <td>
                                    <button class="btn btn-icon-only btn-warning btn-edit-anggota"
                                            data-id="<?= $row['id'] ?>"
                                            data-nama="<?= htmlspecialchars($row['nama_lengkap']) ?>"
                                            data-email="<?= htmlspecialchars($row['email']) ?>"
                                            data-role="<?= htmlspecialchars($row['role']) ?>">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>
                                    
                                    <a href="/projek-ifws/api/delete_anggota.php?id=<?= $row['id'] ?>" 
                                       class="btn btn-icon-only btn-danger"
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus anggota ini? Data yang dihapus tidak dapat dikembalikan.');">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr><td colspan="4" style="text-align: center;">Tidak ada data anggota.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <div id="tambah-anggota-overlay" class="overlay hidden">
        <div class="overlay-content">
            <div class="overlay-header">
                <h3 class="overlay-main-title">Tambah Anggota Baru</h3>
                <button class="btn-close btn-close-overlay">&times;</button>
            </div>
            <div class="overlay-body">
                <form action="/projek-ifws/api/tambah_anggota.php" method="POST">
                    <div id="tambah-form-error" class="form-error <?= !$form_error_tambah ? 'hidden' : '' ?>"><?= htmlspecialchars($form_error_tambah ?? '') ?></div>
                    <div class="form-group"><label for="nama_lengkap">Nama Lengkap</label><input type="text" id="nama_lengkap" name="nama_lengkap" required></div>
                    <div class="form-group"><label for="role">Role</label><select id="role" name="role" required><option value="" disabled selected>Pilih Role</option><option value="sekretaris">Sekretaris</option><option value="bendahara">Bendahara</option><option value="promosi">Promosi</option><option value="teknisi">Teknisi</option></select></div>
                    <div class="form-group"><label for="email">Alamat Email</label><input type="email" id="email" name="email" required></div>
                    <div class="form-actions"><button type="submit" class="btn btn-success">Simpan</button></div>
                </form>
            </div>
        </div>
    </div>

    <div id="edit-anggota-overlay" class="overlay hidden">
        <div class="overlay-content">
            <div class="overlay-header">
                <h3 class="overlay-main-title">Edit Data Anggota</h3>
                <button class="btn-close btn-close-overlay">&times;</button>
            </div>
            <div class="overlay-body">
                <form action="/projek-ifws/api/update_anggota.php" method="POST">
                    <input type="hidden" name="anggota_id" id="edit_anggota_id">
                    <div id="edit-form-error" class="form-error <?= !$form_error_edit ? 'hidden' : '' ?>"><?= htmlspecialchars($form_error_edit ?? '') ?></div>
                    <div class="form-group">
                        <label for="edit_nama_lengkap">Nama Lengkap</label>
                        <input type="text" id="edit_nama_lengkap" name="nama_lengkap" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_role">Role</label>
                        <select id="edit_role" name="role" required>
                            <option value="" disabled>Pilih Role</option>
                            <option value="sekretaris">Sekretaris</option>
                            <option value="bendahara">Bendahara</option>
                            <option value="promosi">Promosi</option>
                            <option value="teknisi">Teknisi</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_email">Alamat Email</label>
                        <input type="email" id="edit_email" name="email" required>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-success">Update Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="/projek-ifws/assets/scripts/PIC/admin_dataanggota.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Logika Tambah
            const formErrorTambah = <?= json_encode($form_error_tambah) ?>;
            const formDataTambah = <?= json_encode($form_data_tambah) ?>;
            const tambahOverlay = document.getElementById('tambah-anggota-overlay');

            if (formErrorTambah && tambahOverlay) {
                const errorDiv = tambahOverlay.querySelector('#tambah-form-error');
                if(errorDiv) { errorDiv.textContent = formErrorTambah; errorDiv.classList.remove('hidden'); }
                if (formDataTambah) {
                    tambahOverlay.querySelector('#nama_lengkap').value = formDataTambah.nama_lengkap || '';
                    tambahOverlay.querySelector('#role').value = formDataTambah.role || '';
                }
                tambahOverlay.classList.remove('hidden'); 
            }

            // Logika Edit
            const formErrorEdit = <?= json_encode($form_error_edit) ?>;
            const formDataEdit = <?= json_encode($form_data_edit) ?>;
            const editErrorId = <?= json_encode($edit_error_id) ?>;
            const editOverlay = document.getElementById('edit-anggota-overlay');

            if (formErrorEdit && editErrorId && editOverlay) {
                 const errorDiv = editOverlay.querySelector('#edit-form-error');
                 if(errorDiv) { errorDiv.textContent = formErrorEdit; errorDiv.classList.remove('hidden'); }
                 const form = editOverlay.querySelector('form');
                 if(formDataEdit) {
                    form.querySelector('#edit_anggota_id').value = formDataEdit.anggota_id || editErrorId;
                    form.querySelector('#edit_nama_lengkap').value = formDataEdit.nama_lengkap || '';
                    form.querySelector('#edit_role').value = formDataEdit.role || '';
                    form.querySelector('#edit_email').value = formDataEdit.email || '';
                 } else { 
                     const editButton = document.querySelector(`.btn-edit-anggota[data-id="${editErrorId}"]`);
                     if(editButton){
                        form.querySelector('#edit_anggota_id').value = editErrorId;
                        form.querySelector('#edit_nama_lengkap').value = editButton.dataset.nama || '';
                        form.querySelector('#edit_role').value = editButton.dataset.role || '';
                        form.querySelector('#edit_email').value = editButton.dataset.email || '';
                     }
                 }
                editOverlay.classList.remove('hidden'); 
            }
        });
    </script>
</body>
</html>