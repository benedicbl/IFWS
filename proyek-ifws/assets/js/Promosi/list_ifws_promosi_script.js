document.addEventListener('DOMContentLoaded', () => {
    let currentEditingId = null;
    let selectedYearData = null;
    let webinars = [];

    // --- DOM Elements ---
    const pilihTahunBtn = document.getElementById('pilihTahunBtn');
    const tahunDropdown = document.getElementById('tahunDropdown');
    const tableBody = document.getElementById('webinar-table-body');
    const initialMessage = document.getElementById('initial-message');
    const dataSection = document.getElementById('data-section');
    
    const modal = document.getElementById('poster-modal');
    const modalTitle = document.getElementById('modal-webinar-title');
    const modalFileInput = document.getElementById('poster-file-input');
    const cancelModalBtn = document.getElementById('cancel-modal-btn');
    const savePosterBtn = document.getElementById('save-poster-btn');

    // --- Image Viewer Modal Elements ---
    const imageViewerModal = document.getElementById('image-viewer-modal');
    const fullPosterImage = document.getElementById('full-poster-image');
    const closeModalBtn = document.querySelector('.close-modal-btn');


    // --- FUNGSI ---

    const populateTahunDropdown = (tahunList) => {
        tahunDropdown.innerHTML = '';
        if (tahunList && tahunList.length > 0) {
            tahunList.forEach(item => {
                const link = document.createElement('a');
                link.href = '#';
                link.dataset.tahun = `${item.tahun_akd}-${item.semester_akd}`;
                link.textContent = `${item.tahun_akd} - ${item.semester_akd}`;
                tahunDropdown.appendChild(link);
            });
        } else {
            tahunDropdown.innerHTML = '<a>Tidak ada data.</a>';
        }
    };

    const displayWebinars = () => {
        tableBody.innerHTML = '';
        if (webinars && webinars.length > 0) {
            webinars.forEach(webinar => {
                const row = document.createElement('tr');
                row.setAttribute('data-id', webinar.id);
                // Menampilkan nama file poster jika ada
                let posterStatus = `<span class="poster-status belum-ada">Belum Ada</span>`;
                if (webinar.poster) {
                    // Tambahkan class 'viewable' untuk menandai gambar bisa diklik
                    posterStatus = `<img src="data:image/jpeg;base64,${webinar.poster}" alt="Poster" class="poster-thumbnail viewable" title="Klik untuk lihat ukuran penuh">`;
                }
                const formattedDate = new Date(webinar.tanggal).toLocaleDateString('id-ID', {
                    day: '2-digit', month: 'long', year: 'numeric'
                });
    
                row.innerHTML = `
                    <td>${webinar.topik_webinar}</td>
                    <td>${formattedDate}</td>
                    <td>${posterStatus}</td>
                    <td><button class="btn-upload">Upload / Ganti</button></td>
                `;
                tableBody.appendChild(row);
            });
        } else {
            tableBody.innerHTML = `<tr><td colspan="4" style="text-align:center;">Tidak ada data webinar.</td></tr>`;
        }
        dataSection.classList.remove('hidden');
        initialMessage.classList.add('hidden');
    };

    const fetchWebinars = (tahun, semester) => {
        tableBody.innerHTML = `<tr><td colspan="4" style="text-align:center;">Memuat data...</td></tr>`;
        fetch(`/proyek-ifws/api/get_ifws_for_promosi.php?tahun=${tahun}&semester=${semester}`)
            .then(response => response.json())
            .then(data => {
                webinars = data;
                displayWebinars();
            })
            .catch(error => console.error('Gagal memuat data webinar:', error));
    };

    const openModal = (webinar) => {
        currentEditingId = webinar.id;
        modalTitle.textContent = webinar.topik_webinar;
        modalFileInput.value = ''; // Reset input file
        modal.classList.remove('hidden');
    };
    
    const closeModal = () => modal.classList.add('hidden');

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
            fetchWebinars(tahun_akd, semester);
            pilihTahunBtn.innerHTML = `${link.textContent} <i class="fa-solid fa-chevron-down"></i>`;
            tahunDropdown.classList.remove('show');
        }
    });
    
    window.addEventListener('click', (e) => {
        if (!e.target.closest('.dropdown')) {
            tahunDropdown.classList.remove('show');
        }
    });

    tableBody.addEventListener('click', (e) => {
        if (e.target.classList.contains('btn-upload')) {
            const row = e.target.closest('tr');
            const webinarId = parseInt(row.dataset.id);
            const webinarToEdit = webinars.find(w => w.id === webinarId);
            if (webinarToEdit) openModal(webinarToEdit);
        }

        // LOGIKA BARU: Jika thumbnail gambar diklik
        if (e.target.classList.contains('viewable')) {
            fullPosterImage.src = e.target.src; // Set gambar di modal dengan gambar yang diklik
            imageViewerModal.classList.remove('hidden'); // Tampilkan modal
        }
    });
    
    cancelModalBtn.addEventListener('click', closeModal);

    // LOGIKA BARU: Event listener untuk menutup modal gambar
    const closeImageViewer = () => imageViewerModal.classList.add('hidden');
    closeModalBtn.addEventListener('click', closeImageViewer);
    imageViewerModal.addEventListener('click', (e) => {
        // Tutup modal jika area gelap di sekeliling gambar diklik
        if (e.target === imageViewerModal) {
            closeImageViewer();
        }
    });
    
    savePosterBtn.addEventListener('click', () => {
        const file = modalFileInput.files[0];
        if (!file) {
            alert('Harap pilih file poster terlebih dahulu!');
            return;
        }
        if (currentEditingId === null) return;

        // Gunakan FormData untuk mengirim file
        const formData = new FormData();
        formData.append('id_webinar', currentEditingId);
        formData.append('posterFile', file);

        savePosterBtn.textContent = 'Mengupload...';
        savePosterBtn.disabled = true;

        fetch('/proyek-ifws/api/upload_poster.php', {
            method: 'POST',
            body: formData // Kirim sebagai FormData
        })
        .then(response => response.json())
        .then(data => {
            alert(data.pesan);
            if (data.sukses) {
                closeModal();
                // Refresh data tabel
                fetchWebinars(selectedYearData.tahun, selectedYearData.semester);
            }
        })
        .catch(error => {
            console.error('Error saat upload:', error);
            alert('Terjadi kesalahan saat mengupload file.');
        })
        .finally(() => {
            savePosterBtn.textContent = 'Simpan';
            savePosterBtn.disabled = false;
        });
    });
    
    // --- Inisialisasi Halaman ---
    fetch('/proyek-ifws/api/get_tahun_akademik.php')
        .then(response => response.json())
        .then(data => populateTahunDropdown(data))
        .catch(error => console.error('Gagal memuat daftar tahun:', error));
});