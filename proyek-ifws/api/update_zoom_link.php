<?php
header('Content-Type: application/json');
include '../includes/koneksi.php'; // Sesuaikan path ke file koneksi

// Ambil data JSON yang dikirim dari JavaScript
$data = json_decode(file_get_contents('php://input'), true);

$id_webinar = $data['id'] ?? null;
$link_zoom = $data['linkZoom'] ?? '';

if ($id_webinar === null) {
    echo json_encode(['sukses' => false, 'pesan' => 'ID Webinar tidak valid.']);
    exit;
}

$sql = "UPDATE ifws SET link_webinar = ? WHERE id = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("si", $link_zoom, $id_webinar);

if ($stmt->execute()) {
    echo json_encode(['sukses' => true, 'pesan' => 'Link Zoom berhasil diperbarui.']);
} else {
    echo json_encode(['sukses' => false, 'pesan' => 'Gagal memperbarui Link Zoom.']);
}

$stmt->close();
$koneksi->close();
?>