<?php
header('Content-Type: application/json');
include '../includes/koneksi.php';

$response = ['status' => 'error', 'message' => 'Invalid request.'];
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

if ($data && isset($data['id_webinar']) && isset($data['peserta'])) {
    $id_webinar = (int)$data['id_webinar'];
    $peserta = $data['peserta'];

    $koneksi->begin_transaction();
    try {
        $stmt_delete = $koneksi->prepare("DELETE FROM webinar_peserta WHERE id_webinar = ?");
        $stmt_delete->bind_param("i", $id_webinar);
        $stmt_delete->execute();
        $stmt_delete->close();

        if (!empty($peserta)) {
            $sql_insert = "INSERT INTO webinar_peserta (id_webinar, id_narasumber, peran) VALUES (?, ?, ?)";
            $stmt_insert = $koneksi->prepare($sql_insert);
            foreach ($peserta as $p) {
                $id_narasumber = (int)$p['id'];
                $peran = $p['peran'];
                $stmt_insert->bind_param("iis", $id_webinar, $id_narasumber, $peran);
                $stmt_insert->execute();
            }
            $stmt_insert->close();
        }
        
        $koneksi->commit();
        $response = ['status' => 'success', 'message' => 'Daftar peserta webinar berhasil diperbarui!'];
    } catch (Exception $e) {
        $koneksi->rollback();
        $response = ['status' => 'error', 'message' => 'Gagal menyimpan peserta: ' . $e->getMessage()];
    }
}

$koneksi->close();
echo json_encode($response);
?>