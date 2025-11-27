<?php
require_once '../includes/config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teknisi') {
    die('Akses ditolak.');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $webinar_id = (int)$_POST['webinar_id'];
    $link_akses = trim($_POST['link_akses']); // Ambil link dan hapus spasi ekstra

    // -- PERUBAHAN DI SINI --
    // Jika link tidak kosong dan tidak memiliki awalan http/https, tambahkan https:// secara default
    if (!empty($link_akses) && !preg_match("~^(?:f|ht)tps?://~i", $link_akses)) {
        $link_akses = "https://" . $link_akses;
    }

    $query = "UPDATE webinars SET link_akses = ? WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "si", $link_akses, $webinar_id);

    if (mysqli_stmt_execute($stmt)) {
        header('Location: /projek-ifws/Teknisi/teknisi_datawebinar.php?status=sukses');
    } else {
        header('Location: /projek-ifws/Teknisi/teknisi_datawebinar.php?status=gagal');
    }
    mysqli_stmt_close($stmt);
    exit();
}