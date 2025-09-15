<?php
header('Content-Type: application/json');
include '../includes/koneksi.php';

$response = ['status' => 'error', 'message' => 'Terjadi kesalahan.'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = isset($_POST['narasumberId']) ? (int)$_POST['narasumberId'] : 0;
    $nama = isset($_POST['nama']) ? trim($_POST['nama']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';

    if ($id > 0 && !empty($nama) && !empty($email)) {
        // Gunakan prepared statement untuk UPDATE
        $sql = "UPDATE narasumber SET nama = ?, email = ? WHERE id = ?";
        $stmt = $koneksi->prepare($sql);

        if ($stmt) {
            // Bind parameter. "ssi" berarti String, String, Integer.
            $stmt->bind_param("ssi", $nama, $email, $id);

            if ($stmt->execute()) {
                $response['status'] = 'success';
                $response['message'] = 'Data narasumber berhasil diperbarui!';
            } else {
                $response['message'] = 'Gagal mengeksekusi query: ' . $stmt->error;
            }
            $stmt->close();
        } else {
            $response['message'] = 'Gagal mempersiapkan statement: ' . $koneksi->error;
        }
    } else {
        $response['message'] = 'Data tidak lengkap.';
    }
} else {
    $response['message'] = 'Metode request tidak valid.';
}

$koneksi->close();
echo json_encode($response);
?>