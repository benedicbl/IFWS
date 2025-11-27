<?php
// File: /projek-ifws/api/delete_anggota.php
require_once '../includes/config.php';
session_start();

// 1. Cek apakah user login & admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /projek-ifws/login.php');
    exit();
}

// 2. Cek apakah ada ID yang dikirim
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // 3. Query Delete
    $query = "DELETE FROM anggota_ifws WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        
        if (mysqli_stmt_execute($stmt)) {
            // Sukses -> Redirect dengan status sukses
            header("Location: ../PIC/admin_dataanggota.php?status=hapus_sukses");
        } else {
            // Gagal Query
            header("Location: ../PIC/admin_dataanggota.php?status=hapus_gagal");
        }
        mysqli_stmt_close($stmt);
    } else {
        header("Location: ../PIC/admin_dataanggota.php?status=hapus_gagal");
    }
} else {
    // Jika tidak ada ID
    header("Location: ../PIC/admin_dataanggota.php");
}
exit();
?>