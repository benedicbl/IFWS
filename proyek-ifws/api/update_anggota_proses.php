<?php
header('Content-Type: application/json');
include '../includes/koneksi.php';

$response = ['status' => 'error', 'message' => 'Terjadi kesalahan.'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = isset($_POST['anggotaId']) ? (int)$_POST['anggotaId'] : 0;
    $nama = $_POST['nama'] ?? '';
    $email = $_POST['email'] ?? '';
    $jabatan = $_POST['jabatan'] ?? '';

    if ($id > 0 && !empty($nama) && !empty($email) && !empty($jabatan)) {
        $sql = "UPDATE anggota SET nama = ?, email = ?, jabatan = ? WHERE id = ?";
        $stmt = $koneksi->prepare($sql);

        if ($stmt) {
            // "sssi" berarti String, String, String, Integer
            $stmt->bind_param("sssi", $nama, $email, $jabatan, $id);

            if ($stmt->execute()) {
                $response['status'] = 'success';
                $response['message'] = 'Data anggota berhasil diperbarui!';
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