document.addEventListener('DOMContentLoaded', () => {
    // --- KONFIGURASI (Aturan dari Teknisi) ---
    const MINIMUM_DURATION_MINUTES = 45;

    // --- DATABASE WEBINAR SEMU ---
    const webinarData = {
        '101': { id: 101, narasumber: 'John Doe', topik: 'Introduction To 3D Computer Vision' },
        '102': { id: 102, narasumber: 'Joss Everman', topik: 'Automation in Tech Industry' },
    };

    // --- DOM Elements ---
    const infoNarasumberEl = document.getElementById('info-narasumber');
    const infoTopikEl = document.getElementById('info-topik');
    const importCsvBtn = document.getElementById('import-csv-btn');
    const csvFileInput = document.getElementById('csv-file-input');
    const tableBody = document.getElementById('peserta-table-body');
    const emptyStateMsg = document.getElementById('empty-state-message');
    const saveBtn = document.getElementById('save-btn');

    // --- Muat Info Webinar Berdasarkan ID dari URL ---
    const urlParams = new URLSearchParams(window.location.search);
    const webinarId = urlParams.get('id');

    if (webinarId && webinarData[webinarId]) {
        const webinar = webinarData[webinarId];
        infoNarasumberEl.textContent = `Narasumber : ${webinar.narasumber}`;
        infoTopikEl.textContent = `Topik IFWS : ${webinar.topik}`;
    } else {
        infoNarasumberEl.textContent = 'Webinar Tidak Ditemukan';
    }

    // --- Fungsionalitas Tombol Import CSV ---
    importCsvBtn.addEventListener('click', () => {
        csvFileInput.click();
    });

    csvFileInput.addEventListener('change', (event) => {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const csvContent = e.target.result;
                const participants = parseCSV(csvContent);
                displayParticipants(participants);
            };
            reader.readAsText(file);
        }
    });

    // --- FUNGSI PARSE CSV YANG SUDAH DIPERBAIKI ---
    function parseCSV(csvText) {
        const lines = csvText.split('\n');

        // Cari baris header untuk menentukan titik awal data
        const headerIndex = lines.findIndex(line => line.includes("Name (original name)	Email	Total duration (minutes)	Guest	"));
        
        if (headerIndex === -1) {
            alert("Format CSV tidak dikenali. Header 'Name (Original Name)' tidak ditemukan.");
            return [];
        }
        
        const dataRows = lines.slice(headerIndex + 1);
        const participants = [];

        dataRows.forEach(row => {
            if (row.trim() === '') return;
            
            const cols = row.split('\t');
            
            // Memastikan baris punya cukup data
            if (cols.length >= 3) {
                // Indeks kolom disesuaikan dengan format data yang benar
                const nama = cols[0].trim(); // Indeks 0 untuk Nama
                const email = cols[1].trim().toLowerCase(); // Indeks 1 untuk Email
                const durasi = parseInt(cols[2]) || 0; // Indeks 2 untuk Durasi
                
                let jenis = 'Luar';

                // Logika baru untuk menentukan jenis berdasarkan email
                if (email.endsWith('@student.unpar.ac.id')) {
                    jenis = 'Mahasiswa';
                } else if (email.endsWith('@unpar.ac.id')) {
                    jenis = 'Dosen';
                }

                participants.push({
                    nama: nama,
                    email: cols[1].trim(), // Menggunakan email asli untuk ditampilkan
                    durasi: durasi,
                    jenis: jenis
                });
            }
        });
        return participants;
    }

    // --- Fungsi untuk Menampilkan Data ke Tabel ---
    function displayParticipants(participants) {
        tableBody.innerHTML = '';
        if (participants.length > 0) {
            emptyStateMsg.style.display = 'none';
        } else {
            emptyStateMsg.style.display = 'block';
            return;
        }

        participants.forEach(p => {
            const row = document.createElement('tr');
            const isPresent = p.durasi >= MINIMUM_DURATION_MINUTES;
            const statusClass = isPresent ? 'status-valid' : 'status-invalid';
            const statusText = isPresent ? 'Valid' : 'Tidak Valid';

            row.innerHTML = `
                <td>${p.nama}</td>
                <td>${p.email}</td>
                <td>${p.durasi} Menit</td>
                <td>${p.jenis}</td>
                <td><span class="status ${statusClass}">${statusText}</span></td>
            `;
            tableBody.appendChild(row);
        });
    }
    
    // --- Fungsionalitas Tombol Simpan ---
    saveBtn.addEventListener('click', () => {
        if(tableBody.children.length === 0) {
            alert('Tidak ada data untuk disimpan. Silakan impor file CSV terlebih dahulu.');
            return;
        }
        alert('Rekap kehadiran berhasil disimpan!');
        window.location.href = 'list_ifws_sekretariat.html';
    });
});