<?php include '../../includes/auth_check.php'; ?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Admin - List Data Webinar</title>
    <link rel="stylesheet" href="/proyek-ifws/IFWS_Baru/assets/css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>
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
              <li class="active">
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
              <li>
                <a href="/proyek-ifws/pages/Promosi/promosi_datawebinar.php">
                  <i class="fas fa-calendar-alt"></i>
                  <span>Data Webinar</span>
                </a>
              </li>
            </ul>
          </div>
        </nav>
      </aside>

      <main class="main-content"></main>
    </div>

    <script src="/proyek-ifws/assets/js/Promosi/overlayposter.js"></script>
  </body>
</html>
