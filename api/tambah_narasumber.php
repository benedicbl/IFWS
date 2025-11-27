<?php
require_once '../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die('Akses ditolak.');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'] ?? '';
    $email = $_POST['email'] ?? '';

    // Validasi sederhana
    if (empty($nama) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: /projek-ifws/PIC/admin_datanarsum.php?status=gagal_input');
        exit();
    }

    // Cek apakah email sudah ada
    $checkQuery = "SELECT id FROM narasumber WHERE email = ?";
    $stmtCheck = mysqli_prepare($koneksi, $checkQuery);
    mysqli_stmt_bind_param($stmtCheck, "s", $email);
    mysqli_stmt_execute($stmtCheck);
    mysqli_stmt_store_result($stmtCheck);
    
    if (mysqli_stmt_num_rows($stmtCheck) > 0) {
        header('Location: /projek-ifws/PIC/admin_datanarsum.php?status=email_duplikat');
        exit();
    }
    mysqli_stmt_close($stmtCheck);
    
    // Jika aman, masukkan data baru
    $query = "INSERT INTO narasumber (nama, email) VALUES (?, ?)";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "ss", $nama, $email);

    if (mysqli_stmt_execute($stmt)) {
        header('Location: /projek-ifws/PIC/admin_datanarsum.php?status=tambah_sukses');
    } else {
        header('Location: /projek-ifws/PIC/admin_datanarsum.php?status=gagal');
    }
    mysqli_stmt_close($stmt);
    exit();
}
?>