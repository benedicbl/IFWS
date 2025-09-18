<?php
header('Content-Type: application/json');
include '../includes/koneksi.php';

$tahun_akd_full = $_GET['tahun'] ?? '';
$semester_akd = $_GET['semester'] ?? '';

if (empty($tahun_akd_full) || empty($semester_akd)) {
    echo json_encode(['error' => 'Parameter tahun dan semester dibutuhkan.']);
    exit;
}

// Mengambil kolom yang relevan untuk halaman Promosi
$sql = "SELECT id, topik_webinar, tanggal, poster 
        FROM ifws 
        WHERE tahun_akd = ? AND semester_akd = ?
        ORDER BY tanggal ASC";

$stmt = $koneksi->prepare($sql);
$stmt->bind_param("ss", $tahun_akd_full, $semester_akd);
$stmt->execute();
$result = $stmt->get_result();

$webinars = [];
while ($row = $result->fetch_assoc()) {
    // Jika ada data poster (BLOB), ubah menjadi format Base64
    if (!empty($row['poster'])) {
        $row['poster'] = base64_encode($row['poster']);
    }
    $webinars[] = $row;
}

$stmt->close();
$koneksi->close();

echo json_encode($webinars);
?>