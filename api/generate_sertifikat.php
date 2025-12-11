<?php
// Set header ke JSON
header('Content-Type: application/json');

function handle_php_error($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) { return false; }
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}
set_error_handler('handle_php_error');

try {
    // 1. Cek Autoloader
    $autoload_path = '../vendor/autoload.php';
    if (!file_exists($autoload_path)) {
        throw new Exception("File 'vendor/autoload.php' tidak ditemukan.");
    }
    require_once $autoload_path;
    require_once '../includes/config.php';

    // 2. Proteksi Session
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'sekretaris') {
        throw new Exception('Akses ditolak.');
    }
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id_webinar'])) {
        throw new Exception('Request tidak valid.');
    }

    set_time_limit(300); 
    ini_set('memory_limit', '256M');
    
    $id_webinar = (int)$_POST['id_webinar'];

    // 3. Cek Template Gambar
    $template_path_abs = realpath(__DIR__ . '/../assets/picture/templates/template_sertifikat.jpg');
    if (!$template_path_abs || !file_exists($template_path_abs)) {
        throw new Exception('File template_sertifikat.jpg tidak ditemukan.');
    }
    
    // 4. Ambil Info Webinar
    $query_webinar = "SELECT topik, tanggal_direncanakan FROM webinars WHERE id = ?";
    $stmt_webinar = mysqli_prepare($koneksi, $query_webinar);
    mysqli_stmt_bind_param($stmt_webinar, "i", $id_webinar);
    mysqli_stmt_execute($stmt_webinar);
    $webinar_data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_webinar));
    if (!$webinar_data) { throw new Exception("Data webinar tidak ditemukan."); }
    
    $topik_webinar = $webinar_data['topik'] ?? 'Webinar';
    $tanggal_webinar = $webinar_data['tanggal_direncanakan'] ?? date('Y-m-d');
    mysqli_stmt_close($stmt_webinar);

    // --- LOGIKA UKURAN FONT DINAMIS ---
    // Hitung panjang karakter topik untuk menentukan ukuran font
    $panjang_karakter = strlen($topik_webinar);
    $font_size_topik = '24pt'; // Default (Judul Pendek)
    $line_height_topik = '1.2'; // Spasi baris normal

    if ($panjang_karakter > 80) {
        // Kasus Ekstrem Panjang (> 80 huruf)
        $font_size_topik = '16pt'; 
        $line_height_topik = '1.1'; // Rapatkan baris agar muat
    } elseif ($panjang_karakter > 50) {
        // Kasus Panjang Menengah (50 - 80 huruf)
        $font_size_topik = '20pt';
    } elseif ($panjang_karakter > 30) {
        // Kasus Agak Panjang (30 - 50 huruf)
        $font_size_topik = '22pt';
    }

    // Logika Folder
    $clean_topic = preg_replace('/[^A-Za-z0-9_-]/', '_', substr($topik_webinar, 0, 30)); 
    $specific_folder_name = "Webinar_" . $id_webinar . "_" . $clean_topic;
    
    $upload_dir_relative = 'assets/sertifikat/' . $specific_folder_name . '/';
    $upload_dir_absolute = realpath(__DIR__ . '/../') . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'sertifikat' . DIRECTORY_SEPARATOR . $specific_folder_name . DIRECTORY_SEPARATOR;

    if (!is_dir($upload_dir_absolute)) {
        if (!mkdir($upload_dir_absolute, 0777, true)) {
            throw new Exception("Gagal membuat folder sertifikat.");
        }
    }

    setlocale(LC_TIME, 'id_ID.UTF-8', 'id_ID', 'id');
    $tanggal_formatted = strftime('%d %B %Y', strtotime($tanggal_webinar));
    $generated_files = 0;

    // 5. Query Data
    $query_all_hadir = "SELECT id, nama_lengkap, role 
                        FROM kehadiran 
                        WHERE id_webinar = ? 
                        AND status_kehadiran = 'hadir'
                        AND (path_sertifikat IS NULL OR path_sertifikat = '')";
                        
    $stmt_all = mysqli_prepare($koneksi, $query_all_hadir);
    mysqli_stmt_bind_param($stmt_all, "i", $id_webinar);
    mysqli_stmt_execute($stmt_all);
    $result_all = mysqli_stmt_get_result($stmt_all);

    if (mysqli_num_rows($result_all) == 0) {
        echo json_encode(['status' => 'success', 'message' => "Semua data sudah lengkap. Tidak ada sertifikat baru."]);
        exit();
    }

    $template_html_base = file_get_contents('../templates/template_sertifikat.php');

    while ($row = mysqli_fetch_assoc($result_all)) {
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8', 
            'format' => 'A4-L',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
            'margin_header' => 0,
            'margin_footer' => 0
        ]);
        
        $mpdf->SetWatermarkImage($template_path_abs, 1, [297, 210], 'F');
        $mpdf->showWatermarkImage = true;
        $mpdf->watermarkImgBehind = true;
        
        $nama = $row['nama_lengkap'];
        $role_text = ucfirst($row['role']); 
        $kehadiran_id = $row['id'];
        
        // Replace Placeholder (Termasuk Font Size Dinamis)
        $html = str_replace(
            ['{NAMA_LENGKAP}', '{PERAN}', '{TOPIK_WEBINAR}', '{TANGGAL_WEBINAR}', '{FONT_SIZE_TOPIK}', '{LINE_HEIGHT}'],
            [htmlspecialchars($nama), htmlspecialchars($role_text), htmlspecialchars($topik_webinar), $tanggal_formatted, $font_size_topik, $line_height_topik],
            $template_html_base
        );

        $mpdf->WriteHTML($html);
        
        $safe_name = preg_replace('/[^A-Za-z0-9\-]/', '_', $nama);
        $file_name = "Sertifikat_{$safe_name}_{$kehadiran_id}.pdf";
        
        $file_path_abs = $upload_dir_absolute . $file_name;
        $file_path_rel = $upload_dir_relative . $file_name;
        
        $mpdf->Output($file_path_abs, 'F');
        $generated_files++;
        
        // Update DB
        $query_update = "UPDATE kehadiran SET path_sertifikat = ? WHERE id = ?";
        $stmt_update = mysqli_prepare($koneksi, $query_update);
        mysqli_stmt_bind_param($stmt_update, "si", $file_path_rel, $kehadiran_id);
        mysqli_stmt_execute($stmt_update);
        mysqli_stmt_close($stmt_update);

        unset($mpdf);
    }
    mysqli_stmt_close($stmt_all);

    echo json_encode(['status' => 'success', 'message' => "Berhasil generate $generated_files sertifikat."]);

} catch (Exception $e) {
    http_response_code(500); 
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
exit();
?>