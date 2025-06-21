document.addEventListener('DOMContentLoaded', () => {
    // --- KONFIGURASI ---
    const MINIMUM_DURATION_MINUTES = 45;

    // --- DATABASE SEMU ---
    const fullWebinarData = {
        '101': {
            info: { id: 101, narasumber: 'John Doe', topik: 'Introduction To 3D Computer Vision' },
            narasumber: [ 
                { nama: 'John Doe', email: 'john.doe@example.com' } 
            ],
            peserta: [
                { nama: 'James Carter', email: 'james.carter@example.com', durasi: 60 },
                { nama: 'Sophia Anderson', email: 'sophia.anderson@example.com', durasi: 55 },
                { nama: 'Ethan Mitchell', email: 'ethan.mitchell@example.com', durasi: 30 },
                { nama: 'Olivia Roberts', email: 'olivia.roberts@example.com', durasi: 70 },
            ]
        },
    };

    // --- DOM Elements (SUDAH LENGKAP) ---
    const narsumUploadBtn = document.getElementById('upload-narsum-btn');
    const pesertaUploadBtn = document.getElementById('upload-peserta-btn');
    const narsumFileInput = document.getElementById('narsum-file-input');
    const pesertaFileInput = document.getElementById('peserta-file-input');
    const narsumFileName = document.getElementById('narsum-file-name');
    const pesertaFileName = document.getElementById('peserta-file-name');
    const narasumberTableBody = document.getElementById('narasumber-table-body');
    const pesertaTableBody = document.getElementById('peserta-table-body');
    const generateBtn = document.getElementById('generate-btn');
    const sendBtn = document.getElementById('send-btn');
    const infoNarasumberEl = document.getElementById('info-narasumber');
    const infoTopikEl = document.getElementById('info-topik');


    // --- Muat Data Awal ---
    const urlParams = new URLSearchParams(window.location.search);
    const webinarId = urlParams.get('id');
    const webinar = fullWebinarData[webinarId];

    if (webinar) {
        infoNarasumberEl.textContent = `Narasumber : ${webinar.info.narasumber}`;
        infoTopikEl.textContent = `Topik IFWS : ${webinar.info.topik}`;
        renderNarasumberTable(webinar.narasumber);
        renderPesertaTable(webinar.peserta);
    }

    function renderNarasumberTable(data) {
        narasumberTableBody.innerHTML = '';
        data.forEach(item => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${item.nama}</td>
                <td>${item.email}</td>
                <td>
                    <div class="sertifikat-cell">
                        <span class="sertifikat-status">Belum digenerate</span>
                        <button class="btn btn-preview" disabled>Preview</button>
                    </div>
                </td>
            `;
            narasumberTableBody.appendChild(row);
        });
    }

    function renderPesertaTable(data) {
        pesertaTableBody.innerHTML = '';
        data.forEach(item => {
            const row = document.createElement('tr');
            const isValid = item.durasi >= MINIMUM_DURATION_MINUTES;
            row.dataset.isValid = isValid; 

            const statusClass = isValid ? 'status-valid' : 'status-invalid';
            const statusText = isValid ? 'Valid' : 'Tidak Valid';
            const statusHtml = `<span class="status ${statusClass}">${statusText}</span>`;

            row.innerHTML = `
                <td>${item.nama}</td>
                <td>${item.email}</td>
                <td>${statusHtml}</td>
                <td>
                    <div class="sertifikat-cell">
                        <span class="sertifikat-status">Belum digenerate</span>
                        <button class="btn btn-preview" disabled>Preview</button>
                    </div>
                </td>
            `;
            pesertaTableBody.appendChild(row);
        });
    }

    // --- Logika Upload Template ---
    narsumUploadBtn.addEventListener('click', () => narsumFileInput.click());
    pesertaUploadBtn.addEventListener('click', () => pesertaFileInput.click());

    narsumFileInput.addEventListener('change', () => {
        narsumFileName.textContent = narsumFileInput.files[0] ? narsumFileInput.files[0].name : '';
    });
    pesertaFileInput.addEventListener('change', () => {
        pesertaFileName.textContent = pesertaFileInput.files[0] ? pesertaFileInput.files[0].name : '';
    });

    // --- Logika Generate Sertifikat (Simulasi) ---
    generateBtn.addEventListener('click', () => {
        if (!narsumFileInput.files[0] || !pesertaFileInput.files[0]) {
            alert('Harap upload kedua template sertifikat terlebih dahulu.');
            return;
        }

        alert('Memulai proses generate sertifikat...');
        setTimeout(() => {
            narasumberTableBody.querySelectorAll('tr').forEach(row => {
                row.querySelector('.sertifikat-status').textContent = 'Sertifikat_Generated.pdf';
                row.querySelector('.sertifikat-status').classList.add('generated');
                row.querySelector('.btn-preview').disabled = false;
            });

            pesertaTableBody.querySelectorAll('tr').forEach(row => {
                if (row.dataset.isValid === 'true') { 
                    row.querySelector('.sertifikat-status').textContent = 'Sertifikat_Generated.pdf';
                    row.querySelector('.sertifikat-status').classList.add('generated');
                    row.querySelector('.btn-preview').disabled = false;
                }
            });

            sendBtn.disabled = false;
            alert('Sertifikat untuk narasumber dan peserta yang valid berhasil di-generate!');
        }, 1500);
    });

    // --- Logika Tombol Lainnya ---
    document.querySelector('.main-content').addEventListener('click', (e) => {
        if (e.target.classList.contains('btn-preview') && !e.target.disabled) {
            alert('Menampilkan pratinjau sertifikat...');
        }
    });

    sendBtn.addEventListener('click', () => {
        alert('Email notifikasi dan sertifikat hanya terkirim ke narasumber dan peserta yang valid.');
    });
});