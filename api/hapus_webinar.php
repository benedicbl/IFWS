<?php
require_once '../includes/config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Akses ditolak.');
}

$webinar_id = (int)$_POST['webinar_id'];

$query = "DELETE FROM webinars WHERE id = ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $webinar_id);

if (mysqli_stmt_execute($stmt)) {
    header('Location: /projek-ifws/PIC/admin_listifws.php?status=hapus_sukses');
} else {
    header('Location: /projek-ifws/PIC/admin_listifws.php?status=hapus_gagal');
}
mysqli_stmt_close($stmt);
exit();
?>