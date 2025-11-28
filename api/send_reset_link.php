<?php
session_start();
require_once '../includes/config.php';

// Load PHPMailer
require '../includes/PHPMailer/Exception.php';
require '../includes/PHPMailer/PHPMailer.php';
require '../includes/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);

    // 1. Cek Email
    $query = "SELECT id FROM peserta WHERE email = '$email'";
    $result = mysqli_query($koneksi, $query);

    if (mysqli_num_rows($result) > 0) {

        $token = bin2hex(random_bytes(32)); // Menghasilkan string acak panjang

        $update = "UPDATE peserta SET reset_token = '$token', reset_expires = (NOW() + INTERVAL 30 MINUTE) WHERE email = '$email'";
        mysqli_query($koneksi, $update);

        // 4. Siapkan Link Reset
        // GANTI 'localhost/projek-ifws' dengan domain asli kamu jika sudah hosting
        $base_url = "http://localhost/projek-ifws"; 
        $reset_link = $base_url . "/kata_sandi_baru.php?token=" . $token . "&email=" . urlencode($email);

        // 5. Kirim Email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'lesmanabenny89@gmail.com'; // <--- GANTI INI
            $mail->Password   = 'ayau tmjr ddaa adok';    // <--- GANTI INI
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('no-reply@ifws.com', 'Admin IFWS');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Atur Ulang Kata Sandi - IFWS';
            $mail->Body    = "
                <h3>Permintaan Atur Ulang Kata Sandi</h3>
                <p>Klik link di bawah ini untuk membuat kata sandi baru:</p>
                <p><a href='$reset_link' style='background:#2563eb; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>Atur Ulang Password</a></p>
                <p>Atau copy link ini: <br> $reset_link</p>
                <p>Link ini berlaku selama 30 menit.</p>
            ";

            $mail->send();
            
            // SUKSES: Redirect ke Login dengan pesan hijau
            header("Location: ../login.php?success=Link Atur Ulang Kata Sandi telah dikirimkan ke email anda.");
            exit();

        } catch (Exception $e) {
            header("Location: ../lupa_password.php?error=Gagal mengirim email. Cek koneksi server.");
            exit();
        }
    } else {
        // ERROR: Email tidak ada
        header("Location: ../lupa_password.php?error=Email tidak ditemukan!");
        exit();
    }
}
?>