<?php include '../../includes/auth_check.php'; 
      include '../../includes/koneksi.php';

    // Fungsi baru untuk menentukan tahun akademik dan semester saat ini
    function getCurrentAkademik() {
        $currentMonth = date('n');
        $currentYear = date('Y');
    
        if (($currentMonth >= 8 && $currentMonth <= 12) || $currentMonth == 1) {
            $semester = 'Ganjil';
            if ($currentMonth >= 8) {
                $tahun_akd = $currentYear . '/' . ($currentYear + 1);
            } else {
                $tahun_akd = ($currentYear - 1) . '/' . $currentYear;
            }
        } else {
            $semester = 'Genap';
            $tahun_akd = ($currentYear - 1) . '/' . $currentYear;
        }
    
        return [
           'tahun_akd' => $tahun_akd,
           'semester_akd' => $semester
        ];
    }

    // Dapatkan semester saat ini
    $akademik = getCurrentAkademik();

    // Coba ambil peraturan untuk semester SAAT INI
    $sql_current = "SELECT peraturan_sidang FROM peraturan WHERE tahun_akd = ? AND semester_akd = ?";
    $stmt_current = $koneksi->prepare($sql_current);
    $stmt_current->bind_param("ss", $akademik['tahun_akd'], $akademik['semester_akd']);
    $stmt_current->execute();
    $result_current = $stmt_current->get_result();
    $peraturan = $result_current->fetch_assoc();

    // Jika tidak ditemukan, cari peraturan TERAKHIR yang ada
    if (!$peraturan) {
        // Query untuk mengurutkan berdasarkan tahun lalu semester, dan mengambil yang paling atas
        $sql_latest = "SELECT peraturan_sidang FROM peraturan ORDER BY tahun_akd DESC, semester_akd DESC LIMIT 1";
        $result_latest = $koneksi->query($sql_latest);
        $peraturan = $result_latest->fetch_assoc();
    }
   $nilai_ifws = $peraturan['peraturan_sidang'] ?? 4; 
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pengaturan Sidang TA</title>
    <link rel="stylesheet" href="/proyek-ifws/assets/css/Teknisi/pengaturan_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
    <div class="app-layout">
        <?php include '../../includes/sidebar.php'; // Memanggil sidebar ?>
        <main class="main-content">
            <h1>Pengaturan Syarat Sidang TA</h1>
            <div class="settings-card">
                <form id="settings-form">
                    <div class="setting-item">
                        <label for="jumlah-ifws">Jumlah minimal IFWS yang harus diikuti</label>
                        <div class="input-wrapper">
                            <input type="number" id="jumlah-ifws" min="1" value="<?php echo htmlspecialchars($nilai_ifws); ?>">
                            <span>IFWS</span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="page-actions">
                <a href="homepage_teknisi.php" class="btn btn-kembali">Kembali</a>
                <button id="save-btn" class="btn btn-simpan">Simpan</button>
            </div>
        </main>
    </div>
    <div id="confirmation-modal" class="modal-overlay hidden">
        <div class="modal-content">
            <p>Apakah Anda yakin ingin menyimpan perubahan ini?</p>
            <div class="modal-actions">
                <button id="cancel-btn" class="btn btn-kembali">Batal</button>
                <button id="confirm-save-btn" class="btn btn-simpan">Ya, Simpan</button>
            </div>
        </div>
    </div>
    <script src="/proyek-ifws/assets/js/Teknisi/pengaturan_sidang_script.js"></script>
</body>
</html>