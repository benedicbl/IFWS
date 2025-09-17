document.addEventListener('DOMContentLoaded', () => {

    // --- SIMULASI DATA PENGGUNA YANG LOGIN ---
    // Di aplikasi nyata, data ini akan datang dari server setelah login.
    const currentUser = {
        nama: 'Nathaniel Cooper',
        npm: '6182101024',
        // Ubah nilai ini menjadi 'false' untuk melihat tampilan non-peserta TA
        isPesertaTA: true, 
        riwayat: [
            { id: 1, tanggal: '25 / 10 / 2025', topik: 'Real-World Examples of Cloud Reliability...', statusHadir: 'Valid', sertifikatUrl: '#download-link-1', tahunAkademik: 'GANJIL 2025/2026' },
            { id: 2, tanggal: '30 / 11 / 2025', topik: 'Web Rendering: A Journey Trough Man...', statusHadir: 'Valid', sertifikatUrl: '#download-link-2', tahunAkademik: 'GANJIL 2025/2026' },
            { id: 3, tanggal: '15 / 08 / 2025', topik: 'Sistem Informasi Apotek Adora', statusHadir: 'Tidak Valid', sertifikatUrl: null, tahunAkademik: 'GANJIL 2025/2026' },
            { id: 4, tanggal: '20 / 04 / 2024', topik: 'Masa Depan Antariksa', statusHadir: 'Valid', sertifikatUrl: '#download-link-3', tahunAkademik: 'GENAP 2023/2024' },
        ]
    };

    // --- DOM Elements ---
    const infoTaSection = document.getElementById('info-ta-section');
    const riwayatTableBody = document.getElementById('riwayat-table-body');
    const filterTahun = document.getElementById('filter-tahun');

    // --- 1. CEK & TAMPILKAN INFO PESERTA TA ---
    if (currentUser.isPesertaTA) {
        document.getElementById('nama-peserta').textContent = currentUser.nama;
        document.getElementById('npm-peserta').textContent = currentUser.npm;
        // Hitung total IFWS dengan status 'Valid'
        const totalValidIFWS = currentUser.riwayat.filter(item => item.statusHadir === 'Valid').length;
        document.getElementById('total-ifws').textContent = totalValidIFWS;
        
        infoTaSection.classList.remove('hidden');
    }

    // --- 2. ISI OPSI FILTER TAHUN AKADEMIK ---
    // Ambil semua tahun akademik unik dari data riwayat
    const tahunAkademikUnik = [...new Set(currentUser.riwayat.map(item => item.tahunAkademik))];
    tahunAkademikUnik.forEach(tahun => {
        const option = document.createElement('option');
        option.value = tahun;
        option.textContent = tahun;
        filterTahun.appendChild(option);
    });

    // --- 3. FUNGSI UNTUK MERENDER TABEL ---
    function renderTable(filter) {
        riwayatTableBody.innerHTML = ''; // Kosongkan tabel

        // Filter data riwayat berdasarkan tahun akademik yang dipilih
        const filteredData = currentUser.riwayat.filter(item => item.tahunAkademik === filter);

        if (filteredData.length === 0) {
            riwayatTableBody.innerHTML = `<tr><td colspan="4" style="text-align:center;">Tidak ada riwayat untuk periode ini.</td></tr>`;
            return;
        }

        filteredData.forEach(item => {
            const row = document.createElement('tr');

            // Tentukan kelas CSS dan tombol berdasarkan status kehadiran
            const statusClass = item.statusHadir === 'Valid' ? 'status-valid' : 'status-tidak-valid';
            const actionButton = item.statusHadir === 'Valid'
                ? `<a href="${item.sertifikatUrl}" class="btn-download" download>Download</a>`
                : `<button class="btn-detail" disabled>Detail</button>`;

            row.innerHTML = `
                <td>${item.tanggal}</td>
                <td class="${statusClass}">${item.statusHadir}</td>
                <td>${item.topik}</td>
                <td>${actionButton}</td>
            `;
            riwayatTableBody.appendChild(row);
        });
    }
    
    // --- 4. EVENT LISTENER UNTUK FILTER ---
    filterTahun.addEventListener('change', () => {
        renderTable(filterTahun.value);
    });

    // --- 5. RENDER TABEL PERTAMA KALI SAAT HALAMAN DIBUKA ---
    if (tahunAkademikUnik.length > 0) {
        renderTable(tahunAkademikUnik[0]);
    } else {
        riwayatTableBody.innerHTML = `<tr><td colspan="4" style="text-align:center;">Anda belum memiliki riwayat IFWS.</td></tr>`;
    }
});