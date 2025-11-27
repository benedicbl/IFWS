<?php
require_once '../includes/config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin' || !isset($_GET['id'])) {
    echo json_encode(['error' => 'Akses ditolak atau ID tidak valid.']);
    exit();
}

$webinar_id = (int)$_GET['id'];
$response = [];

// Ambil data utama webinar
$query_webinar = "SELECT * FROM webinars WHERE id = ?";
$stmt_webinar = mysqli_prepare($koneksi, $query_webinar);
mysqli_stmt_bind_param($stmt_webinar, "i", $webinar_id);
mysqli_stmt_execute($stmt_webinar);
$result_webinar = mysqli_stmt_get_result($stmt_webinar);
if ($webinar = mysqli_fetch_assoc($result_webinar)) {
    $response['webinar'] = $webinar;
}
mysqli_stmt_close($stmt_webinar);

// Ambil ID narasumber yang terkait
$query_narsum = "SELECT id_narasumber FROM webinar_narasumber WHERE id_webinar = ?";
$stmt_narsum = mysqli_prepare($koneksi, $query_narsum);
mysqli_stmt_bind_param($stmt_narsum, "i", $webinar_id);
mysqli_stmt_execute($stmt_narsum);
$result_narsum = mysqli_stmt_get_result($stmt_narsum);
$narasumber_ids = [];
while ($row = mysqli_fetch_assoc($result_narsum)) {
    $narasumber_ids[] = $row['id_narasumber'];
}
$response['narasumber_ids'] = $narasumber_ids;
mysqli_stmt_close($stmt_narsum);

echo json_encode($response);
?>