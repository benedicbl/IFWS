<?php
require 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // 1. Cek di tabel STAF (anggota_ifws & users)
    $query_staff = "SELECT a.id, a.email, u.password, a.role 
                    FROM anggota_ifws a 
                    JOIN users u ON a.id = u.id_anggota 
                    WHERE a.email = ?";
              
    $stmt_staff = mysqli_prepare($koneksi, $query_staff);
    mysqli_stmt_bind_param($stmt_staff, "s", $email);
    mysqli_stmt_execute($stmt_staff);
    $result_staff = mysqli_stmt_get_result($stmt_staff);

    if ($user_staff = mysqli_fetch_assoc($result_staff)) {
        // Cek password staf
        if ($password === $user_staff['password']) {
            $_SESSION['user_id'] = $user_staff['id'];
            $_SESSION['email'] = $user_staff['email'];
            $_SESSION['role'] = $user_staff['role'];

            switch ($user_staff['role']) {
                case 'admin': header('Location: /projek-ifws/PIC/admin.php'); break;
                case 'teknisi': header('Location: /projek-ifws/Teknisi/teknisi.php'); break;
                case 'promosi': header('Location: /projek-ifws/Promosi/promosi.php'); break;
                case 'sekretaris': header('Location: /projek-ifws/Sekretaris/sekretaris.php'); break;
                case 'bendahara': header('Location: /projek-ifws/Bendahara/bendahara.php'); break;
                default: header('Location: login.php?error=1'); break;
            }
            exit();
        }
    }

    // 2. Jika tidak ditemukan di STAF, cek di tabel PESERTA
    $query_peserta = "SELECT id, email, password, nama_lengkap FROM peserta WHERE email = ?";
    $stmt_peserta = mysqli_prepare($koneksi, $query_peserta);
    mysqli_stmt_bind_param($stmt_peserta, "s", $email);
    mysqli_stmt_execute($stmt_peserta);
    $result_peserta = mysqli_stmt_get_result($stmt_peserta);

    if ($user_peserta = mysqli_fetch_assoc($result_peserta)) {
        // Cek password peserta
        if ($password === $user_peserta['password']) {
            $_SESSION['peserta_id'] = $user_peserta['id'];
            $_SESSION['peserta_email'] = $user_peserta['email'];
            $_SESSION['peserta_nama'] = $user_peserta['nama_lengkap'];
            header('Location: /projek-ifws/Peserta/dashboard.php');
            exit();
        }
    }
    
    // Jika tidak ditemukan di keduanya atau password salah
    header('Location: login.php?error=1');
    exit();
}
?>