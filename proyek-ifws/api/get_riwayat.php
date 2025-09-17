<?php
header('Content-Type: application/json');
include '../includes/koneksi.php';

$tahun_akademik = isset($_GET['tahun']) ? $_GET['tahun'] : '';
$data = [];

// Logika untuk filter berdasarkan tahun akademik
$where_clause = '';
if (!empty($tahun_akademik)) {
    list($tahun, $semester) = explode('-', $tahun_akademik);
    if ($semester == 'Ganjil') {
        $where_clause = "WHERE w.tanggal BETWEEN '$tahun-08-01' AND '$tahun-12-31'";
    } else {
        $where_clause = "WHERE w.tanggal BETWEEN '$tahun-01-01' AND '$tahun-07-31'";
    }
}

// Query utama untuk mengambil data webinar dan jenisnya
$sql_webinar = "SELECT 
                    w.id, 
                    w.topik, 
                    w.tanggal, 
                    j.nama_jenis AS jenis_ifws 
                FROM webinar AS w
                LEFT JOIN jenis_ifws AS j ON w.id_jenis_ifws = j.id
                $where_clause 
                ORDER BY w.tanggal DESC";

$result_webinar = $koneksi->query($sql_webinar);

if ($result_webinar && $result_webinar->num_rows > 0) {
    while ($webinar = $result_webinar->fetch_assoc()) {
        $id_webinar = $webinar['id'];
        
        // Ambil nama narasumber utama
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

        // ---- LOGIKA BARU UNTUK CEK SCREENSHOT ----
        // Hitung jumlah screenshot yang ada untuk webinar ini
        $jumlah_screenshot = 0;
        $stmt_ss = $koneksi->prepare("SELECT COUNT(id) as jumlah FROM screenshot WHERE id_webinar = ?");
        if ($stmt_ss) {
            $stmt_ss->bind_param("i", $id_webinar);
            $stmt_ss->execute();
            $result_ss = $stmt_ss->get_result();
            $jumlah_screenshot = (int)$result_ss->fetch_assoc()['jumlah'];
            $stmt_ss->close();
        }
        // Tambahkan jumlah screenshot ke data webinar
        $webinar['jumlah_screenshot'] = $jumlah_screenshot;

        $data[] = $webinar;
    }
}

$koneksi->close();
echo json_encode($data);
?>