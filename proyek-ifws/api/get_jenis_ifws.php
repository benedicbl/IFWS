<?php
header('Content-Type: application/json');
include '../includes/koneksi.php';

$data = [];
$sql = "SELECT id, nama_jenis FROM jenis_ifws ORDER BY id";
$result = $koneksi->query($sql);

if ($result) {
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

$koneksi->close();
echo json_encode($data);
?>