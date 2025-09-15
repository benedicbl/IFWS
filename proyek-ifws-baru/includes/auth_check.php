<?php
// Memulai session hanya jika belum ada session yang aktif.
// Ini adalah cara aman untuk memastikan session_start() tidak dipanggil berulang kali.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- LOGIKA SESSION TIMEOUT ---
$timeout_duration = 1800; // Durasi timeout dalam detik. 1800 detik = 30 menit.

// Cek apakah session 'loggedin' ada dan bernilai true
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    
    // Cek apakah waktu aktivitas terakhir sudah di-set
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
        
        // Jika waktu inactivity sudah melebihi durasi timeout, hancurkan session
        session_unset();     // Hapus semua variabel session
        session_destroy();   // Hancurkan session-nya
        
        // Arahkan kembali ke halaman login dengan pesan
        header("location: /proyek-ifws/login.php?error=Sesi Anda telah berakhir, silakan login kembali.");
        exit;
    }
    
    // Jika sesi masih aktif, perbarui waktu aktivitas terakhir ke waktu saat ini
    $_SESSION['last_activity'] = time();

} else {
    // Jika tidak ada session 'loggedin' atau nilainya bukan true,
    // paksa pengguna kembali ke halaman login.
    header("location: /proyek-ifws/login.php");
    exit;
}
?>