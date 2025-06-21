document.addEventListener('DOMContentLoaded', () => {

    // --- FAKE DATA (Diperbarui dengan properti 'screenshots') ---
    const webinarData = {
        '2025-Ganjil': [
            // Contoh dengan screenshot yang sudah ada
            { id: 101, narasumber: 'John Doe', tanggal: 'DD / MM / YY', jenis: 'Internal - Jurusan', topik: 'Introduction To 3D Computer Vision', screenshots: ['ss1.jpg', 'ss2.jpg'] },
            // Contoh dengan screenshot kosong
            { id: 102, narasumber: 'Joss Everman', tanggal: 'DD / MM / YY', jenis: 'Internasional', topik: 'Automation in Tech Industry', screenshots: [] },
            { id: 103, narasumber: 'Jane Doe', tanggal: 'DD / MM / YY', jenis: 'Internal - Dosen', topik: 'Dunia ERP dan Odoo', screenshots: [] },
            // Contoh lain dengan screenshot yang sudah ada
            { id: 104, narasumber: 'Arthur Nightingale', tanggal: 'DD / MM / YY', jenis: 'Umum', topik: 'Real-World Examples of Cloud Reliability...', screenshots: ['ss_arthur.jpg'] },
            { id: 105, narasumber: 'Kushala Daora', tanggal: 'DD / MM / YY', jenis: 'Internal', topik: 'Sistem Informasi Apotek Adora', screenshots: [] },
            { id: 106, narasumber: 'Jane Doe', tanggal: 'DD / MM / YY', jenis: 'Internal', topik: 'Web Rendering: A Journey Trough Man...', screenshots: [] }
        ],
        '2024-Genap': [
            { id: 201, narasumber: 'Elon Musk', tanggal: 'DD / MM / YY', jenis: 'Internasional', topik: 'Masa Depan Antariksa', screenshots: ['spacex.jpg'] },
            { id: 202, narasumber: 'Budi Hartono', tanggal: 'DD / MM / YY', jenis: 'Umum', topik: 'Strategi Investasi Jangka Panjang', screenshots: [] }
        ],
        '2024-Ganjil': [
            { id: 301, narasumber: 'Nadiem Makarim', tanggal: 'DD / MM / YY', jenis: 'Internal - Dosen', topik: 'Inovasi Pendidikan di Era Digital', screenshots: ['gojek.jpg'] }
        ],
        '2023-Genap': []
    };

    // --- DOM Elements ---
    const pilihTahunBtn = document.getElementById('pilihTahunBtn');
    const tahunDropdown = document.getElementById('tahunDropdown');
    const initialMessage = document.getElementById('initial-message');
    const webinarSection = document.getElementById('webinar-section');
    const tahunAkademikTitle = document.getElementById('tahunAkademikTitle');
    const webinarTableBody = document.getElementById('webinar-table-body');

    // --- Dropdown Logic (Tidak berubah) ---
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

    // --- Year Selection Logic (Tidak berubah) ---
    tahunDropdown.addEventListener('click', (event) => {
        event.preventDefault();
        const selectedTahun = event.target.getAttribute('data-tahun');
        if (selectedTahun) {
            displayWebinars(selectedTahun);
            tahunDropdown.classList.remove('show');
        }
    });

    // --- FUNGSI UTAMA YANG DIMODIFIKASI ---
    function displayWebinars(tahun) {
        tahunAkademikTitle.textContent = `Tahun Akademik ${tahun}`;
        webinarTableBody.innerHTML = '';

        const data = webinarData[tahun] || [];
        
        if (data.length > 0) {
            data.forEach(webinar => {
                const row = document.createElement('tr');
                
                // --- LOGIKA BARU UNTUK TOMBOL ---
                let actionButtonHtml = '';
                // Periksa apakah array screenshots ada dan tidak kosong
                if (webinar.screenshots && webinar.screenshots.length > 0) {
                    // Jika ada screenshot, tampilkan tombol "Lihat" berwarna hijau
                    actionButtonHtml = `<a href="upload_screenshot.html?id=${webinar.id}" class="btn-lihat">Lihat</a>`;
                } else {
                    // Jika tidak ada, tampilkan tombol "Upload" berwarna biru
                    actionButtonHtml = `<a href="upload_screenshot.html?id=${webinar.id}" class="upload-btn">Upload</a>`;
                }

                // Masukkan HTML tombol ke dalam baris tabel
                row.innerHTML = `
                    <td>${webinar.narasumber}</td>
                    <td>${webinar.tanggal}</td>
                    <td>${webinar.jenis}</td>
                    <td>${webinar.topik}</td>
                    <td>
                        ${actionButtonHtml}
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
});