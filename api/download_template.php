<?php
require_once '../includes/config.php';

// 1. Cek status session sebelum memulai
// Jika session belum aktif, baru kita start. Jika sudah, abaikan.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Validasi Admin (Hanya admin yang boleh download)
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'sekretaris')) {
    header('Location: /projek-ifws/login.php'); 
    exit();
}

// 2. PENTING: Bersihkan Output Buffer
// Fungsi ini akan menghapus semua output sebelumnya (termasuk pesan error/notice session tadi, spasi, atau enter)
// sehingga file yang didownload benar-benar murni CSV.
ob_end_clean(); 

// Set Header agar browser membacanya sebagai file download CSV
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="template_peserta_ifws.csv"');
header('Pragma: no-cache');
header('Expires: 0');

// Buka output stream
$output = fopen('php://output', 'w');

// 3. Tulis Header Kolom
fputcsv($output, array('Nama Lengkap', 'NPM', 'Jenis TA'));

// 4. Tulis Contoh Data
fputcsv($output, array('Budi Santoso', '6182201001', 'TA1--- Bagian Ini Dapat Dihapus/Diganti (Dimulai dari sini)'));
fputcsv($output, array('Siti Aminah', '6182201002', 'TA2 --- Bagian Ini Dapat Dihapus/Diganti'));

fclose($output);
exit();
?>