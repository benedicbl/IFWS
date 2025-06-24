<?php
    // Mengambil path dari URL untuk menentukan halaman aktif
    $current_page = basename($_SERVER['PHP_SELF']);
?>
<nav class="sidebar">
    <div class="sidebar-header">
        <a href="/proyek-ifws/index.php">
            <img src="/proyek-ifws/assets/images/Logo.jpg" alt="Logo IFWS" class="sidebar-logo">
        </a>
    </div>
    <ul class="sidebar-nav">
        <li class="<?php echo ($current_page == 'list_IFWS.php' || $current_page == 'tambah_webinar.php' || $current_page == 'edit_webinar.php' || $current_page == 'pilih_peserta.php') ? 'active' : ''; ?>">
            <a href="/proyek-ifws/pages/list_IFWS.php" title="List IFWS"><i class="fa-solid fa-list-ul fa-lg"></i></a>
        </li>
        <li class="<?php echo ($current_page == 'riwayat.php' || $current_page == 'upload_screenshot.php') ? 'active' : ''; ?>">
            <a href="/proyek-ifws/pages/riwayat.php" title="Riwayat & Upload Screenshot"><i class="fa-solid fa-chart-line fa-lg"></i></a>
        </li>
        <li class="<?php echo ($current_page == 'list_narasumber.php' || $current_page == 'tambah_narasumber.php' || $current_page == 'edit_narasumber.php') ? 'active' : ''; ?>">
            <a href="/proyek-ifws/pages/list_narasumber.php" title="List Narasumber"><i class="fa-solid fa-chalkboard-user fa-lg"></i></a>
        </li>
        <li class="<?php echo ($current_page == 'list_anggota.php' || $current_page == 'tambah_anggota.php' || $current_page == 'edit_anggota.php') ? 'active' : ''; ?>">
            <a href="/proyek-ifws/pages/list_anggota.php" title="List Anggota"><i class="fa-solid fa-users fa-lg"></i></a>
        </li>
    </ul>
    <ul class="sidebar-footer">
        <li><a href="#" title="Profil"><i class="fa-solid fa-user-circle fa-lg"></i></a></li>
        <li><a href="#" title="Dokumen"><i class="fa-regular fa-file-lines fa-lg"></i></a></li>
    </ul>
</nav>