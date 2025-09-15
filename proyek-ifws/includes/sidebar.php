<?php
    // Mengambil path dari URL untuk menentukan halaman aktif
    $current_page = basename($_SERVER['PHP_SELF']);
    // Ambil role_id dari session untuk menentukan menu yang akan ditampilkan.
    // Default ke 1 (user) jika session tidak ada
    $role_id = $_SESSION['role_id'] ?? 1; 

    // Tentukan URL homepage default
    $homepage_url = '/proyek-ifws/pages/User/homepage_user.php'; 
    // Jika role adalah PIC
    if ($role_id == 2) {
        $homepage_url = '/proyek-ifws/pages/PIC/homepage_pic.php';
    }
    // Jika role adalah Sekretariat
    elseif ($role_id == 3) {
        $homepage_url = '/proyek-ifws/pages/Sekretariat/homepage_sekretariat.php';
    }
    // Jika role adalah Bendahara
    elseif ($role_id == 4) {
        $homepage_url = '/proyek-ifws/pages/Bendahara/homepage_bendahara.php';
    }
    // Jika role adalah Teknisi
    elseif ($role_id == 5) {
        $homepage_url = '/proyek-ifws/pages/Teknisi/homepage_teknisi.php';
    }
    // Jika role adalah Promosi
    elseif ($role_id == 6) {
        $homepage_url = '/proyek-ifws/pages/Promosi/homepage_promosi.php';
    }

?>
<nav class="sidebar">
    <div class="sidebar-header">
        <a href="<?php echo $homepage_url; ?>">
            <img src="/proyek-ifws/assets/images/Logo.jpg" alt="Logo IFWS" class="sidebar-logo">
        </a>
    </div>
    <ul class="sidebar-nav">
        <?php
        // Menu untuk page PIC
        if ($role_id == 2) :
        ?>
            <li class="<?php echo ($current_page == 'list_IFWS.php' || $current_page == 'tambah_webinar.php' || $current_page == 'edit_webinar.php' || $current_page == 'pilih_peserta.php') ? 'active' : ''; ?>">
                <a href="/proyek-ifws/pages/PIC/list_IFWS.php" title="List IFWS"><i class="fa-solid fa-list-ul fa-lg"></i></a>
            </li>
            <li class="<?php echo ($current_page == 'riwayat.php' || $current_page == 'upload_screenshot.php') ? 'active' : ''; ?>">
                <a href="/proyek-ifws/pages/PIC/riwayat.php" title="Riwayat & Upload Screenshot"><i class="fa-solid fa-chart-line fa-lg"></i></a>
            </li>
            <li class="<?php echo ($current_page == 'list_narasumber.php' || $current_page == 'tambah_narasumber.php' || $current_page == 'edit_narasumber.php') ? 'active' : ''; ?>">
                <a href="/proyek-ifws/pages/PIC/list_narasumber.php" title="List Narasumber"><i class="fa-solid fa-chalkboard-user fa-lg"></i></a>
            </li>
            <li class="<?php echo ($current_page == 'list_anggota.php' || $current_page == 'tambah_anggota.php' || $current_page == 'edit_anggota.php') ? 'active' : ''; ?>">
                <a href="/proyek-ifws/pages/PIC/list_anggota.php" title="List Anggota"><i class="fa-solid fa-users fa-lg"></i></a>
            </li>
        <?php
        // Menu untuk page sekretariat
        elseif ($role_id == 3) :
        ?>
            <!-- <li class="<?php echo ($current_page == 'list_ifws_teknisi.php') ? 'active' : ''; ?>">
                <a href="/proyek-ifws/pages/Teknisi/list_ifws_teknisi.php" title="List IFWS"><i class="fa-solid fa-list-ul fa-lg"></i></a>
            </li>
            <li class="<?php echo ($current_page == 'pengaturan_kehadiran.php') ? 'active' : ''; ?>">
                <a href="/proyek-ifws/pages/Teknisi/pengaturan_kehadiran.php" title="Pengaturan Kehadiran"><i class="fa-solid fa-user-clock fa-lg"></i></a>
            </li>
            <li class="<?php echo ($current_page == 'pengaturan_sidang.php') ? 'active' : ''; ?>">
                <a href="/proyek-ifws/pages/Teknisi/pengaturan_sidang.php" title="Pengaturan Sidang"><i class="fa-solid fa-user-graduate fa-lg"></i></a>
            </li> -->
            <?php
        // Menu untuk page bendahara
        elseif ($role_id == 4) :
        ?>
            <li class="<?php echo ($current_page == 'link-upload-pembayaran.php') ? 'active' : ''; ?>">
                <a href="/proyek-ifws/pages/Bendahara/upload_pembayaran.php" title="Upload Bukti Pembayaran"><i class="fa-solid fa-file-invoice-dollar fa-lg"></i></a></li>
        <?php
        // Menu untuk page teknisi
        elseif ($role_id == 5) :
        ?>
            <li class="<?php echo ($current_page == 'list_ifws_teknisi.php') ? 'active' : ''; ?>">
                <a href="/proyek-ifws/pages/Teknisi/list_ifws_teknisi.php" title="List IFWS"><i class="fa-solid fa-list-ul fa-lg"></i></a>
            </li>
            <li class="<?php echo ($current_page == 'pengaturan_kehadiran.php') ? 'active' : ''; ?>">
                <a href="/proyek-ifws/pages/Teknisi/pengaturan_kehadiran.php" title="Pengaturan Kehadiran"><i class="fa-solid fa-user-clock fa-lg"></i></a>
            </li>
            <li class="<?php echo ($current_page == 'pengaturan_sidang.php') ? 'active' : ''; ?>">
                <a href="/proyek-ifws/pages/Teknisi/pengaturan_sidang.php" title="Pengaturan Sidang"><i class="fa-solid fa-user-graduate fa-lg"></i></a>
            </li>
        <?php
        // Menu untuk page promosi
        elseif ($role_id == 6) :
        ?>
            <li class="<?php echo ($current_page == 'list_ifws_promosi.php') ? 'active' : ''; ?>">
                    <a href="/proyek-ifws/pages/Promosi/list_ifws_promosi.php" title="List IFWS"><i class="fa-solid fa-list-ul fa-lg"></i></a>
                </li>
            <?php
        // Menu untuk page user
        else :
        ?>
            <!-- <li class="<?php echo ($current_page == 'list_ifws_teknisi.php') ? 'active' : ''; ?>">
                <a href="/proyek-ifws/pages/Teknisi/list_ifws_teknisi.php" title="List IFWS"><i class="fa-solid fa-list-ul fa-lg"></i></a>
            </li>
            <li class="<?php echo ($current_page == 'pengaturan_kehadiran.php') ? 'active' : ''; ?>">
                <a href="/proyek-ifws/pages/Teknisi/pengaturan_kehadiran.php" title="Pengaturan Kehadiran"><i class="fa-solid fa-user-clock fa-lg"></i></a>
            </li>
            <li class="<?php echo ($current_page == 'pengaturan_sidang.php') ? 'active' : ''; ?>">
                <a href="/proyek-ifws/pages/Teknisi/pengaturan_sidang.php" title="Pengaturan Sidang"><i class="fa-solid fa-user-graduate fa-lg"></i></a>
            </li> -->
        <?php
        endif;
        ?>
    </ul>
    <ul class="sidebar-footer">
        <li><a href="#" title="Profil"><i class="fa-solid fa-user-circle fa-lg"></i></a></li>
        <li><a href="#" title="Dokumen"><i class="fa-regular fa-file-lines fa-lg"></i></a></li>
    </ul>
</nav>