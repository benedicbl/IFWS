<?php
// Pengaturan untuk koneksi ke database
$db_host = 'localhost';     // Biasanya 'localhost'
$db_user = 'root';          // User default XAMPP
$db_pass = '';              // Password default XAMPP (kosong)
$db_name = 'IFWS';       // Nama database yang kita buat

// Membuat koneksi menggunakan mysqli
$koneksi = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Cek koneksi
// Jika koneksi gagal, hentikan skrip dan tampilkan pesan error
if ($koneksi->connect_error) {
    die("Koneksi ke database gagal: " . $koneksi->connect_error);
}

// Mengatur character set agar tidak ada masalah dengan karakter aneh
$koneksi->set_charset("utf8mb4");
?>