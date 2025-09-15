document.addEventListener('DOMContentLoaded', () => {

    // --- Variabel Global ---
    let currentEditingId = null;
    let selectedYearData = null;
    let webinars = [];

    // --- DOM Elements ---
    const pilihTahunBtn = document.getElementById('pilihTahunBtn');
    const tahunDropdown = document.getElementById('tahunDropdown');
    const dataSection = document.getElementById('data-section');
    const tableBody = document.getElementById('webinar-table-body');
    
    // --- Modal Elements ---
    const modal = document.getElementById('zoom-link-modal');
    const modalTitle = document.getElementById('modal-webinar-title');
    const modalInput = document.getElementById('zoom-link-input');
    const cancelModalBtn = document.getElementById('cancel-modal-btn');
    const saveLinkBtn = document.getElementById('save-link-btn');

    // --- FUNGSI-FUNGSI ---

    /**
     * Mengisi pilihan dropdown tahun akademik dari data yang diambil dari API.
     * @param {Array} tahunList - Array objek dari API get_tahun_akademik.php
     */
    const populateTahunDropdown = (tahunList) => {
        tahunDropdown.innerHTML = ''; // Kosongkan pilihan yang ada
        if (tahunList && tahunList.length > 0) {
            tahunList.forEach(item => {
                const link = document.createElement('a');
                link.href = '#';
                // Menyimpan data gabungan di atribut data-tahun (contoh: "2023/2024-Ganjil")
                link.dataset.tahun = `${item.tahun_akd}-${item.semester_akd}`;
                // Teks yang akan dilihat oleh pengguna
                link.textContent = `${item.tahun_akd} - ${item.semester_akd}`;
                tahunDropdown.appendChild(link);
            });
        } else {
            tahunDropdown.innerHTML = '<a>Tidak ada data tahun tersedia.</a>';
        }
    };

    /**
     * Menampilkan data webinar ke dalam tabel HTML.
     */
    const displayWebinars = () => {
        tableBody.innerHTML = '';

        if (webinars && webinars.length > 0) {
            webinars.forEach(webinar => {
                const row = document.createElement('tr');
                row.setAttribute('data-id', webinar.id);

                // --- LOGIKA BARU UNTUK MEMPERBAIKI LINK ---
                let finalLink = webinar.link_webinar || '';
                let linkDisplay = `<span class="link-status belum-ada">Belum ada</span>`;
 
                if (finalLink.trim() !== '') {
                    // Cek jika link tidak diawali http:// atau https://
                    if (!finalLink.startsWith('http://') && !finalLink.startsWith('https://')) {
                        finalLink = 'https://' + finalLink;
                    }
                    linkDisplay = `<a href="${finalLink}" target="_blank" class="link-zoom">Lihat Link</a>`;
                }
    
                const formattedDate = new Date(webinar.tanggal).toLocaleDateString('id-ID', {
                    day: '2-digit', month: 'long', year: 'numeric'
                });

                row.innerHTML = `
                    <td>${webinar.topik_webinar || 'N/A'}</td>
                    <td>(Data Narasumber Belum Ada)</td>
                    <td>${formattedDate}</td>
                    <td class="link-zoom-cell">${linkDisplay}</td>
                    <td><button class="btn-update">Update</button></td>
                `;
                tableBody.appendChild(row);
            });
        } else {
            tableBody.innerHTML = `<tr><td colspan="5" style="text-align:center;">Tidak ada data webinar untuk tahun akademik ini.</td></tr>`;
        }
        
    };

    /**
     * Membuka modal untuk memperbarui link Zoom.
     * @param {Object} webinar - Objek webinar yang akan diedit.
     */
    const openUpdateModal = (webinar) => {
        currentEditingId = webinar.id;
        modalTitle.textContent = webinar.topik_webinar;
        modalInput.value = webinar.link_webinar || '';
        modal.classList.remove('hidden');
        modalInput.focus();
    };

    /**
     * Menutup modal.
     */
    const closeUpdateModal = () => {
        modal.classList.add('hidden');
        currentEditingId = null;
    };

    /**
     * Mengambil data webinar dari API untuk tahun & semester tertentu.
     * @param {string} tahun - Tahun akademik (contoh: "2023/2024")
     * @param {string} semester - Semester (contoh: "Ganjil")
     */
    const fetchWebinars = (tahun, semester) => {
        tableBody.innerHTML = `<tr><td colspan="5" style="text-align:center;">Memuat data...</td></tr>`;
       
        fetch(`/proyek-ifws/api/get_ifws_list.php?tahun=${tahun}&semester=${semester}`)
            .then(response => response.json())
            .then(data => {
                webinars = data;
                displayWebinars();
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                tableBody.innerHTML = `<tr><td colspan="5" style="text-align:center; color:red;">Gagal memuat data.</td></tr>`;
            });
    };

    // --- EVENT LISTENERS ---

    // Event listener untuk tombol utama dropdown
    pilihTahunBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        tahunDropdown.classList.toggle('show');
    });

    // Event listener untuk pilihan di dalam dropdown
    tahunDropdown.addEventListener('click', (e) => {
        e.preventDefault();
        const link = e.target.closest('a');
        if (link && link.dataset.tahun) {
            const tahunData = link.getAttribute('data-tahun'); // Format: "YYYY/YYYY-Semester"
            
            // Memecah string kembali menjadi tahun dan semester
            const [tahun_akd, semester] = tahunData.split('-');
            
            selectedYearData = { tahun: tahun_akd, semester: semester };
            fetchWebinars(tahun_akd, semester);
            
            pilihTahunBtn.innerHTML = `${link.textContent} <i class="fa-solid fa-chevron-down"></i>`;
            tahunDropdown.classList.remove('show');
        }
    });

    // Event listener untuk tombol "Update" di setiap baris tabel
    tableBody.addEventListener('click', (e) => {
        const updateButton = e.target.closest('.btn-update');
        if (updateButton) {
            const row = updateButton.closest('tr');
            const webinarId = parseInt(row.dataset.id);
            const webinarToEdit = webinars.find(w => w.id === webinarId);
            if (webinarToEdit) {
                openUpdateModal(webinarToEdit);
            }
        }
    });
    
    // Event listener untuk tombol-tombol di dalam modal
    cancelModalBtn.addEventListener('click', closeUpdateModal);

    saveLinkBtn.addEventListener('click', () => {
        if (currentEditingId !== null) {
            saveLinkBtn.textContent = 'Menyimpan...';
            saveLinkBtn.disabled = true;

            const newLink = modalInput.value.trim();

            fetch('/proyek-ifws/api/update_zoom_link.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: currentEditingId, linkZoom: newLink })
            })
            .then(response => response.json())
            .then(data => {
                if(data.sukses) {
                    alert(data.pesan);
                    // Refresh data tabel setelah berhasil menyimpan
                    fetchWebinars(selectedYearData.tahun, selectedYearData.semester);
                } else {
                    alert('Error: ' + data.pesan);
                }
            })
            .catch(error => {
                console.error('Error saving link:', error);
                alert('Gagal terhubung ke server.');
            })
            .finally(() => {
                closeUpdateModal();
                saveLinkBtn.textContent = 'Simpan';
                saveLinkBtn.disabled = false;
            });
        }
    });

    // Event listener global untuk menutup dropdown jika klik di luar area
    window.addEventListener('click', (e) => {
        if (!e.target.closest('.dropdown')) {
            if (tahunDropdown.classList.contains('show')) {
                tahunDropdown.classList.remove('show');
            }
        }
    });
    
    // --- INISIALISASI HALAMAN ---
    // Panggil API untuk mengisi dropdown saat halaman pertama kali dimuat
    fetch('/proyek-ifws/api/get_tahun_akademik.php')
        .then(response => response.json())
        .then(data => {
            populateTahunDropdown(data);
        })
        .catch(error => {
            console.error('Gagal memuat daftar tahun akademik:', error);
            tahunDropdown.innerHTML = '<a>Gagal memuat data.</a>';
        });
});