<?php
// Set header ke JSON di baris pertama
header('Content-Type: application/json');

// Menangkap semua error PHP dan mengubahnya menjadi JSON
function handle_php_error($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) { return false; }
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}
set_error_handler('handle_php_error');

try {
    // 1. Cek Autoloader MPDF
    $autoload_path = '../vendor/autoload.php';
    if (!file_exists($autoload_path)) {
        throw new Exception("File 'vendor/autoload.php' tidak ditemukan. Pastikan Anda sudah menjalankan 'composer require mpdf/mpdf' di folder proyek.");
    }
    require_once $autoload_path;
    require_once '../includes/config.php';

    // 2. Proteksi Session & Request
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'sekretaris') {
        throw new Exception('Akses ditolak (session tidak valid).');
    }
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id_webinar'])) {
        throw new Exception('Request tidak valid.');
    }

    set_time_limit(300); // 5 menit
    $id_webinar = (int)$_POST['id_webinar'];

    // 3. Cek Path Template Gambar (HANYA SATU)
    $template_path_abs = realpath(__DIR__ . '/../assets/picture/templates/template_sertifikat.jpg');
    if (!$template_path_abs || !file_exists($template_path_abs)) {
        throw new Exception('File template_sertifikat.jpg tidak ditemukan di /assets/picture/templates/. Harap upload template terlebih dahulu.');
    }
    
    // Siapkan folder penyimpanan sertifikat PDF
    $upload_dir_relative = 'assets/sertifikat/';
    $upload_dir_absolute = realpath(__DIR__ . '/../') . DIRECTORY_SEPARATOR . $upload_dir_relative;
    if (!is_dir($upload_dir_absolute)) {
        if (!mkdir($upload_dir_absolute, 0777, true)) {
            throw new Exception("Gagal membuat folder sertifikat. Periksa izin folder /assets.");
        }
    }

    // 4. Ambil Info Webinar (Topik & Tanggal)
    $query_webinar = "SELECT topik, tanggal_direncanakan FROM webinars WHERE id = ?";
    $stmt_webinar = mysqli_prepare($koneksi, $query_webinar);
    mysqli_stmt_bind_param($stmt_webinar, "i", $id_webinar);
    mysqli_stmt_execute($stmt_webinar);
    $webinar_data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_webinar));
    if (!$webinar_data) { throw new Exception("Data webinar tidak ditemukan."); }
    $topik_webinar = $webinar_data['topik'] ?? 'Webinar IFWS';
    $tanggal_webinar = $webinar_data['tanggal_direncanakan'] ?? date('Y-m-d');
    mysqli_stmt_close($stmt_webinar);

    setlocale(LC_TIME, 'id_ID.UTF-8', 'id_ID', 'id');
    $tanggal_formatted = "" . strftime('%d %B %Y', strtotime($tanggal_webinar));
    $generated_files = 0;
    
    // <-- PERUBAHAN DI SINI: Inisialisasi mPDF DIPINDAHKAN DARI SINI ...

    // 5. Query SEMUA yang 'hadir' (termasuk yang di-override)
    // Ini akan mengambil Peserta, Panitia, dan Narasumber
    $query_all_hadir = "SELECT id, nama_lengkap, email, role 
                        FROM kehadiran 
                        WHERE id_webinar = ? AND status_kehadiran = 'hadir'";
    $stmt_all = mysqli_prepare($koneksi, $query_all_hadir);
    mysqli_stmt_bind_param($stmt_all, "i", $id_webinar);
    mysqli_stmt_execute($stmt_all);
    $result_all = mysqli_stmt_get_result($stmt_all);

    // Ambil HTML template SATU KALI
    $template_html_base = file_get_contents('../templates/template_sertifikat.php');

    while ($row = mysqli_fetch_assoc($result_all)) {
        
        // <-- PERUBAHAN DI SINI: ... DAN DITEMPATKAN DI DALAM LOOP
        // Ini membuat objek PDF baru (kosong) untuk SETIAP peserta.
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4-L']);
        $mpdf->SetDisplayMode('fullpage');
        
        $nama = $row['nama_lengkap'];
        $role_text = ucfirst($row['role']); // Mengubah 'peserta' -> 'Peserta', 'panitia' -> 'Panitia', 'narasumber' -> 'Narasumber'
        $kehadiran_id = $row['id'];
        
        $html = str_replace(
            ['{NAMA_LENGKAP}', '{PERAN}', '{TOPIK_WEBINAR}', '{TANGGAL_WEBINAR}'],
            [htmlspecialchars($nama), htmlspecialchars($role_text), htmlspecialchars($topik_webinar), $tanggal_formatted],
            $template_html_base
        );
        
        // <-- PERUBAHAN DI SINI: Mengganti placeholder yang lebih jelas
        // Ganti placeholder background di template HTML dengan path absolut
        $html = str_replace(
            '{BACKGROUND_PLACEHOLDER}', // Placeholder baru (lihat file template_sertifikat.php)
            $template_path_abs, // Ganti dengan path absolut ke gambar
            $html
        );

        $mpdf->WriteHTML($html);
        $safe_name = preg_replace('/[^A-Za-z0-9\-]/', '_', $nama);
        // Buat nama file yang unik
        $file_name = "sertifikat_{$id_webinar}_{$role_text}_{$kehadiran_id}_{$safe_name}.pdf";
        $file_path_rel = $upload_dir_relative . $file_name;
        $file_path_abs = $upload_dir_absolute . $file_name;
        
        $mpdf->Output($file_path_abs, 'F');
        $generated_files++;
        
        // Simpan path ke database
        $query_update = "UPDATE kehadiran SET path_sertifikat = ? WHERE id = ?";
        $stmt_update = mysqli_prepare($koneksi, $query_update);
        mysqli_stmt_bind_param($stmt_update, "si", $file_path_rel, $kehadiran_id);
        mysqli_stmt_execute($stmt_update);
        mysqli_stmt_close($stmt_update);

        // <-- PERUBAHAN DI SINI: Hancurkan objek mPDF untuk membebaskan memori (opsional tapi bagus)
        unset($mpdf);
    }
    mysqli_stmt_close($stmt_all);

    echo json_encode(['status' => 'success', 'message' => "Berhasil men-generate $generated_files sertifikat."]);

} catch (Exception $e) {
    http_response_code(500); // Set status error server
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
exit();
?>