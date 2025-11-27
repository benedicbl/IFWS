<?php
require_once '../includes/config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Akses ditolak.');
}

$webinar_id = (int)$_POST['webinar_id'];
$tanggal_direncanakan = $_POST['tanggal_direncanakan'];
$jam_mulai = $_POST['jam_mulai'];
$jam_selesai = $_POST['jam_selesai'];

// Validasi sederhana (pastikan tidak kosong)
if (empty($tanggal_direncanakan) || empty($jam_mulai) || empty($jam_selesai)) {
    header('Location: /projek-ifws/PIC/admin_listifws.php?status=reschedule_gagal');
    exit();
}

$query = "UPDATE webinars SET tanggal_direncanakan = ?, jam_mulai = ?, jam_selesai = ? WHERE id = ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "sssi", $tanggal_direncanakan, $jam_mulai, $jam_selesai, $webinar_id);

if (mysqli_stmt_execute($stmt)) {
    header('Location: /projek-ifws/PIC/admin_listifws.php?status=reschedule_sukses');
} else {
    header('Location: /projek-ifws/PIC/admin_listifws.php?status=reschedule_gagal');
}
mysqli_stmt_close($stmt);
exit();
?>