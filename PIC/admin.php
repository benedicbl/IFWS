<?php
require_once '../includes/config.php';

// Validasi Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Ambil Data Statistik untuk Dashboard
// 1. Jumlah Rencana Webinar
$q_rencana = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM webinars WHERE status='rencana'");
$d_rencana = mysqli_fetch_assoc($q_rencana);

// 2. Jumlah Webinar Selesai
$q_selesai = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM webinars WHERE status='finished'");
$d_selesai = mysqli_fetch_assoc($q_selesai);

// 3. Jumlah Peserta TA
$q_peserta = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM peserta WHERE status_ta != 'Bukan_TA'");
$d_peserta = mysqli_fetch_assoc($q_peserta);

// 4. Jumlah Narasumber
$q_narsum = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM narasumber");
$d_narsum = mysqli_fetch_assoc($q_narsum);

// Ambil Data Admin yang Login
$id_user = $_SESSION['user_id'];
$q_user = mysqli_query($koneksi, "SELECT * FROM anggota_ifws WHERE id='$id_user'");
$user = mysqli_fetch_assoc($q_user);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Admin</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/projek-ifws/assets/css/style.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />

    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f3f4f6; }
        
        /* Layout Dashboard Grid */
        .dashboard-container {
            display: grid;
            grid-template-columns: 350px 1fr; /* Kolom Kiri (Profil) fix, Kanan auto */
            gap: 25px;
            margin-top: 20px;
        }

        /* 1. Widget Profil */
        .profile-widget {
            background: white;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            height: fit-content;
        }
        .profile-img-circle {
            width: 100px;
            height: 100px;
            background: #e0e7ff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: #2563eb;
            font-size: 40px;
        }
        .profile-name { font-size: 20px; font-weight: 600; color: #1e3a8a; margin-bottom: 5px; }
        .profile-role { font-size: 14px; color: #6b7280; font-weight: 500; text-transform: uppercase; letter-spacing: 1px; background: #f3f4f6; padding: 5px 15px; border-radius: 20px; display: inline-block; margin-bottom: 20px; }
        .profile-email { font-size: 14px; color: #4b5563; display: flex; align-items: center; justify-content: center; gap: 8px; }
        
        .btn-logout-widget {
            display: block;
            width: 100%;
            padding: 10px;
            margin-top: 30px;
            background: #fee2e2;
            color: #b91c1c;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: 0.3s;
        }
        .btn-logout-widget:hover { background: #fecaca; }

        /* 2. Statistik Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: transform 0.2s;
        }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-icon {
            width: 50px; height: 50px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 24px;
        }
        .icon-blue { background: #eff6ff; color: #2563eb; }
        .icon-green { background: #dcfce7; color: #166534; }
        .icon-orange { background: #ffedd5; color: #c2410c; }
        .icon-purple { background: #f3e8ff; color: #7e22ce; }

        .stat-info h3 { font-size: 28px; font-weight: 700; color: #111; margin: 0; }
        .stat-info p { font-size: 13px; color: #6b7280; margin: 0; }

        /* 3. Shortcut Actions */
        .shortcuts-section h3 { font-size: 18px; font-weight: 600; color: #1e3a8a; margin-bottom: 15px; }
        .shortcuts-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }
        .shortcut-btn {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            text-decoration: none;
            color: #4b5563;
            border: 1px solid #e5e7eb;
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }
        .shortcut-btn i { font-size: 24px; color: #2563eb; }
        .shortcut-btn span { font-size: 13px; font-weight: 500; }
        .shortcut-btn:hover { border-color: #2563eb; color: #2563eb; background: #eff6ff; }

        /* Responsiveness */
        @media (max-width: 1024px) {
            .dashboard-container { grid-template-columns: 1fr; }
            .profile-widget { display: flex; align-items: center; justify-content: space-between; text-align: left; padding: 20px; }
            .profile-img-circle { margin: 0 20px 0 0; width: 60px; height: 60px; font-size: 24px; }
            .btn-logout-widget { width: auto; margin: 0; padding: 8px 20px; }
        }
    </style>
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
            <ul><li class="active"><a href="admin.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li></ul>
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
            <ul><li><a href="admin_dataanggota.php"><i class="fas fa-users-cog"></i><span>Data Anggota IFWS</span></a></li></ul>
          </div>
          <div class="nav-section">
              <p class="section-title">AKUN</p>
              <ul><li><a href="/projek-ifws/logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li></ul>
          </div>
        </nav>
      </aside>

      <main class="main-content">
        <div class="page-header">
            <h1>Dashboard Overview</h1>
        </div>

        <div class="dashboard-container">
            
            <div class="profile-widget">
                <div class="profile-img-circle">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="profile-name"><?= htmlspecialchars($user['nama_lengkap']) ?></div>
                <div class="profile-role">Admin / PIC</div>
                
                <div class="profile-email">
                    <i class="fas fa-envelope"></i> <?= htmlspecialchars($user['email']) ?>
                </div>

            </div>

            <div class="right-content">
                
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon icon-blue"><i class="fas fa-calendar-check"></i></div>
                        <div class="stat-info">
                            <h3><?= $d_rencana['total'] ?></h3>
                            <p>Rencana Webinar</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon icon-green"><i class="fas fa-history"></i></div>
                        <div class="stat-info">
                            <h3><?= $d_selesai['total'] ?></h3>
                            <p>Webinar Selesai</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon icon-purple"><i class="fas fa-users"></i></div>
                        <div class="stat-info">
                            <h3><?= $d_peserta['total'] ?></h3>
                            <p>Peserta TA Aktif</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon icon-orange"><i class="fas fa-chalkboard-teacher"></i></div>
                        <div class="stat-info">
                            <h3><?= $d_narsum['total'] ?></h3>
                            <p>Total Narasumber</p>
                        </div>
                    </div>
                </div>

                <div class="shortcuts-section">
                    <h3>Akses Cepat</h3>
                    <div class="shortcuts-grid">
                        <a href="admin_listifws.php" class="shortcut-btn">
                            <i class="fas fa-plus-circle"></i>
                            <span>Buat Webinar</span>
                        </a>
                        <a href="admin_pesertaTA.php" class="shortcut-btn">
                            <i class="fas fa-user-check"></i>
                            <span>Cek Peserta TA</span>
                        </a>
                        <a href="admin_datanarsum.php" class="shortcut-btn">
                            <i class="fas fa-address-book"></i>
                            <span>Data Narasumber</span>
                        </a>
                        <a href="admin_dataanggota.php" class="shortcut-btn">
                            <i class="fas fa-user-cog"></i>
                            <span>Kelola Anggota</span>
                        </a>
                    </div>
                </div>

            </div>
        </div>
      </main>
    </div>
  </body>
</html>