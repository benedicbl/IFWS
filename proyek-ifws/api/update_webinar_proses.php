<?php
header('Content-Type: application/json');
include '../includes/koneksi.php';

$response = ['status' => 'error', 'message' => 'Terjadi kesalahan.'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = isset($_POST['webinarId']) ? (int)$_POST['webinarId'] : 0;
    $tanggal = $_POST['tanggal'] ?? '';
    $id_jenis_ifws = isset($_POST['id_jenis_ifws']) ? (int)$_POST['id_jenis_ifws'] : 0;
    $jam_mulai = $_POST['jam-mulai'] ?? '';
    $jam_selesai = $_POST['jam-selesai'] ?? '';
    $topik = $_POST['topik'] ?? '';

    if ($id > 0 && !empty($tanggal) && $id_jenis_ifws > 0 && !empty($jam_mulai) && !empty($topik)) {
        $sql = "UPDATE webinar SET tanggal = ?, id_jenis_ifws = ?, jam_mulai = ?, jam_selesai = ?, topik = ? WHERE id = ?";
        $stmt = $koneksi->prepare($sql);

        if ($stmt) {
            // Tipe: s (tanggal), i (id_jenis), s (mulai), s (selesai), s (topik), i (id)
            $stmt->bind_param("sisssi", $tanggal, $id_jenis_ifws, $jam_mulai, $jam_selesai, $topik, $id);

            if ($stmt->execute()) {
                $response['status'] = 'success';
                $response['message'] = 'Data webinar berhasil diperbarui!';
            } else {
                $response['message'] = 'Gagal memperbarui data webinar: ' . $stmt->error;
            }
            $stmt->close();
        } else {
            $response['message'] = 'Gagal mempersiapkan statement: ' . $koneksi->error;
        }
    } else {
        $response['message'] = 'Data form tidak lengkap.';
    }
} else {
    $response['message'] = 'Metode request tidak valid.';
}

$koneksi->close();
echo json_encode($response);
?>