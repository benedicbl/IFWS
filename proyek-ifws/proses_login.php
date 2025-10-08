<?php
// Mulai session di baris paling atas.
session_start();

// Memanggil file koneksi ke database.
include 'includes/koneksi.php';

// Cek apakah form sudah di-submit dengan metode POST.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Ambil data dari form.
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Gunakan prepared statement untuk mencari user berdasarkan username.
    $sql = "SELECT id, email, password, nama, role_id FROM users WHERE email = ?";

    $stmt = $koneksi->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        // --- ALUR LOGIKA YANG DIPERBAIKI ---

        // 1. Cek dulu apakah username ditemukan di database.
        if ($result->num_rows === 1) {
            
            // 2. Jika ditemukan, ambil datanya.
            $user = $result->fetch_assoc();

            // 3. Baru setelah itu, bandingkan passwordnya.
            // Perbandingan string biasa (SANGAT TIDAK AMAN, HANYA UNTUK SEMENTARA)
            if ($password === $user['password'] ) {
                
                // Jika password cocok, login berhasil.
                // Simpan informasi pengguna ke dalam session.
                $_SESSION['loggedin'] = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['nama'] = $user['nama'];
                $_SESSION['role_id'] = $user['role_id'];
                $_SESSION['last_activity'] = time();
                // Arahkan pengguna ke halaman utama.
                switch ($_SESSION['role_id']) {
                    case 1:
                        header("Location: /proyek-ifws/pages/User/homepage_peserta.php");
                        break;
                    case 2:
                        header("Location: /proyek-ifws/pages/PIC/homepage_pic.php");
                        break;
                    case 3:
                        header("Location: /proyek-ifws/pages/Sekretariat/homepage_sekretariat.php");
                        break;
                    case 4:
                        header("Location: /proyek-ifws/pages/Bendahara/homepage_bendahara.php");
                        break;
                    case 5:
                        header("Location: /proyek-ifws/pages/Teknisi/teknisi.php");
                        break;
                    case 6:
                        header("Location: /proyek-ifws/pages/Promosi/promosi.php");
                        break;
                    }
                exit;

            } else {
                // Jika password salah (tapi username benar).
                header("Location: login.php?error=Password salah");
                exit;
            }

        } else {
            // Jika username tidak ditemukan sama sekali di database.
            header("Location: login.php?error=Username tidak ditemukan");
            exit;
        }

        $stmt->close();
    } else {
        // Jika query SQL gagal dipersiapkan.
        header("Location: login.php?error=Terjadi kesalahan pada sistem");
        exit;
    }
    
    $koneksi->close();

} else {
    // Jika file diakses langsung tanpa metode POST, kembalikan ke login.
    header("Location: login.php");
    exit;
}
?>