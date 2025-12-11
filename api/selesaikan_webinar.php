<?php
require_once '../includes/config.php';

// Proteksi: Pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die('Akses ditolak.');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['webinar_id'])) {
    $webinar_id = (int)$_POST['webinar_id'];

    // Query untuk mengubah status menjadi 'finished'
    $query = "UPDATE webinars SET status = 'finished' WHERE id = ?";
    
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "i", $webinar_id);

    if (mysqli_stmt_execute($stmt)) {
        // Jika berhasil, kembali ke halaman list
        header('Location: /projek-ifws/PIC/admin_listifws.php?status=selesai_sukses');
    } else {
        // Jika gagal
        header('Location: /projek-ifws/PIC/admin_listifws.php?status=selesai_gagal');
    }
    mysqli_stmt_close($stmt);
    exit();

} else {
    // Jika diakses langsung, kembalikan
    header('Location: /projek-ifws/PIC/admin_listifws.php');
    exit();
}
?>