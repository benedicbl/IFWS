<?php
header('Content-Type: application/json');
include '../includes/koneksi.php';

$response = ['status' => 'error', 'message' => 'Webinar tidak ditemukan.'];

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_webinar = (int)$_GET['id'];
    
    // 1. Ambil data utama webinar
    $sql_webinar = "SELECT id, topik, tanggal, id_jenis_ifws, jam_mulai, jam_selesai FROM webinar WHERE id = ?";
    $stmt_webinar = $koneksi->prepare($sql_webinar);

    if ($stmt_webinar) {
        $stmt_webinar->bind_param("i", $id_webinar);
        $stmt_webinar->execute();
        $result_webinar = $stmt_webinar->get_result();

        if ($result_webinar->num_rows > 0) {
            $webinar_data = $result_webinar->fetch_assoc();
            
            // 2. Ambil data peserta (narasumber & panitia)
            $peserta = [];
            $sql_peserta = "SELECT id_narasumber, peran, n.nama 
                            FROM webinar_peserta wp
                            JOIN narasumber n ON wp.id_narasumber = n.id
                            WHERE wp.id_webinar = ?";
            $stmt_peserta = $koneksi->prepare($sql_peserta);
            if ($stmt_peserta) {
                $stmt_peserta->bind_param("i", $id_webinar);
                $stmt_peserta->execute();
                $result_peserta = $stmt_peserta->get_result();
                while ($row = $result_peserta->fetch_assoc()) {
                    $peserta[] = $row;
                }
                $stmt_peserta->close();
            }
            
            // Gabungkan semua data menjadi satu
            $response['status'] = 'success';
            $response['message'] = 'Data webinar ditemukan.';
            $response['data'] = [
                'webinar' => $webinar_data,
                'peserta' => $peserta
            ];
        }
        $stmt_webinar->close();
    }
}

$koneksi->close();
echo json_encode($response);
?>