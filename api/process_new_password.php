<?php
require_once '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $token = mysqli_real_escape_string($koneksi, $_POST['token']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // 1. Cek Kesamaan Password
    if ($password !== $confirm_password) {
        header("Location: ../kata_sandi_baru.php?token=$token&email=$email&error=Konfirmasi kata sandi tidak cocok!");
        exit();
    }

    // 2. Cek Token Valid (Lagi)
    $check = "SELECT id FROM peserta WHERE email = '$email' AND reset_token = '$token' AND reset_expires > NOW()";
    $res = mysqli_query($koneksi, $check);

    if (mysqli_num_rows($res) > 0) {
        // 3. Update Password
        // Password disimpan string biasa sesuai request sebelumnya
        $update = "UPDATE peserta SET password = '$password', reset_token = NULL, reset_expires = NULL WHERE email = '$email'";
        
        if (mysqli_query($koneksi, $update)) {
            // SUKSES: Arahkan ke login
            header("Location: ../login.php?success=Kata sandi berhasil di atur ulang. Silahkan login.");
            exit();
        } else {
            header("Location: ../kata_sandi_baru.php?token=$token&email=$email&error=Terjadi kesalahan sistem.");
            exit();
        }
    } else {
        header("Location: ../login.php?error=Link sudah kadaluarsa atau tidak valid.");
        exit();
    }
}
?>