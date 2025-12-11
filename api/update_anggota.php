<?php
require_once '../includes/config.php';

// Proteksi
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Akses ditolak.');
}

// Ambil data dari form
$anggota_id = (int)$_POST['anggota_id'];
$nama_lengkap = $_POST['nama_lengkap'] ?? '';
$role = $_POST['role'] ?? '';
$email = $_POST['email'] ?? '';

// Validasi dasar
if (empty($anggota_id) || empty($nama_lengkap) || empty($role) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['form_error_edit'] = "Input tidak valid. Pastikan semua field terisi dan email benar.";
    $_SESSION['form_data_edit'] = $_POST; // Simpan data input
    header('Location: /projek-ifws/PIC/admin_dataanggota.php?edit_error=1&id=' . $anggota_id); // Kembali ke halaman, tandai error
    exit();
}

// Cek duplikasi email (untuk anggota LAIN)
$checkQuery = "SELECT id FROM anggota_ifws WHERE email = ? AND id != ?";
$stmtCheck = mysqli_prepare($koneksi, $checkQuery);
mysqli_stmt_bind_param($stmtCheck, "si", $email, $anggota_id);
mysqli_stmt_execute($stmtCheck);
mysqli_stmt_store_result($stmtCheck);

if (mysqli_stmt_num_rows($stmtCheck) > 0) {
    $_SESSION['form_error_edit'] = "Email '" . htmlspecialchars($email) . "' sudah digunakan oleh anggota lain.";
    $_SESSION['form_data_edit'] = $_POST; // Simpan data input
    header('Location: /projek-ifws/PIC/admin_dataanggota.php?edit_error=1&id=' . $anggota_id); // Kembali ke halaman, tandai error
    exit();
}
mysqli_stmt_close($stmtCheck);

// Update data di tabel anggota_ifws
$query = "UPDATE anggota_ifws SET nama_lengkap = ?, email = ?, role = ? WHERE id = ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "sssi", $nama_lengkap, $email, $role, $anggota_id);

if (mysqli_stmt_execute($stmt)) {
    header('Location: /projek-ifws/PIC/admin_dataanggota.php?status=update_sukses');
} else {
    header('Location: /projek-ifws/PIC/admin_dataanggota.php?status=update_gagal');
}
mysqli_stmt_close($stmt);
exit();
?>