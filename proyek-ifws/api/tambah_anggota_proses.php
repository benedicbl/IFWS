<?php
header('Content-Type: application/json');
include '../includes/koneksi.php';

$response = ['status' => 'error', 'message' => 'Terjadi kesalahan.'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $nama = $_POST['nama'] ?? '';
    $email = $_POST['email'] ?? '';
    $jabatan = $_POST['jabatan'] ?? '';

    // Validasi sederhana
    if (!empty($nama) && !empty($email) && !empty($jabatan)) {
        
        $sql = "INSERT INTO anggota (nama, email, jabatan) VALUES (?, ?, ?)";
        $stmt = $koneksi->prepare($sql);

        if ($stmt) {
            // "sss" berarti ketiga variabel adalah String
            $stmt->bind_param("sss", $nama, $email, $jabatan);

            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    $response['status'] = 'success';
                    $response['message'] = 'Anggota baru berhasil ditambahkan!';
                } else {
                    $response['message'] = 'Gagal menambahkan anggota, tidak ada data yang tersimpan.';
                }
            } else {
                $response['message'] = 'Eksekusi query gagal: ' . $stmt->error;
            }
            $stmt->close();
        } else {
            $response['message'] = 'Gagal mempersiapkan statement: ' . $koneksi->error;
        }
    } else {
        $response['message'] = 'Semua field wajib diisi.';
    }
} else {
    $response['message'] = 'Metode request tidak valid.';
}

$koneksi->close();
echo json_encode($response);
?>