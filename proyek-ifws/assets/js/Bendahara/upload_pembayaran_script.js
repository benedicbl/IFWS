document.addEventListener('DOMContentLoaded', () => {
    // STRUKTUR DATA DIPERBARUI
    const pembayaranData = {
        '2025-Ganjil': [
            { id: 101, topik: 'Introduction To 3D Computer Vision', tanggal: '10/08/2025', narasumber: 'John Doe', jenis: 'Internal - Jurusan' },
            { id: 102, topik: 'Automation in Tech Industry', tanggal: '12/08/2025', narasumber: 'Joss Everman', jenis: 'Internasional' },
        ],
        '2024-Genap': [
            { id: 201, topik: 'Masa Depan Antariksa', tanggal: '20/04/2024', narasumber: 'Elon Musk', jenis: 'Internasional' },
        ]
    };

    let selectedYear = null;
    
    // --- DOM Elements ---
    const pilihTahunBtn = document.getElementById('pilihTahunBtn');
    const tahunDropdown = document.getElementById('tahunDropdown');
    const tableBody = document.getElementById('pembayaran-table-body');
    const initialMessage = document.getElementById('initial-message');
    const dataSection = document.getElementById('data-section');

    // --- LOGIKA DROPDOWN YANG DIPERBAIKI ---
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
                displayData(selectedYear);
                pilihTahunBtn.innerHTML = `${selectedYear} <i class="fa-solid fa-chevron-down"></i>`;
                tahunDropdown.classList.remove('show');
            }
        }
    });
    
    window.addEventListener('click', (e) => {
        if (!e.target.closest('.dropdown')) {
            if (tahunDropdown.classList.contains('show')) {
                tahunDropdown.classList.remove('show');
            }
        }
    });

    // --- FUNGSI TAMPIL DATA (DIPERBARUI TOTAL) ---
    const displayData = (tahun) => {
        const data = pembayaranData[tahun] || [];
        tableBody.innerHTML = '';
        
        if (data.length > 0) {
            data.forEach(webinar => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${webinar.narasumber}</td>
                    <td>${webinar.tanggal}</td>
                    <td>${webinar.jenis}</td>
                    <td>${webinar.topik}</td>
                    <td>
                        <a href="form_upload_pembayaran.html?id=${webinar.id}" class="btn-upload">Upload/Lihat</a>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        } else {
            tableBody.innerHTML = `<tr><td colspan="5" style="text-align:center;">Tidak ada data untuk tahun akademik ini.</td></tr>`;
        }
        dataSection.classList.remove('hidden');
        initialMessage.classList.add('hidden');
    };

    // --- Inisialisasi Halaman ---
    const initializePage = () => {
        selectedYear = Object.keys(pembayaranData)[0];
        if (selectedYear) {
            displayData(selectedYear);
            pilihTahunBtn.innerHTML = `${selectedYear} <i class="fa-solid fa-chevron-down"></i>`;
        }
    };

    initializePage();
});