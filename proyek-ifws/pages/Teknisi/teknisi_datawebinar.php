<?php include '../../includes/auth_check.php'; ?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Admin - List Data Webinar</title>
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
            <p class="user-name"><?php echo htmlspecialchars($_SESSION['nama']); ?></p>
          </div>
        </div>
        <nav class="sidebar-nav">
          <div class="nav-section">
            <p class="section-title">DASHBOARD</p>
            <ul>
              <li>
                <a href="/proyek-ifws/pages/Teknisi/teknisi.php">
                  <i class="fas fa-tachometer-alt"></i>
                  <span>Dashboard</span>
                </a>
              </li>
            </ul>
          </div>
          <div class="nav-section">
            <p class="section-title">KELOLA DATA IFWS</p>
            <ul>
              <li class="active">
                <a href="/proyek-ifws/pages/Teknisi/teknisi_datawebinar.php">
                  <i class="fas fa-calendar-alt"></i>
                  <span>Data Webinar</span>
                </a>
              </li>
              <li>
                <a href="/proyek-ifws/pages/Teknisi/teknisi_pengaturanifws.php">
                  <i class="fas fa-users-cog"></i>
                  <span>Pengaturan IFWS</span>
                </a>
              </li>
            </ul>
          </div>
        </nav>
      </aside>

      <main class="main-content">
        <div class="page-header">
          <h1>List Data Webinar</h1>

          <div class="dropdown">
              <button id="pilihTahunBtn" class="year-dropdown">
                  <span id="pilihTahunBtnText">Pilih Tahun Akademik</span> 
              </button>
              <div id="tahunDropdown" class="dropdown-content">
                  </div>
          </div>
        </div>

        <div class="content-card">
          <div id="initial-message">
            <p>Silakan pilih tahun akademik untuk menampilkan data webinar.</p>
          </div>
          
          <div id="data-section" class="table-container hidden">
            <table>
              <thead>
                <tr>
                  <th><input type="checkbox" /></th>
                  <th>Narasumber</th>
                  <th>Tanggal</th>
                  <th>Kategori IFWS</th>
                  <th>Topik</th>
                  <th>Link Akses</th>
                </tr>
              </thead>
              <tbody id="webinar-table-body">
                </tbody>
            </table>
          </div>
        </div>
      </main>
    </div>

    <div id="link-overlay" class="overlay hidden">
      <div class="overlay-content">
        <div class="overlay-header">
          <button id="close-link-overlay-btn" class="btn-back">
            &lt; Back
          </button>
        </div>
        <div class="overlay-body">
          <h2 class="overlay-title" id="link-overlay-title">
            Link Akses Webinar : <span></span>
          </h2>
          <p class="overlay-subtitle">
            Silahkan masukkan link untuk akses peserta
          </p>
          <textarea
            id="link-textarea"
            rows="4"
            placeholder="Contoh: https://meet.google.com/xyz-abcd-efg"
          ></textarea>
        </div>
        <div class="overlay-footer">
          <button id="update-link-btn" class="btn btn-primary">Update</button>
        </div>
      </div>
    </div>
    
    <script src="/proyek-ifws/assets/js/Teknisi/teknisi_script.js"></script>
  </body>
</html>