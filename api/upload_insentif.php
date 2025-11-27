<?php
require_once '../includes/config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'bendahara') { die('Akses ditolak.'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['bukti_file']) && isset($_POST['webinar_id'])) {
    $webinar_id = (int)$_POST['webinar_id'];
    $file = $_FILES['bukti_file'];

    if ($file['error'] !== UPLOAD_ERR_OK) { header('Location: /projek-ifws/Bendahara/bendahara_datanarsum.php?status=upload_error'); exit(); }
    if ($file['size'] > 2 * 1024 * 1024) { header('Location: /projek-ifws/Bendahara/bendahara_datanarsum.php?status=file_kebesaran'); exit(); }
    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($file_ext, ['jpg', 'jpeg', 'png', 'pdf'])) { header('Location: /projek-ifws/Bendahara/bendahara_datanarsum.php?status=tipe_file_salah'); exit(); }

    $upload_dir = 'assets/bukti_insentif/';
    if (!is_dir('../' . $upload_dir)) { mkdir('../' . $upload_dir, 0777, true); }
    $new_filename = 'bukti_' . $webinar_id . '_' . time() . '.' . $file_ext;
    $destination_path = $upload_dir . $new_filename;

    if (move_uploaded_file($file['tmp_name'], '../' . $destination_path)) {
        // Hapus bukti lama jika ada (logika update)
        $deleteQuery = "DELETE FROM bukti_insentif WHERE id_webinar = ?";
        $stmtDel = mysqli_prepare($koneksi, $deleteQuery);
        mysqli_stmt_bind_param($stmtDel, "i", $webinar_id);
        mysqli_stmt_execute($stmtDel);

        // Simpan path baru ke database
        $query = "INSERT INTO bukti_insentif (id_webinar, file_path) VALUES (?, ?)";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, "is", $webinar_id, $destination_path);
        mysqli_stmt_execute($stmt);

        header('Location: /projek-ifws/Bendahara/bendahara_datanarsum.php?status=upload_sukses');
    } else {
        header('Location: /projek-ifws/Bendahara/bendahara_datanarsum.php?status=gagal_pindah');
    }
    exit();
}
?>