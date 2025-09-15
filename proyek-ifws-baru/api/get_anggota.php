<?php
header('Content-Type: application/json');
include '../includes/koneksi.php';

$data = [];
$sql = "SELECT id, nama, email, jabatan FROM anggota ORDER BY nama ASC";
$result = $koneksi->query($sql);

if ($result) {
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

$koneksi->close();
echo json_encode($data);
?>