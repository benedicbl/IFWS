<?php
require_once '../includes/config.php';

// Proteksi
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Akses ditolak.');
}

// Ambil data dari form
$narasumber_id = (int)$_POST['narasumber_id'];
$nama = $_POST['nama'] ?? '';
$email = $_POST['email'] ?? '';

// Validasi dasar
if (empty($narasumber_id) || empty($nama) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['form_error'] = "Input tidak valid. Pastikan semua field terisi dan email benar.";
    $_SESSION['form_data'] = $_POST; // Simpan data input
    header('Location: /projek-ifws/PIC/admin_datanarsum.php?edit_error=1&id=' . $narasumber_id); // Kembali ke halaman, tandai error
    exit();
}

// Cek duplikasi email (untuk narasumber LAIN)
$checkQuery = "SELECT id FROM narasumber WHERE email = ? AND id != ?";
$stmtCheck = mysqli_prepare($koneksi, $checkQuery);
mysqli_stmt_bind_param($stmtCheck, "si", $email, $narasumber_id);
mysqli_stmt_execute($stmtCheck);
mysqli_stmt_store_result($stmtCheck);

if (mysqli_stmt_num_rows($stmtCheck) > 0) {
    $_SESSION['form_error'] = "Email '" . htmlspecialchars($email) . "' sudah digunakan oleh narasumber lain.";
    $_SESSION['form_data'] = $_POST; // Simpan data input
    header('Location: /projek-ifws/PIC/admin_datanarsum.php?edit_error=1&id=' . $narasumber_id); // Kembali ke halaman, tandai error
    exit();
}
mysqli_stmt_close($stmtCheck);

// Update data di tabel narasumber
$query = "UPDATE narasumber SET nama = ?, email = ? WHERE id = ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "ssi", $nama, $email, $narasumber_id);

if (mysqli_stmt_execute($stmt)) {
    header('Location: /projek-ifws/PIC/admin_datanarsum.php?status=update_sukses');
} else {
    header('Location: /projek-ifws/PIC/admin_datanarsum.php?status=update_gagal');
}
mysqli_stmt_close($stmt);
exit();
?>