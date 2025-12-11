<?php
require_once '../includes/config.php';

session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'sekretaris')) {
    header('Location: /projek-ifws/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file_csv'])) {
    $file = $_FILES['file_csv']['tmp_name'];
    
    // Validasi file
    if (!is_uploaded_file($file)) {
        header("Location: /projek-ifws/PIC/admin_pesertaTA.php?status=error&msg=File gagal diupload");
        exit();
    }

    $handle = fopen($file, "r");
    $row = 0;
    $success_count = 0;

    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        // Skip header baris pertama (opsional, jika CSV punya header "Nama,NPM,Tipe")
        if ($row == 0 && (strtolower($data[0]) == 'nama' || strtolower($data[0]) == 'nama lengkap')) {
            $row++;
            continue;
        }

        // Asumsi format CSV: Col 0 = Nama, Col 1 = NPM, Col 2 = Status TA (TA_1 / TA_2)
        $nama = mysqli_real_escape_string($koneksi, trim($data[0]));
        $npm = mysqli_real_escape_string($koneksi, trim($data[1]));
        $status_ta = mysqli_real_escape_string($koneksi, trim($data[2]));

        // Validasi Status TA agar sesuai Enum Database
        if (strtoupper($status_ta) == 'TA1' || strtoupper($status_ta) == 'TA 1') $status_ta = 'TA_1';
        elseif (strtoupper($status_ta) == 'TA2' || strtoupper($status_ta) == 'TA 2') $status_ta = 'TA_2';
        else $status_ta = 'Bukan_TA';

        // Default Password = NPM (Plain text sesuai request sebelumnya)
        $password = $npm; 
        $email_dummy = $npm . "@student.univ.ac.id"; // Email dummy jika CSV tidak ada email

        if (!empty($npm)) {
            // Query INSERT ... ON DUPLICATE KEY UPDATE
            // Jika NPM sudah ada, kita update Status TA dan Created_At (agar masuk ke filter tahun ajaran baru)
            $query = "INSERT INTO peserta (nama_lengkap, npm, email, password, status_ta, created_at) 
                      VALUES ('$nama', '$npm', '$email_dummy', '$password', '$status_ta', NOW())
                      ON DUPLICATE KEY UPDATE 
                      nama_lengkap = VALUES(nama_lengkap),
                      status_ta = VALUES(status_ta),
                      created_at = NOW()"; // Update tanggal daftar ke SEKARANG
            
            if (mysqli_query($koneksi, $query)) {
                $success_count++;
            }
        }
        $row++;
    }

    fclose($handle);
    header("Location: /projek-ifws/PIC/admin_pesertaTA.php?status=import_sukses&count=$success_count");
    exit();
} else {
    header("Location: /projek-ifws/PIC/admin_pesertaTA.php");
    exit();
}
?>