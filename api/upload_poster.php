<?php
require_once '../includes/config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'promosi') {
    die('Akses ditolak.');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['poster_file']) && isset($_POST['webinar_id'])) {
    $webinar_id = (int)$_POST['webinar_id'];
    $file = $_FILES['poster_file'];

    // Validasi upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        header('Location: /projek-ifws/Promosi/promosi_datawebinar.php?status=upload_error');
        exit();
    }
    if ($file['size'] > 2 * 1024 * 1024) { // Maks 2MB
        header('Location: /projek-ifws/Promosi/promosi_datawebinar.php?status=file_kebesaran');
        exit();
    }
    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($file_ext, ['jpg', 'jpeg', 'png'])) {
        header('Location: /projek-ifws/Promosi/promosi_datawebinar.php?status=tipe_file_salah');
        exit();
    }

    // Buat path penyimpanan
    $upload_dir = 'assets/posters/';
    if (!is_dir('../' . $upload_dir)) { // Cek dari root direktori proyek
        mkdir('../' . $upload_dir, 0777, true);
    }
    $new_filename = 'poster_' . $webinar_id . '_' . time() . '.' . $file_ext;
    $destination_path = $upload_dir . $new_filename;
    
    // Pindahkan file
    if (move_uploaded_file($file['tmp_name'], '../' . $destination_path)) {
        // Simpan path ke database
        $query = "UPDATE webinars SET poster_path = ? WHERE id = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "si", $destination_path, $webinar_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header('Location: /projek-ifws/Promosi/promosi_datawebinar.php?status=upload_sukses');
    } else {
        header('Location: /projek-ifws/Promosi/promosi_datawebinar.php?status=gagal_pindah');
    }
    exit();
}
?>