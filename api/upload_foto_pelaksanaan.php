<?php
require_once '../includes/config.php';
header('Content-Type: application/json'); // Set header untuk respon JSON

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_FILES['foto_pelaksanaan']) && isset($_POST['id_webinar'])) {
    $webinar_id = (int)$_POST['id_webinar'];
    $files = $_FILES['foto_pelaksanaan'];
    $upload_dir = 'assets/foto_pelaksanaan/';

    if (!is_dir('../' . $upload_dir)) {
        mkdir('../' . $upload_dir, 0777, true);
    }

    $uploaded_files = [];
    $errors = [];

    foreach ($files['name'] as $key => $name) {
        if ($files['error'][$key] === UPLOAD_ERR_OK) {
            $tmp_name = $files['tmp_name'][$key];
            $file_ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            $new_filename = 'pelaksanaan_' . $webinar_id . '_' . time() . '_' . uniqid() . '.' . $file_ext;
            $destination = '../' . $upload_dir . $new_filename;

            if (move_uploaded_file($tmp_name, $destination)) {
                $uploaded_files[] = $upload_dir . $new_filename;
            } else {
                $errors[] = "Gagal memindahkan file: $name";
            }
        }
    }

    if (empty($errors) && !empty($uploaded_files)) {
        $query = "INSERT INTO foto_pelaksanaan (id_webinar, file_path) VALUES (?, ?)";
        $stmt = mysqli_prepare($koneksi, $query);
        foreach ($uploaded_files as $path) {
            mysqli_stmt_bind_param($stmt, "is", $webinar_id, $path);
            mysqli_stmt_execute($stmt);
        }
        mysqli_stmt_close($stmt);

        echo json_encode(['status' => 'success']);
        exit();
    } else {
        $error_message = empty($errors) ? "Tidak ada file yang berhasil diupload." : implode(", ", $errors);
        echo json_encode(['status' => 'error', 'message' => $error_message]);
        exit();
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Permintaan tidak valid.']);
    exit();
}
?>