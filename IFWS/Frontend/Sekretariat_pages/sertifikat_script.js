document.addEventListener('DOMContentLoaded', () => {
    // Data yang sama dengan halaman list IFWS, dengan status 'isRecapped'
    const webinarData = {
        '2025-Ganjil': [
            { id: 101, narasumber: 'John Doe', tanggal: '10/08/2025', topik: 'Introduction To 3D Computer Vision', jenis: 'Internal - Jurusan', isRecapped: true },
            { id: 102, narasumber: 'Joss Everman', tanggal: '12/08/2025', topik: 'Automation in Tech Industry', jenis: 'Internasional', isRecapped: false },
            { id: 103, narasumber: 'Jane Doe', tanggal: '15/08/2025', topik: 'Dunia ERP dan Odoo', jenis: 'Internal - Dosen', isRecapped: false },
            { id: 104, narasumber: 'Arthur Nightingale', tanggal: '20/08/2025', topik: 'Cloud Reliability', jenis: 'Umum', isRecapped: true },
            { id: 105, narasumber: 'Kushala Daora', tanggal: '22/08/2025', topik: 'Sistem Informasi Apotek Adora', jenis: 'Internal', isRecapped: true },
        ],
        '2024-Genap': [
            { id: 201, narasumber: 'Elon Musk', tanggal: '20/04/2024', topik: 'Masa Depan Antariksa', jenis: 'Internasional', isRecapped: true },
        ]
    };

    // --- DOM Elements ---
    const pilihTahunBtn = document.getElementById('pilihTahunBtn');
    const tahunDropdown = document.getElementById('tahunDropdown');
    const initialMessage = document.getElementById('initial-message');
    const webinarSection = document.getElementById('webinar-section');
    const tahunAkademikTitle = document.getElementById('tahunAkademikTitle');
    const webinarTableBody = document.getElementById('webinar-table-body');

    // --- Dropdown Logic (Sama seperti sebelumnya) ---
    pilihTahunBtn.addEventListener('click', () => {
        tahunDropdown.classList.toggle('show');
    });
    window.addEventListener('click', (event) => {
        if (!event.target.matches('.dropdown-btn')) {
            if (tahunDropdown.classList.contains('show')) {
                tahunDropdown.classList.remove('show');
            }
        }
    });
    tahunDropdown.addEventListener('click', (event) => {
        event.preventDefault();
        const selectedTahun = event.target.getAttribute('data-tahun');
        if (selectedTahun) {
            displayWebinars(selectedTahun);
            pilihTahunBtn.innerHTML = `${selectedTahun} <i class="fa-solid fa-chevron-down"></i>`;
            tahunDropdown.classList.remove('show');
        }
    });

    // --- FUNGSI UTAMA (dengan logika filter) ---
    function displayWebinars(tahun) {
        tahunAkademikTitle.textContent = `Tahun Akademik ${tahun}`;
        webinarTableBody.innerHTML = '';

        const data = webinarData[tahun] || [];
        
        // BARU: Filter data untuk hanya mengambil webinar yang sudah direkap
        const recappedWebinars = data.filter(webinar => webinar.isRecapped === true);

        if (recappedWebinars.length > 0) {
            recappedWebinars.forEach(webinar => {
                const row = document.createElement('tr');
                // Tombol "Upload" akan mengarah ke halaman kelola sertifikat
                row.innerHTML = `
                    <td>${webinar.narasumber}</td>
                    <td>${webinar.tanggal}</td>
                    <td>${webinar.jenis}</td>
                    <td>${webinar.topik}</td>
                    <td><a href="kelola_sertifikat.html?id=${webinar.id}" class="btn-upload">Upload</a></td>
                    `;
                webinarTableBody.appendChild(row);
            });
        } else {
             const row = document.createElement('tr');
             row.innerHTML = `<td colspan="5" style="text-align: center;">Tidak ada webinar yang sudah direkap untuk tahun ini.</td>`;
             webinarTableBody.appendChild(row);
        }

        initialMessage.classList.add('hidden');
        webinarSection.classList.remove('hidden');
    }
});