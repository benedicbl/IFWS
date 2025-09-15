<?php
header('Content-Type: application/json');
include '../includes/koneksi.php';

$response = ['status' => 'error', 'message' => 'Request tidak valid.'];

// Pastikan request adalah POST dan ada file yang dikirim
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['screenshots']) && isset($_POST['id_webinar'])) {
    
    $id_webinar = (int)$_POST['id_webinar'];
    $target_dir = "../assets/uploads/screenshots/"; // Folder tujuan yang sudah kita buat
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    $max_size = 5 * 1024 * 1024; // 5 MB

    $uploaded_files = [];
    $errors = [];

    // Looping untuk setiap file yang di-upload
    // $_FILES['screenshots'] adalah array karena input HTML-nya 'multiple'
    foreach ($_FILES['screenshots']['name'] as $key => $name) {
        if ($_FILES['screenshots']['error'][$key] === UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['screenshots']['tmp_name'][$key];
            
            // Cek ukuran file
            if ($_FILES['screenshots']['size'][$key] > $max_size) {
                $errors[] = "File '$name' terlalu besar (Maks 5MB).";
                continue;
            }

            // Cek tipe file
            $file_ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if (!in_array($file_ext, $allowed_types)) {
                $errors[] = "Tipe file '$name' tidak diizinkan (Hanya JPG, PNG, GIF).";
                continue;
            }

            // Buat nama file yang unik untuk mencegah tumpang tindih
            $new_filename = uniqid('ss_', true) . '.' . $file_ext;
            $target_file = $target_dir . $new_filename;

            // Pindahkan file dari lokasi sementara ke folder permanen
            if (move_uploaded_file($tmp_name, $target_file)) {
                
                // Jika berhasil dipindah, simpan nama file ke database
                $sql = "INSERT INTO screenshot (id_webinar, nama_file) VALUES (?, ?)";
                $stmt = $koneksi->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param("is", $id_webinar, $new_filename);
                    $stmt->execute();
                    $stmt->close();
                    $uploaded_files[] = $new_filename;
                }
            } else {
                $errors[] = "Gagal memindahkan file '$name'.";
            }
        }
    }

    if (empty($errors)) {
        $response = ['status' => 'success', 'message' => count($uploaded_files) . ' screenshot berhasil di-upload!'];
    } else {
        $response['message'] = implode("\n", $errors);
    }
}

$koneksi->close();
echo json_encode($response);
?>