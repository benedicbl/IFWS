<?php
require_once '../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die('Akses ditolak.');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data narasumber sebagai array
    $narasumber_ids = $_POST['narasumber_ids'] ?? [];
    $tanggal_direncanakan = $_POST['tanggal_direncanakan'];
    $jam_mulai = $_POST['jam_mulai'];
    $jam_selesai = $_POST['jam_selesai'];
    $kategori = $_POST['kategori'];
    $topik = $_POST['topik'];

    if (empty($narasumber_ids) || empty($tanggal_direncanakan) || empty($kategori) || empty($topik)) {
        header('Location: /projek-ifws/PIC/admin_listifws.php?status=gagal_kosong');
        exit();
    }

    // 1. Masukkan data utama ke tabel 'webinars' (tanpa narasumber)
    $query_webinar = "INSERT INTO webinars (tanggal_direncanakan, jam_mulai, jam_selesai, kategori, topik, status) VALUES (?, ?, ?, ?, ?, 'rencana')";
    $stmt_webinar = mysqli_prepare($koneksi, $query_webinar);
    mysqli_stmt_bind_param($stmt_webinar, "sssss", $tanggal_direncanakan, $jam_mulai, $jam_selesai, $kategori, $topik);

    if (mysqli_stmt_execute($stmt_webinar)) {
        // 2. Ambil ID dari webinar yang baru saja dibuat
        $id_webinar_baru = mysqli_insert_id($koneksi);
        mysqli_stmt_close($stmt_webinar);

        // 3. Masukkan relasi webinar dan narasumber ke tabel penghubung
        $query_narsum = "INSERT INTO webinar_narasumber (id_webinar, id_narasumber) VALUES (?, ?)";
        $stmt_narsum = mysqli_prepare($koneksi, $query_narsum);

        foreach ($narasumber_ids as $id_narasumber) {
            mysqli_stmt_bind_param($stmt_narsum, "ii", $id_webinar_baru, $id_narasumber);
            mysqli_stmt_execute($stmt_narsum);
        }
        mysqli_stmt_close($stmt_narsum);

        header('Location: /projek-ifws/PIC/admin_listifws.php?status=tambah_sukses');
    } else {
        header('Location: /projek-ifws/PIC/admin_listifws.php?status=gagal');
    }
    exit();
}
?>