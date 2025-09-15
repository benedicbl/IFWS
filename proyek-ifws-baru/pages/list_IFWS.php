<?php include '../includes/auth_check.php'; ?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List IFWS</title>
    <link rel="stylesheet" href="../assets/css/list_IFWS_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
    <div class="app-layout">
        
        <?php include '../includes/sidebar.php'; // Memanggil sidebar ?>

        <main class="main-content">
            <header class="main-header">
                <h1>List IFWS</h1>
                <div class="dropdown">
                    <button id="pilihTahunBtn" class="dropdown-btn">Pilih Tahun <i class="fa-solid fa-chevron-down"></i></button>
                    <div id="tahunDropdown" class="dropdown-content">
                        <a href="#" data-tahun="2025-Ganjil">2025-Ganjil</a>
                        <a href="#" data-tahun="2024-Genap">2024-Genap</a>
                        <a href="#" data-tahun="2024-Ganjil">2024-Ganjil</a>
                        <a href="#" data-tahun="2023-Genap">2023-Genap</a>
                    </div>
                </div>
            </header>

            <div id="initial-message" class="initial-message">
                <p>Silakan pilih tahun akademik terlebih dahulu untuk menampilkan data webinar.</p>
            </div>

            <div id="webinar-section" class="hidden">
                <div class="webinar-header">
                    <h2 id="tahunAkademikTitle"></h2>
                    <div class="webinar-actions">
                        <a href="tambah_webinar.php" class="btn btn-primary">+ Tambah Webinar</a>
                        <button id="toggleEditBtn" class="btn btn-secondary">Edit</button>
                    </div>
                </div>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Narasumber</th>
                                <th>Tanggal</th>
                                <th>Jenis IFWS</th>
                                <th>Topik Webinar</th>
                                <th class="actions-header hidden">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="webinar-table-body">
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
    <script src="../assets/js/list_IFWS_script.js"></script>
</body>
</html>