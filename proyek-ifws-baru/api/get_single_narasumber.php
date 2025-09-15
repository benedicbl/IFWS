<?php
header('Content-Type: application/json');
include '../includes/koneksi.php';

// Siapkan respons default jika tidak ada data
$response = null;

// Cek apakah 'id' ada di URL dan merupakan angka
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Gunakan prepared statement untuk keamanan
    $sql = "SELECT id, nama, email FROM narasumber WHERE id = ?";
    $stmt = $koneksi->prepare($sql);

    if ($stmt) {
        // Bind parameter 'id' ke placeholder. 'i' berarti tipe data Integer.
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        // Ambil hasilnya
        if ($result->num_rows > 0) {
            $response = $result->fetch_assoc();
        }
        
        $stmt->close();
    }
}

$koneksi->close();

// Kembalikan hasilnya dalam format JSON
echo json_encode($response);
?>