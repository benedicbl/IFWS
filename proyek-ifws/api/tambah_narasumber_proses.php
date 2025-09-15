<?php
// Mengatur header agar browser tahu bahwa outputnya adalah format JSON
header('Content-Type: application/json');

// Memanggil file koneksi. Path '../' benar karena file ini ada di dalam folder 'api'.
include '../includes/koneksi.php';

// Menyiapkan array default untuk respons jika terjadi error
$response = ['status' => 'error', 'message' => 'Terjadi kesalahan yang tidak diketahui.'];

// Hanya proses jika metode pengiriman adalah POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil data dari form yang dikirim oleh JavaScript
    $nama = isset($_POST['nama']) ? trim($_POST['nama']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';

    // Validasi di sisi server
    if (empty($nama) || empty($email)) {
        $response['message'] = 'Nama dan Email tidak boleh kosong.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Format email tidak valid.';
    } else {
        // Menggunakan PREPARED STATEMENTS untuk mencegah SQL Injection (Sangat Penting!)
        
        // 1. Siapkan query SQL dengan placeholder (?)
        $sql = "INSERT INTO narasumber (nama, email) VALUES (?, ?)";
        $stmt = $koneksi->prepare($sql);

        if ($stmt) {
            // 2. Bind (kaitkan) variabel PHP ke placeholder. "ss" berarti keduanya adalah tipe data String.
            $stmt->bind_param("ss", $nama, $email);

            // 3. Eksekusi query yang sudah aman
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    // Jika berhasil, ubah status dan pesan respons
                    $response['status'] = 'success';
                    $response['message'] = 'Narasumber berhasil ditambahkan!';
                } else {
                    $response['message'] = 'Gagal menambahkan narasumber, tidak ada data yang tersimpan.';
                }
            } else {
                $response['message'] = 'Eksekusi query gagal: ' . $stmt->error;
            }
            // 4. Selalu tutup statement setelah selesai
            $stmt->close();
        } else {
            $response['message'] = 'Gagal mempersiapkan statement: ' . $koneksi->error;
        }
    }
} else {
    // Jika ada yang mencoba mengakses file ini langsung dari browser (bukan via POST)
    $response['message'] = 'Metode request tidak valid. Harap kirim via POST.';
}

// Selalu tutup koneksi database
$koneksi->close();

// Kirim respons kembali ke JavaScript dalam format JSON
echo json_encode($response);
?>