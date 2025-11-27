<?php
require_once '../includes/config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Akses ditolak.');
}

// Ambil semua data dari form
$webinar_id = (int)$_POST['webinar_id'];
$narasumber_ids = $_POST['narasumber_ids'] ?? [];
$tanggal_direncanakan = $_POST['tanggal_direncanakan'];
$jam_mulai = $_POST['jam_mulai'];
$jam_selesai = $_POST['jam_selesai'];
$kategori = $_POST['kategori'];
$topik = $_POST['topik'];

// 1. Update data utama di tabel 'webinars'
$query_update = "UPDATE webinars SET tanggal_direncanakan=?, jam_mulai=?, jam_selesai=?, kategori=?, topik=? WHERE id=?";
$stmt_update = mysqli_prepare($koneksi, $query_update);
mysqli_stmt_bind_param($stmt_update, "sssssi", $tanggal_direncanakan, $jam_mulai, $jam_selesai, $kategori, $topik, $webinar_id);
mysqli_stmt_execute($stmt_update);
mysqli_stmt_close($stmt_update);

// 2. Hapus relasi narasumber yang lama
$query_delete_narsum = "DELETE FROM webinar_narasumber WHERE id_webinar = ?";
$stmt_delete_narsum = mysqli_prepare($koneksi, $query_delete_narsum);
mysqli_stmt_bind_param($stmt_delete_narsum, "i", $webinar_id);
mysqli_stmt_execute($stmt_delete_narsum);
mysqli_stmt_close($stmt_delete_narsum);

// 3. Masukkan relasi narasumber yang baru
if (!empty($narasumber_ids)) {
    $query_insert_narsum = "INSERT INTO webinar_narasumber (id_webinar, id_narasumber) VALUES (?, ?)";
    $stmt_insert_narsum = mysqli_prepare($koneksi, $query_insert_narsum);
    foreach ($narasumber_ids as $id_narasumber) {
        mysqli_stmt_bind_param($stmt_insert_narsum, "ii", $webinar_id, $id_narasumber);
        mysqli_stmt_execute($stmt_insert_narsum);
    }
    mysqli_stmt_close($stmt_insert_narsum);
}

header('Location: /projek-ifws/PIC/admin_listifws.php?status=update_sukses');
exit();
?>