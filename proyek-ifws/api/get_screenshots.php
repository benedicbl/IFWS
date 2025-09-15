<?php
header('Content-Type: application/json');
include '../includes/koneksi.php';

$response = [];

// Cek apakah id_webinar ada di URL dan merupakan angka
if (isset($_GET['id_webinar']) && is_numeric($_GET['id_webinar'])) {
    $id_webinar = (int)$_GET['id_webinar'];

    // Ambil semua nama file untuk webinar tersebut
    $sql = "SELECT id, nama_file FROM screenshot WHERE id_webinar = ?";
    $stmt = $koneksi->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $id_webinar);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $response[] = $row; // Tambahkan setiap baris ke array response
        }
        
        $stmt->close();
    }
}

$koneksi->close();

// Kembalikan hasilnya dalam format JSON
echo json_encode($response);
?>