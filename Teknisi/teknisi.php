<?php
require_once '../includes/config.php';

// Validasi Teknisi
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teknisi') {
    header('Location: /projek-ifws/login.php');
    exit();
}

// --- AMBIL DATA STATISTIK ---

// 1. Webinar Pending Link (Belum Selesai & Link Kosong)
// Ini adalah tugas utama teknisi
$q_pending = mysqli_query($koneksi, "
    SELECT COUNT(*) as total 
    FROM webinars 
    WHERE status != 'finished' 
    AND (link_akses IS NULL OR link_akses = '')
");
$d_pending = mysqli_fetch_assoc($q_pending);

// 2. Webinar Link Ready (Belum Selesai & Link Ada)
$q_ready = mysqli_query($koneksi, "
    SELECT COUNT(*) as total 
    FROM webinars 
    WHERE status != 'finished' 
    AND (link_akses IS NOT NULL AND link_akses != '')
");
$d_ready = mysqli_fetch_assoc($q_ready);

// 3. Total Webinar Selesai (History)
$q_selesai = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM webinars WHERE status='finished'");
$d_selesai = mysqli_fetch_assoc($q_selesai);

// 4. Total Semua Webinar
$q_total = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM webinars");
$d_total = mysqli_fetch_assoc($q_total);

// Ambil Data Profil
$id_user = $_SESSION['user_id'];
$q_user = mysqli_query($koneksi, "SELECT * FROM anggota_ifws WHERE id='$id_user'");
$user = mysqli_fetch_assoc($q_user);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Teknisi</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/projek-ifws/assets/css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />

    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f3f4f6; }
        
        /* Layout Grid */
        .dashboard-container {
            display: grid;
            grid-template-columns: 350px 1fr;
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
            width: 100px; height: 100px; background: #e0e7ff; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px; color: #2563eb; font-size: 40px;
        }
        .profile-name { font-size: 20px; font-weight: 600; color: #1e3a8a; margin-bottom: 5px; }
        .profile-role { font-size: 14px; color: #6b7280; font-weight: 500; text-transform: uppercase; letter-spacing: 1px; background: #f3f4f6; padding: 5px 15px; border-radius: 20px; display: inline-block; margin-bottom: 20px; }
        .profile-email { font-size: 14px; color: #4b5563; display: flex; align-items: center; justify-content: center; gap: 8px; }
        
        .btn-logout-widget {
            display: block; width: 100%; padding: 10px; margin-top: 30px; background: #fee2e2; color: #b91c1c; border-radius: 8px; text-decoration: none; font-weight: 500; transition: 0.3s;
        }
        .btn-logout-widget:hover { background: #fecaca; }

        /* --- WIDGET TUGAS (Highlight) --- */
        .task-card {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            border-radius: 12px;
            padding: 25px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            box-shadow: 0 4px 10px rgba(245, 158, 11, 0.3);
        }
        .task-info h3 { font-size: 20px; margin-bottom: 5px; font-weight: 600; }
        .task-info p { font-size: 14px; opacity: 0.9; margin: 0; }
        
        .btn-task-action {
            background: white; color: #d97706; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; transition: 0.2s; white-space: nowrap;
        }
        .btn-task-action:hover { background: #fff7ed; transform: translateY(-2px); }

        /* 2. Statistik Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 20px; transition: transform 0.2s;
        }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-icon { width: 50px; height: 50px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 24px; }
        
        .icon-blue { background: #eff6ff; color: #2563eb; }
        .icon-green { background: #dcfce7; color: #166534; }
        .icon-red { background: #fee2e2; color: #b91c1c; }
        .icon-purple { background: #f3e8ff; color: #7e22ce; }

        .stat-info h3 { font-size: 28px; font-weight: 700; color: #111; margin: 0; }
        .stat-info p { font-size: 13px; color: #6b7280; margin: 0; }

        /* 3. Shortcuts */
        .shortcuts-section h3 { font-size: 18px; font-weight: 600; color: #1e3a8a; margin-bottom: 15px; }
        .shortcuts-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; }
        .shortcut-btn {
            background: white; padding: 20px; border-radius: 10px; text-align: center; text-decoration: none; color: #4b5563; border: 1px solid #e5e7eb; transition: all 0.3s; display: flex; flex-direction: column; align-items: center; gap: 10px;
        }
        .shortcut-btn i { font-size: 24px; color: #2563eb; }
        .shortcut-btn span { font-size: 13px; font-weight: 500; }
        .shortcut-btn:hover { border-color: #2563eb; color: #2563eb; background: #eff6ff; }

        @media (max-width: 1024px) {
            .dashboard-container { grid-template-columns: 1fr; }
            .task-card { flex-direction: column; text-align: center; gap: 15px; }
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
                <div class="logo-and-title"><img src="/projek-ifws/assets/picture/logo.png" alt="Logo Informatics" class="sidebar-logo" /><h2>Informatics<br /><span>Webinar Series</span></h2></div>
                <div class="admin-profile"><small>Teknisi</small></div>
            </div>
            <nav class="sidebar-nav">
                <div class="nav-section"><p class="section-title">DASHBOARD</p><ul><li class="active"><a href="/projek-ifws/Teknisi/teknisi.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li></ul></div>
                <div class="nav-section"><p class="section-title">KELOLA DATA IFWS</p><ul><li><a href="/projek-ifws/Teknisi/teknisi_datawebinar.php"><i class="fas fa-calendar-alt"></i><span>Data Webinar</span></a></li><li><a href="/projek-ifws/Teknisi/teknisi_pengaturanifws.php"><i class="fas fa-cogs"></i><span>Pengaturan IFWS</span></a></li></ul></div>
                <div class="nav-section"><p class="section-title">AKUN</p><ul><li><a href="/projek-ifws/logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li></ul></div>
            </nav>
        </aside>

        <main class="main-content">
            <div class="page-header">
                <h1>Dashboard Overview</h1>
            </div>

            <div class="dashboard-container">
                
                <div class="profile-widget">
                    <div class="profile-img-circle"><i class="fas fa-wrench"></i></div>
                    <div class="profile-name"><?= htmlspecialchars($user['nama_lengkap']) ?></div>
                    <div class="profile-role">Teknisi</div>
                    <div class="profile-email"><i class="fas fa-envelope"></i> <?= htmlspecialchars($user['email']) ?></div>
                </div>

                <div class="right-content">
                    
                    <?php if ($d_pending['total'] > 0): ?>
                    <div class="task-card">
                        <div class="task-info">
                            <h3><i class="fas fa-link"></i> Link Perlu Disisipkan</h3>
                            <p>Terdapat <strong><?= $d_pending['total'] ?> Webinar</strong> yang belum memiliki Link Akses.</p>
                        </div>
                        <a href="teknisi_datawebinar.php" class="btn-task-action">Update Link <i class="fas fa-arrow-right"></i></a>
                    </div>
                    <?php else: ?>
                    <div class="task-card" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);">
                        <div class="task-info">
                            <h3><i class="fas fa-check-circle"></i> Semua Aman</h3>
                            <p>Semua webinar mendatang sudah memiliki Link Akses.</p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon icon-blue"><i class="fas fa-video"></i></div>
                            <div class="stat-info">
                                <h3><?= $d_total['total'] ?></h3>
                                <p>Total Webinar</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon icon-green"><i class="fas fa-link"></i></div>
                            <div class="stat-info">
                                <h3><?= $d_ready['total'] ?></h3>
                                <p>Link Ready</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon icon-red"><i class="fas fa-unlink"></i></div>
                            <div class="stat-info">
                                <h3><?= $d_pending['total'] ?></h3>
                                <p>Link Kosong</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon icon-purple"><i class="fas fa-history"></i></div>
                            <div class="stat-info">
                                <h3><?= $d_selesai['total'] ?></h3>
                                <p>Webinar Selesai</p>
                            </div>
                        </div>
                    </div>

                    <div class="shortcuts-section">
                        <h3>Akses Cepat</h3>
                        <div class="shortcuts-grid">
                            <a href="teknisi_datawebinar.php" class="shortcut-btn">
                                <i class="fas fa-link"></i>
                                <span>Kelola Link</span>
                            </a>
                            <a href="teknisi_pengaturanifws.php" class="shortcut-btn">
                                <i class="fas fa-sliders-h"></i>
                                <span>Pengaturan IFWS</span>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </main>
    </div>
</body>
</html>