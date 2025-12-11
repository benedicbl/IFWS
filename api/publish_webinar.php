<?php
require_once '../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die('Akses ditolak.');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['webinar_id'])) {
    $id = (int)$_POST['webinar_id'];

    $query = "UPDATE webinars SET status = 'published' WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        header('Location: ../PIC/admin_listifws.php?status=publish_sukses');
    } else {
        header('Location: ../PIC/admin_listifws.php?status=gagal');
    }
    mysqli_stmt_close($stmt);
    exit();
} else {
    header('Location: ../PIC/admin_listifws.php');
    exit();
}
?>