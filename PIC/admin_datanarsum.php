<?php
require_once '../includes/config.php';
// Proteksi session
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /projek-ifws/login.php');
    exit();
}

// 1. Ambil semua narasumber
$query_narsum = "SELECT * FROM narasumber ORDER BY nama ASC";
$result_narsum = mysqli_query($koneksi, $query_narsum);

// 2. Ambil semua riwayat webinar dari tabel 'webinars' dengan JOIN
$query_riwayat = "SELECT w.*, GROUP_CONCAT(DISTINCT n.nama SEPARATOR ', ') as daftar_narasumber
                  FROM webinars w
                  LEFT JOIN webinar_narasumber wn ON w.id = wn.id_webinar
                  LEFT JOIN narasumber n ON wn.id_narasumber = n.id
                  WHERE w.status = 'finished'
                  GROUP BY w.id
                  ORDER BY w.tanggal_direncanakan DESC";
$result_riwayat = mysqli_query($koneksi, $query_riwayat);

$semua_riwayat = [];
if($result_riwayat) {
    while($row = mysqli_fetch_assoc($result_riwayat)) {
        $semua_riwayat[] = $row;
    }
}

// 3. Ambil semua foto pelaksanaan dan kelompokkan
$query_fotos = "SELECT * FROM foto_pelaksanaan";
$result_fotos = mysqli_query($koneksi, $query_fotos);
$fotos_by_webinar = [];
if ($result_fotos) {
    while ($foto = mysqli_fetch_assoc($result_fotos)) {
        $fotos_by_webinar[$foto['id_webinar']][] = ['id' => $foto['id'], 'path' => $foto['file_path']];
    }
}

$form_error = $_SESSION['form_error'] ?? null;
$form_data = $_SESSION['form_data'] ?? null;
$edit_error_id = isset($_GET['edit_error']) ? (int)$_GET['id'] : null; 
unset($_SESSION['form_error'], $_SESSION['form_data']); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0" /><title>Admin - Data Narasumber</title>
    <link rel="stylesheet" href="/projek-ifws/assets/css/style.css" />
    <link rel="stylesheet" href="/projek-ifws/assets/css/PIC/admin_datanarsum.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <div class="sidebar-header"><div class="logo-and-title"><img src="/projek-ifws/assets/picture/logo.png" alt="Logo Informatics" class="sidebar-logo"/><h2>Informatics<br /><span>Webinar Series</span></h2></div><div class="admin-profile"><small>Admin</small></div></div>
            <nav class="sidebar-nav">
                <div class="nav-section"><p class="section-title">DASHBOARD</p><ul><li><a href="/projek-ifws/PIC/admin.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li></ul></div>
                
                <div class="nav-section">
                    <p class="section-title">KELOLA DATA IFWS</p>
                    <ul>
                        <li><a href="/projek-ifws/PIC/admin_listifws.php"><i class="fas fa-calendar-alt"></i><span>Data Webinar</span></a></li>
                        <li><a href="/projek-ifws/PIC/admin_riwayatifws.php"><i class="fas fa-history"></i><span>Riwayat Webinar</span></a></li>
                        <li class="active"><a href="/projek-ifws/PIC/admin_datanarsum.php"><i class="fas fa-user-friends"></i><span>Data Narasumber</span></a></li>
                        <li><a href="admin_pesertaTA.php"><i class="fas fa-user-friends"></i><span>Peserta Tugas Akhir</span></a></li>
                    </ul>
                </div>
                
                <div class="nav-section"><p class="section-title">KELOLA DATA ANGGOTA IFWS</p><ul><li><a href="/projek-ifws/PIC/admin_dataanggota.php"><i class="fas fa-users-cog"></i><span>Data Anggota IFWS</span></a></li></ul></div>
                <div class="nav-section"><p class="section-title">AKUN</p><ul><li><a href="/projek-ifws/logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li></ul></div>
            </nav>
        </aside>
        
        <main class="main-content">
            <div class="page-header"><h1>List Narasumber</h1><button id="btn-tambah-narsum" class="btn btn-success"><i class="fas fa-plus"></i> Tambah Narasumber</button></div>
            
            <?php if(isset($_GET['status']) && $_GET['status'] == 'update_sukses'): ?><div class="alert alert-success">Data narasumber berhasil diperbarui!</div><?php endif; ?>
            <?php if(isset($_GET['status']) && $_GET['status'] == 'tambah_sukses'): ?><div class="alert alert-success">Narasumber baru berhasil ditambahkan!</div><?php endif; ?>

            <div class="content-card">
                <div class="table-container">
                    <table>
                        <thead><tr><th>Nama</th><th>Alamat Email</th><th>Aksi</th></tr></thead>
                        <tbody>
                            <?php if ($result_narsum && mysqli_num_rows($result_narsum) > 0): mysqli_data_seek($result_narsum, 0); while ($narsum = mysqli_fetch_assoc($result_narsum)): ?>
                                <tr>
                                    <td><?= htmlspecialchars($narsum['nama']) ?></td>
                                    <td><?= htmlspecialchars($narsum['email']) ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn btn-icon-only btn-warning btn-edit-narsum" 
                                                    data-id="<?= $narsum['id'] ?>" 
                                                    data-nama="<?= htmlspecialchars($narsum['nama']) ?>" 
                                                    data-email="<?= htmlspecialchars($narsum['email']) ?>">
                                                <i class="fas fa-pencil-alt"></i>
                                            </button>
                                            <a href="#" class="btn btn-lihat view-history-btn" data-target-overlay="history-overlay-<?= $narsum['id'] ?>">Lihat Riwayat</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; else: ?>
                                <tr><td colspan="3" style="text-align: center;">Tidak ada data narasumber.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <div id="tambah-narsum-overlay" class="overlay hidden">
        <div class="overlay-content">
            <div class="overlay-header"><h3 class="overlay-main-title">Tambah Narasumber Baru</h3><button class="btn-close btn-close-overlay">&times;</button></div>
            <div class="overlay-body">
                <form action="/projek-ifws/api/tambah_narasumber.php" method="POST">
                    <div class="form-group"><label for="nama_narsum">Nama Lengkap</label><input type="text" id="nama_narsum" name="nama" placeholder="Masukkan nama narasumber" required></div>
                    <div class="form-group"><label for="email_narsum">Alamat Email</label><input type="email" id="email_narsum" name="email" placeholder="contoh@email.com" required></div>
                    <div class="form-actions"><button type="submit" class="btn btn-success">Simpan</button></div>
                </form>
            </div>
        </div>
    </div>

    <div id="edit-narsum-overlay" class="overlay hidden">
        <div class="overlay-content">
            <div class="overlay-header">
                <h3 class="overlay-main-title">Edit Data Narasumber</h3>
                <button class="btn-close btn-close-overlay">&times;</button>
            </div>
            <div class="overlay-body">
                <form action="/projek-ifws/api/update_narasumber.php" method="POST">
                    <input type="hidden" name="narasumber_id" id="edit_narasumber_id">
                    <div class="form-group">
                        <label for="edit_nama_narsum">Nama Lengkap</label>
                        <input type="text" id="edit_nama_narsum" name="nama" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_email_narsum">Alamat Email</label>
                        <input type="email" id="edit_email_narsum" name="email" required>
                    </div>
                    <div id="edit-form-error" class="form-error hidden"></div> 
                    <div class="form-actions">
                        <button type="submit" class="btn btn-success">Update Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php if ($result_narsum) { mysqli_data_seek($result_narsum, 0); while ($narsum = mysqli_fetch_assoc($result_narsum)): ?>
    <div id="history-overlay-<?= $narsum['id'] ?>" class="overlay hidden">
        <div class="overlay-content large">
            <div class="overlay-header"><h3 class="overlay-main-title">Riwayat Narasumber</h3><button class="btn-back close-history-overlay">&lt; Back</button></div>
            <div class="overlay-body">
                <p class="speaker-name-title">Nama Narasumber : <strong><?= htmlspecialchars($narsum['nama']) ?></strong></p>
                <div class="table-container">
                    <table class="modal-table">
                        <thead><tr><th>Tanggal</th><th>Topik</th><th>Foto Pelaksanaan</th></tr></thead>
                        <tbody>
                            <?php 
                            $found_history = false;
                            foreach ($semua_riwayat as $riwayat):
                                if (str_contains($riwayat['daftar_narasumber'] ?? '', $narsum['nama'])):
                                    $found_history = true;
                                    $adaFoto = isset($fotos_by_webinar[$riwayat['id']]);
                            ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($riwayat['tanggal_direncanakan'])) ?></td>
                                    <td><?= htmlspecialchars($riwayat['topik']) ?></td>
                                    <td><button class="btn-icon-only btn-view-photos <?= $adaFoto ? 'has-photos' : '' ?>" data-images='<?= $adaFoto ? htmlspecialchars(json_encode($fotos_by_webinar[$riwayat['id']])) : '[]' ?>'><i class="fas fa-eye"></i></button></td>
                                </tr>
                            <?php endif; endforeach; ?>
                            <?php if (!$found_history): ?>
                                <tr><td colspan="3" style="text-align: center;">Tidak ada riwayat untuk narasumber ini.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php endwhile; } ?>

    <div id="view-slider-overlay" class="overlay hidden">
        <div class="overlay-content large slider-content">
            <button class="btn-close-slider btn-close-overlay">&times;</button>
            <button class="slider-nav" id="slider-prev">&lt;</button>
            <img src="" id="slider-image" alt="Foto Pelaksanaan">
            <button class="slider-nav" id="slider-next">&gt;</button>
        </div>
    </div>

    <script src="/projek-ifws/assets/scripts/PIC/admin_datanarsum.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const formError = <?= json_encode($form_error) ?>;
            const formData = <?= json_encode($form_data) ?>;
            const editErrorId = <?= json_encode($edit_error_id) ?>;
            const editOverlay = document.getElementById('edit-narsum-overlay');

            if (formError && editErrorId && editOverlay) {
                const errorDiv = editOverlay.querySelector('#edit-form-error');
                if (errorDiv) { errorDiv.textContent = formError; errorDiv.classList.remove('hidden'); }
                if(formData) {
                    editOverlay.querySelector('#edit_narasumber_id').value = formData.narasumber_id || editErrorId;
                    editOverlay.querySelector('#edit_nama_narsum').value = formData.nama || '';
                    editOverlay.querySelector('#edit_email_narsum').value = formData.email || '';
                } else {
                     const editButton = document.querySelector(`.btn-edit-narsum[data-id="${editErrorId}"]`);
                     if(editButton){
                         editOverlay.querySelector('#edit_narasumber_id').value = editErrorId;
                         editOverlay.querySelector('#edit_nama_narsum').value = editButton.dataset.nama || '';
                         editOverlay.querySelector('#edit_email_narsum').value = editButton.dataset.email || '';
                     }
                }
                editOverlay.classList.remove('hidden');
            }
        });
    </script>
</body>
</html>