<?php
header('Content-Type: application/json');
session_start();
include '../includes/koneksi.php'; 

// Fungsi untuk menentukan tahun akademik dan semester saat ini
function getCurrentAkademik() {
    $currentMonth = date('n'); // format 'n' = Bulan sebagai angka (1-12)
    $currentYear = date('Y');  // format 'Y' = Tahun sebagai 4 digit angka

    // Cek untuk semester Ganjil (Agustus - Desember DAN Januari)
    if (($currentMonth >= 8 && $currentMonth <= 12) || $currentMonth == 1) {
        $semester = 'Ganjil';
        if ($currentMonth >= 8) {
            $tahun_akd = $currentYear . '/' . ($currentYear + 1);
        }
        else { // Bulan Januari
            $tahun_akd = ($currentYear - 1) . '/' . $currentYear;
        }
    }
    // Semester Genap (Februari - Juli)
    else {
        $semester = 'Genap';
        $tahun_akd = ($currentYear - 1) . '/' . $currentYear;
    }

    return [
        'tahun_akd' => $tahun_akd,
        'semester_akd' => $semester
    ];
}

$akademik = getCurrentAkademik();
$tahun_akd = $akademik['tahun_akd'];
$semester_akd = $akademik['semester_akd'];

$response = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // --- PROSES MENYIMPAN DATA (UPDATE/INSERT) ---
    $data = json_decode(file_get_contents('php://input'), true);

    // 1. Dapatkan semester saat ini & data input dari user
    $akademik = getCurrentAkademik();
    $tahun_akd = $akademik['tahun_akd'];
    $semester_akd = $akademik['semester_akd'];
    $data_input = json_decode(file_get_contents('php://input'), true);

    // 2. Cari nilai dasar (baseline) dari peraturan terakhir yang ada
    // Ini akan menjadi "warisan" jika kita membuat baris baru
    $sql_latest = "SELECT * FROM peraturan ORDER BY tahun_akd DESC, semester_akd DESC LIMIT 1";
    $result_latest = $koneksi->query($sql_latest);
    $latest_rule = $result_latest->fetch_assoc();

    // 3. Gabungkan data: mulai dengan nilai terakhir, lalu timpa dengan input baru
    // Jika tabel kosong, gunakan default hardcode
    $nilai_waktu = $data_input['peraturan_waktu'] ?? $latest_rule['peraturan_waktu'] ?? 45;
    $nilai_sidang = $data_input['peraturan_sidang'] ?? $latest_rule['peraturan_sidang'] ?? 10;

    // 4. Gunakan satu perintah SQL cerdas untuk INSERT atau UPDATE
    $sql = "INSERT INTO peraturan (tahun_akd, semester_akd, peraturan_waktu, peraturan_sidang) 
            VALUES (?, ?, ?, ?) 
            ON DUPLICATE KEY UPDATE 
            peraturan_waktu = VALUES(peraturan_waktu), 
            peraturan_sidang = VALUES(peraturan_sidang)";
           
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("ssii", $tahun_akd, $semester_akd, $nilai_waktu, $nilai_sidang);

    if ($stmt->execute()) {
        $response = ['sukses' => true, 'pesan' => 'Peraturan berhasil disimpan.'];
    } else {
        $response = ['sukses' => false, 'pesan' => 'Gagal menyimpan peraturan.'];
    }
    $stmt->close();

}

$koneksi->close();
echo json_encode($response);
?>