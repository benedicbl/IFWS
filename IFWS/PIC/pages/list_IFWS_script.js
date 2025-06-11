document.addEventListener('DOMContentLoaded', () => {

    // --- FAKE DATA (Struktur data diperbarui untuk mendukung fitur edit) ---
    const webinarData = {
        '2025-Ganjil': [
            { id: 101, narasumber: 'John Doe', tanggal: '2025-08-10', jenis: 'Internal - Jurusan', topik: 'Introduction To 3D Computer Vision', jamMulai: '09:00', jamSelesai: '11:00', narasumberTerpilih: [{id: 1, nama: 'Nathaniel Cooper'}], panitiaTerpilih: [{id: 2, nama: 'Victoria Bennett', peran: 'Moderator'}] },
            { id: 102, narasumber: 'Joss Everman', tanggal: '2025-08-12', jenis: 'Internasional', topik: 'Automation in Tech Industry', jamMulai: '13:00', jamSelesai: '15:00', narasumberTerpilih: [{id: 4, nama: 'Ryan Reynolds'}], panitiaTerpilih: [] },
            { id: 103, narasumber: 'Jane Doe', tanggal: '2025-08-15', jenis: 'Internal - Dosen', topik: 'Dunia ERP dan Odoo', jamMulai: '10:00', jamSelesai: '12:00', narasumberTerpilih: [], panitiaTerpilih: [] }
        ],
        '2024-Genap': [
            { id: 201, narasumber: 'Elon Musk', tanggal: '2024-04-20', jenis: 'Internasional', topik: 'Masa Depan Antariksa', jamMulai: '19:00', jamSelesai: '21:00', narasumberTerpilih: [], panitiaTerpilih: [] }
        ],
        '2024-Ganjil': [],
        '2023-Genap': []
    };

    // --- DOM Elements (tidak berubah) ---
    const pilihTahunBtn = document.getElementById('pilihTahunBtn');
    const tahunDropdown = document.getElementById('tahunDropdown');
    const initialMessage = document.getElementById('initial-message');
    const webinarSection = document.getElementById('webinar-section');
    const tahunAkademikTitle = document.getElementById('tahunAkademikTitle');
    const webinarTableBody = document.getElementById('webinar-table-body');
    const toggleEditBtn = document.getElementById('toggleEditBtn');
    const actionsHeader = document.querySelector('.actions-header');

    // --- Dropdown & Year Selection Logic (tidak berubah) ---
    pilihTahunBtn.addEventListener('click', () => tahunDropdown.classList.toggle('show'));
    window.addEventListener('click', (event) => {
        if (!event.target.matches('.dropdown-btn') && tahunDropdown.classList.contains('show')) {
            tahunDropdown.classList.remove('show');
        }
    });
    tahunDropdown.addEventListener('click', (event) => {
        event.preventDefault();
        const selectedTahun = event.target.getAttribute('data-tahun');
        if (selectedTahun) {
            displayWebinars(selectedTahun);
            tahunDropdown.classList.remove('show');
        }
    });

    // --- FUNGSI TAMPIL WEBINAR (diperbarui) ---
    function displayWebinars(tahun) {
        tahunAkademikTitle.textContent = `Tahun Akademik ${tahun}`;
        webinarTableBody.innerHTML = '';
        webinarTableBody.classList.remove('edit-mode');
        actionsHeader.classList.add('hidden');
        toggleEditBtn.textContent = 'Edit';
        toggleEditBtn.style.backgroundColor = '#7f8c8d';

        const data = webinarData[tahun] || [];
        if (data.length > 0) {
            data.forEach(webinar => {
                const row = document.createElement('tr');
                // MENAMBAHKAN ATRIBUT 'data-id' PADA BARIS
                row.setAttribute('data-id', webinar.id);
                row.innerHTML = `
                    <td>${webinar.narasumber}</td>
                    <td>${new Date(webinar.tanggal).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' })}</td>
                    <td>${webinar.jenis}</td>
                    <td>${webinar.topik}</td>
                    <td class="row-actions">
                        <button class="action-btn edit-btn">Edit</button>
                        <button class="action-btn delete-btn">Delete</button>
                    </td>
                `;
                webinarTableBody.appendChild(row);
            });
        } else {
             const row = document.createElement('tr');
             row.innerHTML = `<td colspan="5" style="text-align: center;">Tidak ada data webinar untuk tahun ini.</td>`;
             webinarTableBody.appendChild(row);
        }
        initialMessage.classList.add('hidden');
        webinarSection.classList.remove('hidden');
    }
    
    // --- Toggle Edit Mode (tidak berubah) ---
    toggleEditBtn.addEventListener('click', () => {
        const isEditing = webinarTableBody.classList.toggle('edit-mode');
        actionsHeader.classList.toggle('hidden', !isEditing);
        toggleEditBtn.textContent = isEditing ? 'Selesai' : 'Edit';
        toggleEditBtn.style.backgroundColor = isEditing ? '#2ecc71' : '#7f8c8d';
    });

    // --- LOGIKA TOMBOL BARIS (diperbarui) ---
    webinarTableBody.addEventListener('click', (event) => {
        const target = event.target;
        
        // AKSI TOMBOL EDIT BARU
        if (target.classList.contains('edit-btn')) {
            const row = target.closest('tr');
            const webinarId = row.getAttribute('data-id');
            // Arahkan ke halaman edit dengan membawa ID
            window.location.href = `edit_webinar.html?id=${webinarId}`;
        }

        if (target.classList.contains('delete-btn')) {
            const row = target.closest('tr');
            if (confirm('Apakah Anda yakin ingin menghapus webinar ini?')) {
                row.remove();
                alert('Webinar telah dihapus.');
            }
        }
    });
});