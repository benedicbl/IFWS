<?php
require_once '../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die('Akses ditolak.');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_lengkap = $_POST['nama_lengkap'] ?? '';
    $role = $_POST['role'] ?? '';
    $email = $_POST['email'] ?? '';

    // Validasi dasar
    if (empty($nama_lengkap) || empty($role) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: /projek-ifws/PIC/admin_dataanggota.php?status=gagal_input');
        exit();
    }

    // Cek duplikasi email
    $checkQuery = "SELECT id FROM anggota_ifws WHERE email = ?";
    $stmtCheck = mysqli_prepare($koneksi, $checkQuery);
    mysqli_stmt_bind_param($stmtCheck, "s", $email);
    mysqli_stmt_execute($stmtCheck);
    mysqli_stmt_store_result($stmtCheck);
    
    if (mysqli_stmt_num_rows($stmtCheck) > 0) {
        // --- PERUBAHAN DI SINI ---
        // Simpan pesan error dan data form ke session
        $_SESSION['form_error'] = "Email '" . htmlspecialchars($email) . "' sudah terdaftar. Silakan gunakan email lain.";
        $_SESSION['form_data'] = [
            'nama_lengkap' => $nama_lengkap,
            'role' => $role
        ];
        // Redirect kembali ke halaman anggota
        header('Location: /projek-ifws/PIC/admin_dataanggota.php');
        exit();
    }
    mysqli_stmt_close($stmtCheck);
    
    // Generate password acak
    $password = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyz'), 0, 8);

    // Gunakan transaction untuk memastikan kedua query berhasil
    mysqli_begin_transaction($koneksi);
    try {
        // 1. Insert ke tabel anggota_ifws
        $query1 = "INSERT INTO anggota_ifws (nama_lengkap, role, email) VALUES (?, ?, ?)";
        $stmt1 = mysqli_prepare($koneksi, $query1);
        mysqli_stmt_bind_param($stmt1, "sss", $nama_lengkap, $role, $email);
        mysqli_stmt_execute($stmt1);
        $id_anggota_baru = mysqli_insert_id($koneksi);
        mysqli_stmt_close($stmt1);
        
        // 2. Insert ke tabel users
        $query2 = "INSERT INTO users (id_anggota, password) VALUES (?, ?)";
        $stmt2 = mysqli_prepare($koneksi, $query2);
        mysqli_stmt_bind_param($stmt2, "is", $id_anggota_baru, $password);
        mysqli_stmt_execute($stmt2);
        mysqli_stmt_close($stmt2);

        mysqli_commit($koneksi);
        header('Location: /projek-ifws/PIC/admin_dataanggota.php?status=tambah_sukses');

    } catch (mysqli_sql_exception $exception) {
        mysqli_rollback($koneksi);
        header('Location: /projek-ifws/PIC/admin_dataanggota.php?status=gagal');
    }
    
    exit();
}
?>