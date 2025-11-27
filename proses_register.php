<?php
session_start();

// --- KONEKSI DATABASE ---
// Sesuaikan dengan settingan database kamu
$servername = "localhost";
$username = "root";
$password_db = ""; // Ganti sesuai password database lokal
$dbname = "ifws_db"; 

$conn = new mysqli($servername, $username, $password_db, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
// ------------------------

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil & Bersihkan Input
    $nama = mysqli_real_escape_string($conn, trim($_POST['nama_lengkap']));
    $npm = mysqli_real_escape_string($conn, trim($_POST['npm']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    
    // Ambil password langsung (Tanpa hashing untuk development)
    $password_input = $_POST['password']; 
    $confirm_password = $_POST['confirm_password'];

    // 1. Validasi Password Match
    if ($password_input !== $confirm_password) {
        header("Location: register.php?error=Password dan Konfirmasi Password tidak cocok!");
        exit();
    }

    // 2. Cek Email duplikat
    $checkEmail = "SELECT id FROM peserta WHERE email = '$email'";
    $resultEmail = $conn->query($checkEmail);
    if ($resultEmail->num_rows > 0) {
        header("Location: register.php?error=Email sudah terdaftar!");
        exit();
    }

    // 3. Cek NPM duplikat
    $checkNPM = "SELECT id FROM peserta WHERE npm = '$npm'";
    $resultNPM = $conn->query($checkNPM);
    if ($resultNPM->num_rows > 0) {
        header("Location: register.php?error=NPM sudah terdaftar!");
        exit();
    }

    // 4. Insert Data ke Database (Password String Biasa)
    // Perhatikan variabel '$password_input' langsung dimasukkan ke query
    $sql = "INSERT INTO peserta (nama_lengkap, npm, email, password, status_ta) 
            VALUES ('$nama', '$npm', '$email', '$password_input', 'Bukan_TA')";

    if ($conn->query($sql) === TRUE) {
        // Redirect ke login dengan pesan sukses
        header("Location: login.php?error=Registrasi berhasil! Silahkan login dengan akun baru.");
        exit();
    } else {
        header("Location: register.php?error=Terjadi kesalahan sistem: " . $conn->error);
        exit();
    }
} else {
    header("Location: register.php");
    exit();
}
?>