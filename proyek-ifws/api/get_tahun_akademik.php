<?php
header('Content-Type: application/json');
include '../includes/koneksi.php'; // Sesuaikan path ke file koneksi

// Query ini mengambil kombinasi unik dari tahun dan semester yang ada di tabel ifws.
// DISTINCT memastikan tidak ada data yang duplikat.
// ORDER BY mengurutkan dari yang terbaru ke yang terlama.
$sql = "SELECT DISTINCT tahun_akd, semester_akd 
        FROM ifws 
        ORDER BY tahun_akd ASC, semester_akd ASC";

$result = $koneksi->query($sql);

$tahun_akademik = [];
while ($row = $result->fetch_assoc()) {
    $tahun_akademik[] = $row;
}

$koneksi->close();

echo json_encode($tahun_akademik);
?>