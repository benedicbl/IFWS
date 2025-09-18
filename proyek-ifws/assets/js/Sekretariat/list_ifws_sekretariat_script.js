document.addEventListener('DOMContentLoaded', () => {


    // --- Variabel Global ---
    let webinars = []; // Untuk menampung data webinar dari API
    let selectedYearData = null; // Untuk menyimpan tahun & semester yang dipilih


    // --- DOM Elements ---
    const pilihTahunBtn = document.getElementById('pilihTahunBtn');
    const tahunDropdown = document.getElementById('tahunDropdown');
    const initialMessage = document.getElementById('initial-message');
    const webinarSection = document.getElementById('webinar-section');
    const tahunAkademikTitle = document.getElementById('tahunAkademikTitle');
    const webinarTableBody = document.getElementById('webinar-table-body');


    // --- FUNGSI-FUNGSI ---

    /**
     * Mengisi pilihan dropdown tahun dari data API.
     */
    const populateTahunDropdown = (tahunList) => {
        tahunDropdown.innerHTML = ''; // Kosongkan pilihan
        if (tahunList && tahunList.length > 0) {
            tahunList.forEach(item => {
                const link = document.createElement('a');
                link.href = '#';
                link.dataset.tahun = `${item.tahun_akd}-${item.semester_akd}`;
                link.textContent = `${item.tahun_akd} - ${item.semester_akd}`;
                tahunDropdown.appendChild(link);
            });
        } else {
            tahunDropdown.innerHTML = '<a>Tidak ada data tahun.</a>';
        }
    };

    /**
     * Menampilkan data webinar yang sudah diambil ke dalam tabel.
     */
    const displayWebinars = () => {
        webinarTableBody.innerHTML = '';

        if (webinars && webinars.length > 0) {
            webinars.forEach(webinar => {

                const row = document.createElement('tr');
                
                // LOGIKA KONDISIONAL UNTUK TOMBOL
                let actionButtonHtml = '';

                // Asumsi API mengembalikan properti 'sudah_rekap'
                if (webinar.sudah_rekap) {
                    actionButtonHtml = `<a href="/proyek-ifws/pages/Sekretariat/lihat_rekap.php?id=${webinar.id}" class="btn-lihat">Lihat</a>`;
                } else {

                    actionButtonHtml = `<a href="/proyek-ifws/pages/Sekretariat/rekap_peserta.php?id=${webinar.id}" class="btn-rekap">Rekap</a>`;
                }

                row.innerHTML = `

                    <td>${webinar.narasumber || 'N/A'}</td>
                    <td>${new Date(webinar.tanggal).toLocaleDateString('id-ID', {day: '2-digit', month: 'long', year: 'numeric'})}</td>
                    <td>${webinar.jenis_webinar || 'N/A'}</td>
                    <td>${webinar.topik_webinar || 'N/A'}</td>
                    <td>${actionButtonHtml}</td>
                `;
                webinarTableBody.appendChild(row);
            });
        } else {

             webinarTableBody.innerHTML = `<tr><td colspan="5" style="text-align: center;">Tidak ada data webinar untuk tahun ini.</td></tr>`;
        }

        initialMessage.classList.add('hidden');
        webinarSection.classList.remove('hidden');
    }
  

    /**
     * Mengambil data webinar dari API untuk tahun & semester tertentu.
     */
    const fetchWebinars = (tahun, semester) => {
        webinarTableBody.innerHTML = `<tr><td colspan="5" style="text-align:center;">Memuat data...</td></tr>`;
        tahunAkademikTitle.textContent = `Tahun Akademik ${tahun} - ${semester}`;
       
        // Ganti dengan endpoint API yang sesuai untuk sekretariat
        fetch(`/proyek-ifws/api/get_ifws_for_sekretariat.php?tahun=${tahun}&semester=${semester}`)
            .then(response => response.json())
            .then(data => {
                webinars = data; // Simpan data ke variabel global
                displayWebinars(); // Tampilkan data
            })
            .catch(error => {
                console.error('Error fetching webinars:', error);
                webinarTableBody.innerHTML = `<tr><td colspan="5" style="text-align:center; color:red;">Gagal memuat data.</td></tr>`;
            });
    };


    // --- EVENT LISTENERS ---

    pilihTahunBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        tahunDropdown.classList.toggle('show');
    });
    
    tahunDropdown.addEventListener('click', (e) => {
        e.preventDefault();
        const link = e.target.closest('a');
        if (link && link.dataset.tahun) {
            const tahunData = link.getAttribute('data-tahun');
            const [tahun_akd, semester] = tahunData.split('-');
            
            selectedYearData = { tahun: tahun_akd, semester: semester };
            fetchWebinars(tahun_akd, semester); // Panggil fungsi fetch
            
            pilihTahunBtn.innerHTML = `${link.textContent} <i class="fa-solid fa-chevron-down"></i>`;
            tahunDropdown.classList.remove('show');
        }
    });

    window.addEventListener('click', (e) => {
        if (!e.target.closest('.dropdown')) {
            if (tahunDropdown.classList.contains('show')) {
                tahunDropdown.classList.remove('show');
            }
        }
    });

    // --- INISIALISASI HALAMAN ---
    // Mengisi dropdown saat halaman pertama kali dimuat
    fetch('/proyek-ifws/api/get_tahun_akademik.php')
        .then(response => response.json())
        .then(data => {
            populateTahunDropdown(data);
        })
        .catch(error => {
            console.error('Gagal memuat daftar tahun:', error);
            tahunDropdown.innerHTML = '<a>Gagal memuat data.</a>';
        });

});