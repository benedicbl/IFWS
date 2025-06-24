<?php
header('Content-Type: application/json');
include '../includes/koneksi.php';

$response = ['status' => 'error', 'message' => 'Terjadi kesalahan.'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tanggal = $_POST['tanggal'] ?? '';
    $id_jenis_ifws = isset($_POST['id_jenis_ifws']) ? (int)$_POST['id_jenis_ifws'] : 0;
    $jam_mulai = $_POST['jam-mulai'] ?? '';
    $jam_selesai = $_POST['jam-selesai'] ?? '';
    $topik = $_POST['topik'] ?? '';

    if (!empty($tanggal) && $id_jenis_ifws > 0 && !empty($jam_mulai) && !empty($topik)) {
        
        $sql = "INSERT INTO webinar (tanggal, id_jenis_ifws, jam_mulai, jam_selesai, topik) VALUES (?, ?, ?, ?, ?)";
        $stmt = $koneksi->prepare($sql);

        if ($stmt) {
            // --- PERBAIKAN UTAMA DI SINI ---
            // Urutan dan tipe data disesuaikan dengan urutan kolom di SQL
            // SQL:   (tanggal,      id_jenis_ifws, jam_mulai, jam_selesai, topik)
            // Tipe:  (string 's',  integer 'i',   string 's', string 's', string 's')
            // Variabel: ($tanggal, $id_jenis_ifws, $jam_mulai, $jam_selesai, $topik)
            $stmt->bind_param("sisss", $tanggal, $id_jenis_ifws, $jam_mulai, $jam_selesai, $topik);

            if ($stmt->execute()) {
                $new_webinar_id = $koneksi->insert_id;

                $response['status'] = 'success';
                $response['message'] = 'Data dasar webinar berhasil disimpan. Mengarahkan ke halaman pilih peserta...';
                $response['new_webinar_id'] = $new_webinar_id;
            } else {
                $response['message'] = 'Gagal menyimpan data webinar: ' . $stmt->error;
            }
            $stmt->close();
        } else {
            $response['message'] = 'Gagal mempersiapkan statement: ' . $koneksi->error;
        }

    } else {
        $response['message'] = 'Data form tidak lengkap. Pastikan semua field terisi, termasuk Jenis IFWS.';
    }
} else {
    $response['message'] = 'Metode request tidak valid.';
}

$koneksi->close();
echo json_encode($response);
?>