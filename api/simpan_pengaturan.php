<?php
require_once '../includes/config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teknisi') {
    die('Akses ditolak.');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $min_duration = (int)$_POST['min_duration'];
    $min_ifws_ta = (int)$_POST['min_ifws_ta'];

    // Query untuk update atau insert jika belum ada
    $query = "INSERT INTO settings (setting_key, setting_value) VALUES (?, ?), (?, ?)
              ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)";
    
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "sisi", $key1, $min_duration, $key2, $min_ifws_ta);

    $key1 = 'min_duration';
    $key2 = 'min_ifws_ta';

    if (mysqli_stmt_execute($stmt)) {
        header('Location: /projek-ifws/Teknisi/teknisi_pengaturanifws.php?status=sukses');
    } else {
        header('Location: /projek-ifws/Teknisi/teknisi_pengaturanifws.php?status=gagal');
    }
    mysqli_stmt_close($stmt);
    exit();
}