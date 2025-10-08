<?php
header('Content-Type: application/json');
include '../includes/koneksi.php'; // Sesuaikan path ke file koneksi

// Ambil parameter tahun dan semester dari URL
$tahun_akd_full = $_GET['tahun'] ?? ''; // Contoh: '2023/2024'
$semester_akd = $_GET['semester'] ?? ''; // Contoh: 'Ganjil'

if (empty($tahun_akd_full) || empty($semester_akd)) {
    echo json_encode(['error' => 'Parameter tahun dan semester dibutuhkan.']);
    exit;
}

// Catatan: Tabel `ifws` Anda tidak memiliki kolom `narasumber`. 
// Untuk mengambil nama narasumber, idealnya perlu JOIN dengan tabel lain.
// Untuk saat ini, kita akan mengambil kolom yang ada di tabel `ifws`.
$sql = "SELECT id, topik_webinar, tanggal, link_webinar 
        FROM ifws 
        WHERE tahun_akd = ? AND semester_akd = ?
        ORDER BY tanggal ASC";

$stmt = $koneksi->prepare($sql);
$stmt->bind_param("ss", $tahun_akd_full, $semester_akd);
$stmt->execute();
$result = $stmt->get_result();

$webinars = [];
while ($row = $result->fetch_assoc()) {
    $webinars[] = $row;
}

$stmt->close();
$koneksi->close();

echo json_encode($webinars);
?>