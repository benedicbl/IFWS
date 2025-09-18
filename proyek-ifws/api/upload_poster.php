<?php
header('Content-Type: application/json');
include '../includes/koneksi.php';

$response = ['sukses' => false, 'pesan' => 'Request tidak valid.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_webinar = $_POST['id_webinar'] ?? null;

    if ($id_webinar && isset($_FILES['posterFile']) && $_FILES['posterFile']['error'] === UPLOAD_ERR_OK) {
        
        $file = $_FILES['posterFile'];
        
        // Validasi tipe file
        $allowedTypes = ['image/jpeg', 'image/png'];
        if (!in_array($file['type'], $allowedTypes)) {
            $response['pesan'] = 'Hanya file JPG dan PNG yang diizinkan.';
            echo json_encode($response);
            exit;
        }

        // Baca seluruh konten file sebagai data biner
        $fileContent = file_get_contents($file['tmp_name']);

        // Update kolom 'poster' dengan data biner (BLOB)
        $sql = "UPDATE ifws SET poster = ? WHERE id = ?";
        $stmt = $koneksi->prepare($sql);
        // "b" berarti kita mengirim data sebagai BLOB
        $stmt->bind_param("bi", $fileContent, $id_webinar);
        // Untuk data besar, cara ini lebih aman
        $stmt->send_long_data(0, $fileContent);
       
        if ($stmt->execute()) {
            $response = ['sukses' => true, 'pesan' => 'Poster berhasil disimpan ke database.'];
        } else {
            $response['pesan'] = 'Gagal menyimpan data poster ke database.';
        }
        $stmt->close();
    } else {
        $response['pesan'] = 'Data tidak lengkap atau terjadi error saat upload.';
    }
}

$koneksi->close();
echo json_encode($response);
?>