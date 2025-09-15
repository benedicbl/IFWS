<?php
header('Content-Type: application/json');
include '../includes/koneksi.php';

$response = null;

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "SELECT id, nama, email, jabatan FROM anggota WHERE id = ?";
    $stmt = $koneksi->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $response = $result->fetch_assoc();
        }

        $stmt->close();
    }
}

$koneksi->close();
echo json_encode($response);
?>