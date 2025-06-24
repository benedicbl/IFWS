<?php include '../includes/auth_check.php'; ?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Narasumber</title>
    <link rel="stylesheet" href="../assets/css/list_narasumber.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
    <div class="app-layout">
        
        <?php include '../includes/sidebar.php'; // Memanggil sidebar ?>

        <main class="main-content">
            <header class="main-header">
                <h1>List Narasumber</h1>
                <a href="tambah_narasumber.php" class="btn btn-tambah">
                    <i class="fa-solid fa-plus"></i> Tambah Narasumber
                </a>
            </header>

            <div class="page-controls">
                <div class="search-container">
                    <i class="fa-solid fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Cari narasumber berdasarkan nama atau email...">
                </div>
                <button id="toggleEditBtn" class="btn btn-secondary">Edit</button>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Nama Narasumber</th>
                            <th>Email</th>
                            <th class="actions-header hidden">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="narasumber-table-body">
                    </tbody>
                </table>
            </div>
        </main>
    </div>
    <script src="../assets/js/list_narasumber.js"></script>
</body>
</html>