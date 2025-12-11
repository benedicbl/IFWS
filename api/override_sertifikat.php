<?php
require_once '../includes/config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'sekretaris' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
    exit();
}

// Ambil ID dari tabel 'kehadiran'
$kehadiran_id = (int)($_POST['id_kehadiran'] ?? 0);

if (empty($kehadiran_id)) {
    echo json_encode(['status' => 'error', 'message' => 'ID Kehadiran tidak valid.']);
    exit();
}

// Update status_kehadiran menjadi 'hadir' dan set override_sertifikat = 1
$query = "UPDATE kehadiran SET status_kehadiran = 'hadir', override_sertifikat = 1 WHERE id = ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $kehadiran_id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['status' => 'success', 'message' => 'Status berhasil di-override. Peserta ini sekarang akan menerima sertifikat.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal mengupdate database.']);
}
mysqli_stmt_close($stmt);
exit();
?>