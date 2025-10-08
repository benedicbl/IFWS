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
            <small>Promosi</small>
            <?php
              echo '<p class="user-name">' . htmlspecialchars($_SESSION['nama']) . '</p>';
            ?>
          </div>
        </div>
        <nav class="sidebar-nav">
          <div class="nav-section">
            <p class="section-title">DASHBOARD</p>
            <ul>
              <li>
                <a href="/proyek-ifws/pages/Promosi/promosi.php">
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
                <a href="/proyek-ifws/pages/Promosi/promosi_datawebinar.php">
                  <i class="fas fa-calendar-alt"></i>
                  <span>Data Webinar</span>
                </a>
              </li>
            </ul>
          </div>
        </nav>
      </aside>

      <main class="main-content">
        <div class="page-header">
          <h1>List Data Webinar</h1>

          <div class="dropdown-container">
              <button id="pilihTahunBtn" class="year-dropdown">
                  Pilih Tahun Akademik <i class="fa-solid fa-chevron-down"></i>
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
                  <th>Poster</th>
                </tr>
              </thead>
              <tbody id="webinar-table-body">
                </tbody>
            </table>
          </div>
        </div>
      </main>
    </div>

    <div id="poster-modal" class="modal hidden">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Upload Poster</h3>
                <p>Untuk webinar: <strong id="modal-webinar-title"></strong></p>
            </div>
            <div class="modal-body">
                <input type="file" id="poster-file-input" accept="image/*">
            </div>
            <div class="modal-footer">
                <button id="cancel-modal-btn" class="btn btn-secondary">Batal</button>
                <button id="save-poster-btn" class="btn">Simpan</button>
            </div>
        </div>
    </div>

    <div id="poster-overlay" class="overlay hidden">
      <div class="overlay-content">
        <div class="overlay-header">
          <h3 class="overlay-main-title">Preview Poster</h3>
          <button id="close-overlay-btn" class="btn-back">&lt; Back</button>
        </div>
        <div class="overlay-body">
          <img id="poster-preview-img" src="" alt="Poster Preview" />
        </div>
      </div>
    </div>

    <script src="/proyek-ifws/assets/js/Promosi/overlayposter.js"></script>
  </body>
</html>