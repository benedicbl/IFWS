<?php
require_once '../includes/config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'sekretaris' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
    exit();
}

try {
    $file = $_FILES['template_file'] ?? null;
    
    // HANYA SATU NAMA FILE TUJUAN
    $destination_folder = '../assets/picture/templates/';
    $destination_name = 'template_sertifikat.jpg'; // Nama file tetap
    $destination_path = $destination_folder . $destination_name;

    if ($file === null || $file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Gagal mengupload file. Pastikan file tidak rusak.');
    }
    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($file_ext, ['jpg', 'jpeg', 'png'])) {
        throw new Exception('Tipe file tidak valid. Harap upload file .jpg atau .png');
    }
    if (!is_dir($destination_folder)) {
        if (!mkdir($destination_folder, 0777, true)) {
            throw new Exception("Gagal membuat folder. Periksa izin folder /assets/picture/.");
        }
    }
    if (move_uploaded_file($file['tmp_name'], $destination_path)) {
        echo json_encode(['status' => 'success', 'message' => 'Template gambar berhasil diupload!']);
    } else {
        throw new Exception('Gagal menyimpan file. Periksa izin folder /assets/picture/templates.');
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
exit();
?>