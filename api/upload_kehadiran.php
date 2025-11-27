<?php
require_once '../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'sekretaris') {
    die('Akses ditolak.');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file_csv']) && isset($_POST['id_webinar'])) {
    
    $id_webinar = (int)$_POST['id_webinar'];
    $file_tmp = $_FILES['file_csv']['tmp_name'];

    // 1. Ambil pengaturan durasi minimal
    $query_settings = "SELECT setting_value FROM settings WHERE setting_key = 'min_duration'";
    $result_settings = mysqli_query($koneksi, $query_settings);
    $min_duration = (int)(mysqli_fetch_assoc($result_settings)['setting_value'] ?? 45);

    // 2. Hapus data kehadiran lama (kecuali narasumber) untuk webinar ini
    $query_delete = "DELETE FROM kehadiran WHERE id_webinar = ? AND role != 'narasumber'";
    $stmt_delete = mysqli_prepare($koneksi, $query_delete);
    mysqli_stmt_bind_param($stmt_delete, "i", $id_webinar);
    mysqli_stmt_execute($stmt_delete);
    mysqli_stmt_close($stmt_delete);

    // 3. Masukkan data Narasumber dari database (sesuai permintaan)
    $query_narsum_db = "SELECT n.id, n.nama, n.email FROM narasumber n 
                        JOIN webinar_narasumber wn ON n.id = wn.id_narasumber 
                        WHERE wn.id_webinar = ?";
    $stmt_narsum_db = mysqli_prepare($koneksi, $query_narsum_db);
    mysqli_stmt_bind_param($stmt_narsum_db, "i", $id_webinar);
    mysqli_stmt_execute($stmt_narsum_db);
    $result_narsum_db = mysqli_stmt_get_result($stmt_narsum_db);
    
    $query_insert_kehadiran = "INSERT INTO kehadiran 
                                (id_webinar, id_narasumber, nama_lengkap, email, role, durasi_kehadiran, status_kehadiran) 
                                VALUES (?, ?, ?, ?, 'narasumber', 0, 'hadir')
                                ON DUPLICATE KEY UPDATE 
                                nama_lengkap = VALUES(nama_lengkap), durasi_kehadiran = 0, status_kehadiran = 'hadir'";
    $stmt_insert_narsum = mysqli_prepare($koneksi, $query_insert_kehadiran);
    
    while ($narsum = mysqli_fetch_assoc($result_narsum_db)) {
        mysqli_stmt_bind_param($stmt_insert_narsum, "iiss", $id_webinar, $narsum['id'], $narsum['nama'], $narsum['email']);
        mysqli_stmt_execute($stmt_insert_narsum);
    }
    mysqli_stmt_close($stmt_insert_narsum);
    mysqli_stmt_close($stmt_narsum_db);

    $total_diperbarui = 0;
    
    // 4. Buka dan baca file CSV (Tab-delimited)
    if (($handle = fopen($file_tmp, "r")) !== FALSE) {
        
        $query_insert = "INSERT INTO kehadiran (id_webinar, id_peserta, nama_lengkap, email, role, durasi_kehadiran, status_kehadiran) 
                         VALUES (?, ?, ?, ?, ?, ?, ?)
                         ON DUPLICATE KEY UPDATE 
                         nama_lengkap = VALUES(nama_lengkap), durasi_kehadiran = VALUES(durasi_kehadiran), status_kehadiran = VALUES(status_kehadiran), role = VALUES(role), id_peserta = VALUES(id_peserta)";
        $stmt_insert = mysqli_prepare($koneksi, $query_insert);
        
        $query_get_peserta = "SELECT id FROM peserta WHERE email = ?";
        $stmt_get_peserta = mysqli_prepare($koneksi, $query_get_peserta);

        // Lewati 3 baris header file CSV
        fgetcsv($handle, 1000, "\t");
        fgetcsv($handle, 1000, "\t");
        fgetcsv($handle, 1000, "\t");

        while (($data = fgetcsv($handle, 1000, "\t")) !== FALSE) {
            if (count($data) < 3) continue; 
            
            $name_col = $data[0] ?? '';
            $email = strtolower(trim($data[1] ?? ''));
            $durasi = (int)($data[2] ?? 0);

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) continue;

            // --- LOGIKA FILTER EMAIL (SESUAI PERMINTAAN) ---
            $domain = substr(strrchr($email, "@"), 1);
            if ($domain !== 'unpar.ac.id' && $domain !== 'student.unpar.ac.id') {
                continue; // Lewati email eksternal
            }

            // --- LOGIKA EKSTRAKSI NAMA (SESUAI PERMINTAAN) ---
            $real_name = $name_col;
            if (preg_match('/(.*?)\s*\((.*?)\)/', $name_col, $matches)) {
                // Jika format "Nama (Nama Asli)" -> ambil "Nama Asli"
                $real_name = trim($matches[2]); 
            }
            // Jika nama di kurung kosong atau masih ada NPM, coba cari di luar kurung
            if (empty($real_name) || str_starts_with(trim($real_name), '618')) {
                $name_parts = explode('(', $name_col);
                $name_before_paren = trim($name_parts[0]);
                if (!empty($name_before_paren)) {
                    // Cek format "NPM - Nama"
                    if(preg_match('/^[0-9\s-]+(.*?)$/', $name_before_paren, $matches_npm)) {
                        $real_name = trim($matches_npm[1]); // Ambil bagian nama saja
                    } else {
                        $real_name = $name_before_paren; // Ambil nama utuh di luar kurung
                    }
                }
            }
            // Hapus NPM dan tanda strip jika masih ada di depan
            $real_name = trim(preg_replace('/^[0-9\s-]+/', '', $real_name));
            if(empty($real_name)) $real_name = $email; // Fallback jika semua gagal

            // --- LOGIKA PERAN (PANITIA / PESERTA) ---
            $id_peserta = NULL;
            $role = 'peserta'; // Default
            if (str_ends_with($email, '@unpar.ac.id')) {
                $role = 'panitia';
            } else {
                // Cari ID Peserta di tabel 'peserta'
                mysqli_stmt_bind_param($stmt_get_peserta, "s", $email);
                mysqli_stmt_execute($stmt_get_peserta);
                $result_peserta = mysqli_stmt_get_result($stmt_get_peserta);
                if ($peserta = mysqli_fetch_assoc($result_peserta)) {
                    $id_peserta = $peserta['id'];
                }
            }

            // --- Tentukan Status Kehadiran ---
            $status_kehadiran = ($durasi >= $min_duration) ? 'hadir' : 'tidak_hadir';
            if ($role == 'panitia') {
                $status_kehadiran = 'hadir'; // Panitia selalu valid
            }

            // Masukkan ke database
            mysqli_stmt_bind_param($stmt_insert, "iisssis", 
                $id_webinar, $id_peserta, $real_name, $email, $role, $durasi, $status_kehadiran
            );
            mysqli_stmt_execute($stmt_insert);
            $total_diperbarui++;
        }
        
        fclose($handle);
        mysqli_stmt_close($stmt_insert);
        mysqli_stmt_close($stmt_get_peserta);
    }

    header('Location: /projek-ifws/Sekretaris/sekretaris_rekapperserta.php?id_webinar=' . $id_webinar . '&status=csv_sukses&total=' . $total_diperbarui);
    exit();
} else {
    header('Location: /projek-ifws/Sekretaris/sekretaris_datawebinar.php?status=gagal_upload');
    exit();
}
?>