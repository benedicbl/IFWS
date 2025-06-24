<?php
// Mengatur header agar output berupa JSON
header('Content-Type: application/json');

// Memanggil file koneksi
include '../includes/koneksi.php';

// Inisialisasi array untuk respons
$response = ['status' => 'error', 'message' => 'Terjadi kesalahan yang tidak diketahui.'];

// Cek apakah request yang datang adalah POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil data dari form. Lakukan sanitasi dasar.
    $nama = isset($_POST['nama']) ? trim($_POST['nama']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';

    // Validasi sederhana
    if (empty($nama) || empty($email)) {
        $response['message'] = 'Nama dan Email tidak boleh kosong.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Format email tidak valid.';
    } else {
        // Menggunakan PREPARED STATEMENTS untuk keamanan (mencegah SQL Injection)
        // Ini adalah cara yang paling aman untuk memasukkan data ke database.
        
        // 1. Siapkan query dengan placeholder (?)
        $sql = "INSERT INTO narasumber (nama, email) VALUES (?, ?)";
        $stmt = $koneksi->prepare($sql);

        if ($stmt) {
            // 2. Bind variabel ke placeholder. "ss" berarti kedua variabel adalah string.
            $stmt->bind_param("ss", $nama, $email);

            // 3. Eksekusi statement
            if ($stmt->execute()) {
                // Cek apakah ada baris yang terpengaruh (berarti data berhasil masuk)
                if ($stmt->affected_rows > 0) {
                    $response['status'] = 'success';
                    $response['message'] = 'Narasumber berhasil ditambahkan!';
                } else {
                    $response['message'] = 'Gagal menambahkan narasumber, tidak ada baris yang terpengaruh.';
                }
            } else {
                $response['message'] = 'Eksekusi query gagal: ' . $stmt->error;
            }
            // 4. Tutup statement
            $stmt->close();
        } else {
            $response['message'] = 'Gagal mempersiapkan statement: ' . $koneksi->error;
        }
    }
} else {
    $response['message'] = 'Metode request tidak valid.';
}

// Menutup koneksi database
$koneksi->close();

// Mengembalikan respons dalam format JSON
echo json_encode($response);
?>