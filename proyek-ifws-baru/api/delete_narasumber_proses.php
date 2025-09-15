<?php
header('Content-Type: application/json');
include '../includes/koneksi.php';

$response = ['status' => 'error', 'message' => 'Terjadi kesalahan.'];

// Pastikan metode pengiriman adalah POST untuk keamanan
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil id dari data yang dikirim
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if ($id > 0) {
        // Gunakan prepared statement untuk DELETE
        $sql = "DELETE FROM narasumber WHERE id = ?";
        $stmt = $koneksi->prepare($sql);

        if ($stmt) {
            // Bind parameter id
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                // Cek apakah ada baris yang terhapus
                if ($stmt->affected_rows > 0) {
                    $response['status'] = 'success';
                    $response['message'] = 'Narasumber berhasil dihapus.';
                } else {
                    $response['message'] = 'Narasumber tidak ditemukan atau sudah dihapus.';
                }
            } else {
                $response['message'] = 'Gagal mengeksekusi query: ' . $stmt->error;
            }
            $stmt->close();
        } else {
            $response['message'] = 'Gagal mempersiapkan statement: ' . $koneksi->error;
        }
    } else {
        $response['message'] = 'ID narasumber tidak valid.';
    }
} else {
    $response['message'] = 'Metode request tidak valid.';
}

$koneksi->close();
echo json_encode($response);
?>