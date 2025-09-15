<?php
header('Content-Type: application/json');
include '../includes/koneksi.php';

$response = ['status' => 'error', 'message' => 'Terjadi kesalahan.'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if ($id > 0) {
        // Karena ada ON DELETE CASCADE di database,
        // kita hanya perlu menghapus data dari tabel 'webinar'.
        // Data di 'webinar_peserta' dan 'screenshot' akan terhapus otomatis.
        $sql = "DELETE FROM webinar WHERE id = ?";
        $stmt = $koneksi->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    $response['status'] = 'success';
                    $response['message'] = 'Webinar berhasil dihapus.';
                } else {
                    $response['message'] = 'Webinar tidak ditemukan atau sudah dihapus.';
                }
            } else {
                $response['message'] = 'Gagal mengeksekusi query: ' . $stmt->error;
            }
            $stmt->close();
        } else {
            $response['message'] = 'Gagal mempersiapkan statement: ' . $koneksi->error;
        }
    } else {
        $response['message'] = 'ID webinar tidak valid.';
    }
} else {
    $response['message'] = 'Metode request tidak valid.';
}

$koneksi->close();
echo json_encode($response);
?>