document.addEventListener('DOMContentLoaded', () => {
    // --- Data Semu ---
    const webinarData = {
        '2025-Ganjil': [
            { id: 101, topik: 'Introduction To 3D Computer Vision', narasumber: 'John Doe', tanggal: '10/08/2025', linkZoom: null },
            { id: 102, topik: 'Automation in Tech Industry', narasumber: 'Joss Everman', tanggal: '12/08/2025', linkZoom: 'https://zoom.us/j/123456789' },
        ],
        '2024-Genap': [
            { id: 201, topik: 'Masa Depan Antariksa', narasumber: 'Elon Musk', tanggal: '20/04/2024', linkZoom: 'https://zoom.us/j/987654321' },
        ]
    };

    let currentEditingId = null; 
    let selectedYear = null;
    
    // --- DOM Elements ---
    const pilihTahunBtn = document.getElementById('pilihTahunBtn');
    const tahunDropdown = document.getElementById('tahunDropdown');
    const initialMessage = document.getElementById('initial-message');
    const dataSection = document.getElementById('data-section');
    const tableBody = document.getElementById('webinar-table-body');
    
    // --- Modal Elements ---
    const modal = document.getElementById('zoom-link-modal');
    const modalTitle = document.getElementById('modal-webinar-title');
    const modalInput = document.getElementById('zoom-link-input');
    const cancelModalBtn = document.getElementById('cancel-modal-btn');
    const saveLinkBtn = document.getElementById('save-link-btn');

    // --- FUNGSI-FUNGSI ---

    // Fungsi untuk menampilkan data webinar ke tabel
    const displayWebinars = (tahun) => {
        const data = webinarData[tahun] || [];
        tableBody.innerHTML = '';

        if (data.length > 0) {
            data.forEach(webinar => {
                const row = document.createElement('tr');
                row.setAttribute('data-id', webinar.id);
                const linkDisplay = webinar.linkZoom 
                    ? `<a href="${webinar.linkZoom}" target="_blank" class="link-zoom">Lihat Link</a>`
                    : `<span class="link-status belum-ada">Belum ada</span>`;
    
                row.innerHTML = `
                    <td>${webinar.topik}</td>
                    <td>${webinar.narasumber}</td>
                    <td>${webinar.tanggal}</td>
                    <td class="link-zoom-cell">${linkDisplay}</td>
                    <td><button class="btn-update">Update</button></td>
                `;
                tableBody.appendChild(row);
            });
        } else {
            tableBody.innerHTML = `<tr><td colspan="5" style="text-align:center;">Tidak ada data untuk tahun akademik ini.</td></tr>`;
        }
        
        dataSection.classList.remove('hidden');
        initialMessage.classList.add('hidden');
    };

    // Fungsi untuk membuka modal pop-up
    const openUpdateModal = (webinar) => {
        currentEditingId = webinar.id;
        modalTitle.textContent = webinar.topik;
        modalInput.value = webinar.linkZoom || '';
        modal.classList.remove('hidden');
        modalInput.focus();
    };

    // Fungsi untuk menutup modal pop-up
    const closeUpdateModal = () => {
        modal.classList.add('hidden');
        currentEditingId = null;
    };

    // --- EVENT LISTENERS ---

    // Event Listener untuk Dropdown
    pilihTahunBtn.addEventListener('click', (e) => {
        e.stopPropagation(); // Mencegah event lain terpanggil
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

    // Event Listener untuk membuka modal saat tombol "Update" di tabel diklik
    tableBody.addEventListener('click', (e) => {
        const updateButton = e.target.closest('.btn-update');
        if (updateButton) {
            const row = updateButton.closest('tr');
            const webinarId = parseInt(row.dataset.id);
            
            let webinarToEdit;
            for (const year in webinarData) {
                const found = webinarData[year].find(w => w.id === webinarId);
                if (found) {
                    webinarToEdit = found;
                    break;
                }
            }
            if (webinarToEdit) {
                openUpdateModal(webinarToEdit);
            }
        }
    });
    
    // Event Listener untuk tombol-tombol di dalam modal
    cancelModalBtn.addEventListener('click', closeUpdateModal);
    saveLinkBtn.addEventListener('click', () => {
        if (currentEditingId !== null) {
            let webinarToUpdate;
            for (const year in webinarData) {
                const found = webinarData[year].find(w => w.id === currentEditingId);
                if (found) {
                    webinarToUpdate = found;
                    break;
                }
            }
            if (webinarToUpdate) {
                webinarToUpdate.linkZoom = modalInput.value.trim();
                alert('Link Zoom berhasil diperbarui!');
                closeUpdateModal();
                displayWebinars(selectedYear); // Refresh tabel
            }
        }
    });

    // Event Listener global untuk menutup dropdown jika klik di luar area
    window.addEventListener('click', (e) => {
        if (!e.target.closest('.dropdown')) {
            if (tahunDropdown.classList.contains('show')) {
                tahunDropdown.classList.remove('show');
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
            initialMessage.textContent = 'Tidak ada data webinar yang tersedia.';
            initialMessage.classList.remove('hidden');
            dataSection.classList.add('hidden');
        }
    };

    initializePage();
});