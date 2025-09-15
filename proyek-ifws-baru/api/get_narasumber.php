<?php
// Mengatur header agar output berupa JSON
header('Content-Type: application/json');

// Memanggil file koneksi ke database
include '../includes/koneksi.php';

// Menyiapkan array untuk menampung data
$data = [];

// Query untuk mengambil semua data dari tabel narasumber, diurutkan berdasarkan nama
$sql = "SELECT id, nama, email FROM narasumber ORDER BY nama ASC";
$result = $koneksi->query($sql);

// Cek apakah query berhasil dan ada hasilnya
if ($result && $result->num_rows > 0) {
    // Looping untuk mengambil setiap baris data
    while($row = $result->fetch_assoc()) {
        // Menambahkan setiap baris ke dalam array $data
        $data[] = $row;
    }
}

// Menutup koneksi database
$koneksi->close();

// Mengubah array PHP menjadi format JSON dan menampilkannya
echo json_encode($data);
?>