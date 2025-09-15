<?php
header('Content-Type: application/json');
include '../includes/koneksi.php';

$tahun_akademik = isset($_GET['tahun']) ? $_GET['tahun'] : '';
$data = [];

$where_clause = '';
if (!empty($tahun_akademik)) {
    list($tahun, $semester_str) = explode('-', $tahun_akademik);
    $tahun_int = (int)$tahun;

    // --- LOGIKA FILTER TANGGAL DIPERBAIKI ---
    if ($semester_str == 'Ganjil') {
        // Semester Ganjil: Agustus (8) sampai Desember (12)
        $where_clause = "WHERE YEAR(w.tanggal) = ? AND MONTH(w.tanggal) BETWEEN 8 AND 12";
    } else { // Genap
        // Semester Genap: Januari (1) sampai Juli (7)
        $where_clause = "WHERE YEAR(w.tanggal) = ? AND MONTH(w.tanggal) BETWEEN 1 AND 7";
    }
    // -----------------------------------------
}

$sql_webinar = "SELECT 
                    w.id, w.topik, w.tanggal, 
                    j.nama_jenis AS jenis_ifws 
                FROM webinar AS w
                LEFT JOIN jenis_ifws AS j ON w.id_jenis_ifws = j.id
                $where_clause 
                ORDER BY w.tanggal DESC";

$stmt_webinar = $koneksi->prepare($sql_webinar);

// Jika ada filter tahun, bind parameternya
if (!empty($where_clause)) {
    $stmt_webinar->bind_param("i", $tahun_int);
}

$stmt_webinar->execute();
$result_webinar = $stmt_webinar->get_result();

if ($result_webinar && $result_webinar->num_rows > 0) {
    while ($webinar = $result_webinar->fetch_assoc()) {
        $id_webinar = $webinar['id'];
        $narasumber_utama = "Belum ada";
        
        $stmt_narsum = $koneksi->prepare("SELECT n.nama FROM webinar_peserta wp JOIN narasumber n ON wp.id_narasumber = n.id WHERE wp.id_webinar = ? AND wp.peran = 'Narasumber' LIMIT 1");
        if ($stmt_narsum) {
            $stmt_narsum->bind_param("i", $id_webinar);
            $stmt_narsum->execute();
            $result_narsum = $stmt_narsum->get_result();
            if ($result_narsum->num_rows > 0) {
                $narasumber_utama = $result_narsum->fetch_assoc()['nama'];
            }
            $stmt_narsum->close();
        }
        $webinar['narasumber'] = $narasumber_utama;
        $data[] = $webinar;
    }
}

$stmt_webinar->close();
$koneksi->close();
echo json_encode($data);
?>