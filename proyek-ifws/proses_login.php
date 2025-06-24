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
    $sql = "SELECT id, username, password, nama_lengkap FROM users WHERE username = ?";
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
            if ($password === $user['password']) {
                
                // Jika password cocok, login berhasil.
                // Simpan informasi pengguna ke dalam session.
                $_SESSION['loggedin'] = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
                $_SESSION['last_activity'] = time();
                // Arahkan pengguna ke halaman utama.
                header("Location: index.php");
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