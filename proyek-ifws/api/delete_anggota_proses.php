<?php
header('Content-Type: application/json');
include '../includes/koneksi.php';

$response = ['status' => 'error', 'message' => 'Terjadi kesalahan.'];

// Pastikan metode pengiriman adalah POST untuk keamanan
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil id dari data yang dikirim oleh JavaScript
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if ($id > 0) {
        // Gunakan prepared statement untuk DELETE
        $sql = "DELETE FROM anggota WHERE id = ?";
        $stmt = $koneksi->prepare($sql);

        if ($stmt) {
            // Bind parameter id (tipe data integer)
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                // Cek apakah ada baris yang terhapus
                if ($stmt->affected_rows > 0) {
                    $response['status'] = 'success';
                    $response['message'] = 'Data anggota berhasil dihapus.';
                } else {
                    $response['message'] = 'Anggota tidak ditemukan atau sudah dihapus.';
                }
            } else {
                $response['message'] = 'Gagal mengeksekusi query: ' . $stmt->error;
            }
            $stmt->close();
        } else {
            $response['message'] = 'Gagal mempersiapkan statement: ' . $koneksi->error;
        }
    } else {
        $response['message'] = 'ID anggota tidak valid.';
    }
} else {
    $response['message'] = 'Metode request tidak valid.';
}

$koneksi->close();
echo json_encode($response);
?>