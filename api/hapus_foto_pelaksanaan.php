<?php
require_once '../includes/config.php';
header('Content-Type: application/json'); // Respon JSON

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
    exit();
}

$foto_id = $_POST['foto_id'] ?? null;
$file_path = $_POST['file_path'] ?? null;

if (empty($foto_id) || empty($file_path)) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap.']);
    exit();
}

// Hapus record dari database
$query_delete_db = "DELETE FROM foto_pelaksanaan WHERE id = ?";
$stmt = mysqli_prepare($koneksi, $query_delete_db);
mysqli_stmt_bind_param($stmt, "i", $foto_id);
$success_db = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

// Hapus file dari server
$full_path = '../' . $file_path; // Path relatif dari root proyek
$success_file = false;
if ($success_db && file_exists($full_path)) {
    if (unlink($full_path)) {
        $success_file = true;
    }
} elseif ($success_db) {
    // Jika record DB terhapus tapi file tidak ada, anggap sukses
    $success_file = true;
}

if ($success_db && $success_file) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus foto.']);
}
exit();
?>