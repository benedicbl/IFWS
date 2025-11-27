<?php
// Mulai session di file config agar tidak lupa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Pengaturan koneksi database
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'ifws_db';

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>