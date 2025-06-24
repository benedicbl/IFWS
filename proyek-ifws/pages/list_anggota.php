<?php include '../includes/auth_check.php'; ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>List Anggota IFWS</title>
    <link rel="stylesheet" href="../assets/css/list_anggota.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body class="page-anggota">
    <div class="app-layout">
        
        <?php include '../includes/sidebar.php'; ?>

        <main class="main-content">
            <header class="main-header">
                <h1>List Anggota IFWS</h1>
                <a href="tambah_anggota.php" class="btn btn-tambah">
                    <i class="fa-solid fa-plus"></i> Tambah Anggota
                </a>
            </header>
            <div class="page-controls">
                <div class="search-container">
                    <i class="fa-solid fa-search"></i>
                    <input type="text" id="searchInput"
                        placeholder="Cari anggota berdasarkan nama, email, atau jabatan...">
                </div>
                <button id="toggleEditBtn" class="btn btn-secondary">Edit</button>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Nama Anggota</th>
                            <th>Email</th>
                            <th>Jabatan</th>
                            <th class="actions-header hidden">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="anggota-table-body"></tbody>
                </table>
            </div>
        </main>
    </div>
    <script src="../assets/js/list_anggota_script.js"></script>
</body>

</html>