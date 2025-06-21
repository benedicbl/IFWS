document.addEventListener('DOMContentLoaded', () => {
    // --- DATABASE SEMU ---
    const allWebinarData = {
        '101': {
            info: { topik: 'Introduction To 3D Computer Vision' },
            narasumber: [ 
                { id: 'narsum-1', nama: 'John Doe', statusPembayaran: 'Belum Lunas' } 
            ],
            panitia: [
                { id: 'panitia-1', nama: 'Benny Luswana', jabatan: 'PIC', statusPembayaran: 'Lunas' },
                { id: 'panitia-2', nama: 'Sekretaris Satu', jabatan: 'Sekretariat', statusPembayaran: 'Belum Lunas' },
            ]
        },
    };

    let currentEditing = { id: null, type: null };

    // --- DOM Elements ---
    const infoTopikEl = document.getElementById('info-topik');
    const narasumberTableBody = document.getElementById('narasumber-table-body');
    const panitiaTableBody = document.getElementById('panitia-table-body');
    const saveBtn = document.getElementById('save-btn');
    
    // Modal elements
    const modal = document.getElementById('upload-modal');
    const modalPersonName = document.getElementById('modal-person-name');
    const fileInput = document.getElementById('bukti-file-input');
    const cancelModalBtn = document.getElementById('cancel-modal-btn');
    const saveBuktiBtn = document.getElementById('save-bukti-btn');

    // --- Inisialisasi Halaman ---
    const urlParams = new URLSearchParams(window.location.search);
    const webinarId = urlParams.get('id');
    const webinar = allWebinarData[webinarId];

    if (webinar) {
        infoTopikEl.textContent = `Topik IFWS : ${webinar.info.topik}`;
        renderNarasumberTable();
        renderPanitiaTable();
    } else {
        infoTopikEl.textContent = 'Data Webinar Tidak Ditemukan';
    }

    // --- FUNGSI RENDER TABEL (DIPERBARUI) ---
    function renderNarasumberTable() {
        narasumberTableBody.innerHTML = '';
        webinar.narasumber.forEach(person => {
            const row = document.createElement('tr');
            const isLunas = person.statusPembayaran === 'Lunas';
            
            // LOGIKA BARU: Tentukan tombol mana yang akan ditampilkan
            const actionButtonHtml = isLunas
                ? `<button class="btn-lihat" data-nama="${person.nama}">Lihat</button>`
                : `<button class="btn-upload" data-id="${person.id}" data-type="narasumber" data-nama="${person.nama}">Upload</button>`;

            row.innerHTML = `
                <td>${person.nama}</td>
                <td><span class="status ${isLunas ? 'status-lunas' : 'status-belum-lunas'}">${person.statusPembayaran}</span></td>
                <td>${actionButtonHtml}</td>
            `;
            narasumberTableBody.appendChild(row);
        });
    }

    function renderPanitiaTable() {
        panitiaTableBody.innerHTML = '';
        webinar.panitia.forEach(person => {
            const row = document.createElement('tr');
            const isLunas = person.statusPembayaran === 'Lunas';

            // LOGIKA BARU: Tentukan tombol mana yang akan ditampilkan
            const actionButtonHtml = isLunas
                ? `<button class="btn-lihat" data-nama="${person.nama}">Lihat</button>`
                : `<button class="btn-upload" data-id="${person.id}" data-type="panitia" data-nama="${person.nama}">Upload</button>`;

            row.innerHTML = `
                <td>${person.nama}</td>
                <td>${person.jabatan}</td>
                <td><span class="status ${isLunas ? 'status-lunas' : 'status-belum-lunas'}">${person.statusPembayaran}</span></td>
                <td>${actionButtonHtml}</td>
            `;
            panitiaTableBody.appendChild(row);
        });
    }

    // --- LOGIKA MODAL ---
    function openModal(id, type, nama) {
        currentEditing = { id, type };
        modalPersonName.textContent = nama;
        fileInput.value = '';
        modal.classList.remove('hidden');
    }

    function closeModal() {
        modal.classList.add('hidden');
    }

    // EVENT LISTENER DIPERBARUI untuk menangani tombol 'Upload' dan 'Lihat'
    document.querySelector('.main-content').addEventListener('click', (e) => {
        // Jika tombol 'Upload' diklik
        if (e.target.classList.contains('btn-upload')) {
            const { id, type, nama } = e.target.dataset;
            openModal(id, type, nama);
        }
        // Jika tombol 'Lihat' diklik
        if (e.target.classList.contains('btn-lihat')) {
            const nama = e.target.dataset.nama;
            alert(`Mensimulasikan penampilan bukti bayar untuk: ${nama}`);
        }
    });

    cancelModalBtn.addEventListener('click', closeModal);

    saveBuktiBtn.addEventListener('click', () => {
        if (fileInput.files.length === 0) {
            alert('Harap pilih file bukti pembayaran terlebih dahulu.');
            return;
        }

        const { id, type } = currentEditing;
        let personToUpdate;
        if (type === 'narasumber') {
            personToUpdate = webinar.narasumber.find(p => p.id === id);
        } else {
            personToUpdate = webinar.panitia.find(p => p.id === id);
        }

        if (personToUpdate) {
            personToUpdate.statusPembayaran = 'Lunas';
        }

        alert(`Bukti pembayaran untuk ${modalPersonName.textContent} berhasil diupload!`);
        closeModal();
        
        if (type === 'narasumber') {
            renderNarasumberTable();
        } else {
            renderPanitiaTable();
        }
    });
    
    saveBtn.addEventListener('click', () => {
        alert('Semua perubahan status pembayaran telah disimpan!');
        window.location.href = 'upload_pembayaran.html';
    });
});