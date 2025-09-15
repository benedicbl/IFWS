document.addEventListener('DOMContentLoaded', () => {
    // Data semu dengan properti 'posterFile'
    const webinarData = {
        '2025-Ganjil': [
            { id: 101, topik: 'Introduction To 3D Computer Vision', tanggal: '10/08/2025', posterFile: 'poster_3d_vision.jpg' },
            { id: 102, topik: 'Automation in Tech Industry', tanggal: '12/08/2025', posterFile: null },
        ],
        '2024-Genap': [
            { id: 201, topik: 'Masa Depan Antariksa', tanggal: '20/04/2024', posterFile: null },
        ]
    };

    let currentEditingId = null; 
    let selectedYear = null;
    
    // --- DOM Elements ---
    const pilihTahunBtn = document.getElementById('pilihTahunBtn');
    const tahunDropdown = document.getElementById('tahunDropdown');
    const tableBody = document.getElementById('webinar-table-body');
    const initialMessage = document.getElementById('initial-message');
    const dataSection = document.getElementById('data-section');
    
    // --- Modal Elements ---
    const modal = document.getElementById('poster-modal');
    const modalTitle = document.getElementById('modal-webinar-title');
    const modalFileInput = document.getElementById('poster-file-input');
    const cancelModalBtn = document.getElementById('cancel-modal-btn');
    const savePosterBtn = document.getElementById('save-poster-btn');

    // --- LOGIKA DROPDOWN (YANG HILANG SEBELUMNYA) ---
    pilihTahunBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        tahunDropdown.classList.toggle('show');
    });

    tahunDropdown.addEventListener('click', (e) => {
        e.preventDefault();
        const link = e.target.closest('a');
        if (link) {
            selectedYear = link.getAttribute('data-tahun');
            if (selectedYear) {
                displayWebinars(selectedYear);
                pilihTahunBtn.innerHTML = `${selectedYear} <i class="fa-solid fa-chevron-down"></i>`;
                tahunDropdown.classList.remove('show');
            }
        }
    });
    
    // Menutup dropdown jika klik di luar
    window.addEventListener('click', (e) => {
        if (!e.target.closest('.dropdown')) {
            if (tahunDropdown.classList.contains('show')) {
                tahunDropdown.classList.remove('show');
            }
        }
    });
    // --- AKHIR DARI LOGIKA DROPDOWN ---


    // --- Fungsi Utama untuk Menampilkan Data ---
    const displayWebinars = (tahun) => {
        const data = webinarData[tahun] || [];
        tableBody.innerHTML = '';
        if (data.length > 0) {
            data.forEach(webinar => {
                const row = document.createElement('tr');
                row.setAttribute('data-id', webinar.id);
                const posterStatus = webinar.posterFile 
                    ? `<span class="poster-status sudah-ada">${webinar.posterFile}</span>`
                    : `<span class="poster-status belum-ada">Belum Ada</span>`;
    
                row.innerHTML = `
                    <td>${webinar.topik}</td>
                    <td>${webinar.tanggal}</td>
                    <td>${posterStatus}</td>
                    <td><button class="btn-upload">Upload</button></td>
                `;
                tableBody.appendChild(row);
            });
        } else {
            tableBody.innerHTML = `<tr><td colspan="4" style="text-align:center;">Tidak ada data.</td></tr>`;
        }
        dataSection.classList.remove('hidden');
        initialMessage.classList.add('hidden');
    };

    // --- Logika Modal ---
    const openModal = (webinar) => {
        currentEditingId = webinar.id;
        modalTitle.textContent = webinar.topik;
        modalFileInput.value = '';
        modal.classList.remove('hidden');
    };
    const closeModal = () => modal.classList.add('hidden');

    tableBody.addEventListener('click', (e) => {
        if (e.target.classList.contains('btn-upload')) {
            const row = e.target.closest('tr');
            const webinarId = parseInt(row.dataset.id);
            let webinarToEdit;
            for (const year in webinarData) {
                const found = webinarData[year].find(w => w.id === webinarId);
                if (found) { webinarToEdit = found; break; }
            }
            if (webinarToEdit) openModal(webinarToEdit);
        }
    });
    
    cancelModalBtn.addEventListener('click', closeModal);
    savePosterBtn.addEventListener('click', () => {
        const file = modalFileInput.files[0];
        if (!file) {
            alert('Harap pilih file poster terlebih dahulu!');
            return;
        }

        if (currentEditingId !== null) {
            let webinarToUpdate;
            for (const year in webinarData) {
                const found = webinarData[year].find(w => w.id === currentEditingId);
                if (found) { webinarToUpdate = found; break; }
            }
            if (webinarToUpdate) {
                webinarToUpdate.posterFile = file.name;
                alert(`Poster "${file.name}" berhasil diupload!`);
                closeModal();
                displayWebinars(selectedYear);
            }
        }
    });
    
    // --- Inisialisasi Halaman ---
    const initializePage = () => {
        selectedYear = Object.keys(webinarData)[0];
        if (selectedYear) {
            displayWebinars(selectedYear);
            pilihTahunBtn.innerHTML = `${selectedYear} <i class="fa-solid fa-chevron-down"></i>`;
        } else {
            initialMessage.classList.remove('hidden');
            dataSection.classList.add('hidden');
        }
    };
    initializePage();
});