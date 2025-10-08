<?php
header('Content-Type: application/json');
include '../includes/koneksi.php'; // Sesuaikan path ke file koneksi

// Ambil parameter tahun dan semester dari URL
$tahun_akd_full = $_GET['tahun'] ?? '';
$semester_akd = $_GET['semester'] ?? '';

if (empty($tahun_akd_full) || empty($semester_akd)) {
    echo json_encode(['error' => 'Parameter tahun dan semester dibutuhkan.']);
    exit;
}

// 1. Query SQL mengambil kolom yang dibutuhkan untuk Sekretariat
//    Termasuk 'narasumber', 'jenis_webinar', dan 'status_rekap'.
$sql = "SELECT i.id, i.tanggal, j.nama_jenis AS jenis_webinar, i.topik_webinar, i.status_rekap 
        FROM ifws AS i
        JOIN jenis_ifws AS j ON i.id_jenis_ifws = j.id
        WHERE i.tahun_akd = ? AND i.semester_akd = ?
        ORDER BY i.tanggal ASC";

$stmt = $koneksi->prepare($sql);
$stmt->bind_param("ss", $tahun_akd_full, $semester_akd);
$stmt->execute();
$result = $stmt->get_result();

$webinars = [];
while ($row = $result->fetch_assoc()) {
    // 2. Proses data: Ubah status rekap menjadi boolean (true/false)
    //    Ini penting agar JavaScript bisa dengan mudah melakukan pengecekan if/else.
    $row['sudah_rekap'] = ($row['status_rekap'] == 'Sudah');

    // (Opsional) Hapus kolom status_rekap asli agar output JSON lebih bersih
    unset($row['status_rekap']);

    $webinars[] = $row;
}

$stmt->close();
$koneksi->close();

// 3. Kirim hasil dalam format JSON
echo json_encode($webinars);
?>