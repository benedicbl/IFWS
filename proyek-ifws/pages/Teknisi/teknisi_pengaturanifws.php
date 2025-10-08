<?php
// DITAMBAHKAN: Blok PHP untuk mengambil data pengaturan saat ini
include '../../includes/auth_check.php';
include '../../includes/koneksi.php'; // Pastikan path ini benar

// Fungsi untuk menentukan tahun akademik dan semester saat ini
function getCurrentAkademik() {
    $currentMonth = date('n');
    $currentYear = date('Y');
    if (($currentMonth >= 8 && $currentMonth <= 12) || $currentMonth == 1) {
        $semester = 'Ganjil';
        $tahun_akd = ($currentMonth >= 8) ? ($currentYear . '/' . ($currentYear + 1)) : (($currentYear - 1) . '/' . $currentYear);
    } else {
        $semester = 'Genap';
        $tahun_akd = ($currentYear - 1) . '/' . $currentYear;
    }
    return ['tahun_akd' => $tahun_akd, 'semester_akd' => $semester];
}

$akademik = getCurrentAkademik();
$tahun_akd = $akademik['tahun_akd'];
$semester_akd = $akademik['semester_akd'];

// Siapkan nilai default jika data belum ada di database
$current_duration = 45; // Default durasi
$current_ifws = 3;      // Default syarat IFWS

// Query untuk mengambil pengaturan semester ini
$sql = "SELECT peraturan_waktu, peraturan_sidang FROM peraturan WHERE tahun_akd = ? AND semester_akd = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("ss", $tahun_akd, $semester_akd);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $current_duration = $row['peraturan_waktu'];
    $current_ifws = $row['peraturan_sidang'];
}
$stmt->close();
$koneksi->close();
// --- AKHIR BLOK PHP TAMBAHAN ---
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Admin - Pengaturan IFWS</title>
    <link rel="stylesheet" href="/proyek-ifws/IFWS_Baru/assets/css/style.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    />
  </head>
  <body>
    <div class="container">
      <aside class="sidebar">
        <div class="sidebar-header">
          <div class="logo-and-title">
            <img
              src="/proyek-ifws/assets/images/Logo.jpg"
              alt="Logo Informatics"
              class="sidebar-logo"
            />
            <h2>Informatics<br /><span>Webinar Series</span></h2>
          </div>
          <div class="admin-profile">
            <small>Teknisi</small>
            <?php
              echo '<p class="user-name">' . htmlspecialchars($_SESSION['nama']) . '</p>';
            ?>
          </div>
        </div>
        <nav class="sidebar-nav">
          <div class="nav-section">
            <p class="section-title">DASHBOARD</p>
            <ul>
              <li><a href="teknisi.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
            </ul>
          </div>
          <div class="nav-section">
            <p class="section-title">KELOLA DATA IFWS</p>
            <ul>
              <li><a href="/proyek-ifws/pages/Teknisi/teknisi_datawebinar.php"><i class="fas fa-calendar-alt"></i><span>Data Webinar</span></a></li>
              <li class="active"><a href="/proyek-ifws/pages/Teknisi/teknisi_pengaturanifws.php"><i class="fas fa-users-cog"></i><span>Pengaturan IFWS</span></a></li>
            </ul>
          </div>
        </nav>
      </aside>

      <main class="main-content">
        <h1 class="page-title">Pengaturan IFWS</h1>

        <div class="setting-section">
          <h2 class="setting-title">Pengaturan Durasi Kehadiran</h2>
          <div class="content-card setting-card">
            <div class="setting-item">
              <label for="min-duration">Durasi minimal kehadiran agar terhitung valid</label>
              <div class="input-group">
                <input type="number" id="min-duration" value="<?php echo htmlspecialchars($current_duration); ?>" min="0" />
                <span>menit</span>
              </div>
            </div>
          </div>
        </div>

        <div class="setting-section">
          <h2 class="setting-title">Pengaturan Syarat Sidang TA</h2>
          <div class="content-card setting-card">
            <div class="setting-item">
              <label for="min-ifws">Jumlah minimal IFWS yang harus diikuti</label>
              <div class="input-group">
                <input type="number" id="min-ifws" value="<?php echo htmlspecialchars($current_ifws); ?>" min="0" />
                <span>IFWS</span>
              </div>
            </div>
          </div>
        </div>
        
        <div class="page-actions">
            <button id="simpan-pengaturan-btn" class="btn btn-primary">Simpan</button>
        </div>
      </main>
    </div>

    <script src="/proyek-ifws/proyek-ifws/assets/js/Teknisi/pengaturan_script.js"></script>
  </body>
</html>