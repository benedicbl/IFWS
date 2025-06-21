document.addEventListener('DOMContentLoaded', () => {
    // Database semu yang sama
    const webinarData = {
        '101': { id: 101, narasumber: 'John Doe', tanggal: '2025-08-10', jenis: 'Internal - Jurusan', topik: 'Introduction To 3D Computer Vision', jamMulai: '09:00', jamSelesai: '11:00', narasumberTerpilih: [{id: 1, nama: 'Nathaniel Cooper'}], panitiaTerpilih: [{id: 2, nama: 'Victoria Bennett', peran: 'Moderator'}] },
        '102': { id: 102, narasumber: 'Joss Everman', tanggal: '2025-08-12', jenis: 'Internasional', topik: 'Automation in Tech Industry', jamMulai: '13:00', jamSelesai: '15:00', narasumberTerpilih: [{id: 4, nama: 'Ryan Reynolds'}], panitiaTerpilih: [] },
        '103': { id: 103, narasumber: 'Jane Doe', tanggal: '2025-08-15', jenis: 'Internal - Dosen', topik: 'Dunia ERP dan Odoo', jamMulai: '10:00', jamSelesai: '12:00', narasumberTerpilih: [], panitiaTerpilih: [] },
        '201': { id: 201, narasumber: 'Elon Musk', tanggal: '2024-04-20', jenis: 'Internasional', topik: 'Masa Depan Antariksa', jamMulai: '19:00', jamSelesai: '21:00', narasumberTerpilih: [], panitiaTerpilih: [] }
    };

    // DOM Elements
    const form = document.getElementById('webinar-form');
    const pilihPesertaBtn = document.getElementById('pilih-peserta-btn');
    const tanggalInput = document.getElementById('tanggal');
    const jenisSelect = document.getElementById('jenis-ifws');
    const jamMulaiInput = document.getElementById('jam-mulai');
    const jamSelesaiInput = document.getElementById('jam-selesai');
    const topikTextarea = document.getElementById('topik');

    const urlParams = new URLSearchParams(window.location.search);
    const webinarId = urlParams.get('id');

    if (webinarId && webinarData[webinarId]) {
        const webinar = webinarData[webinarId];
        tanggalInput.value = webinar.tanggal;
        jamMulaiInput.value = webinar.jamMulai;
        jamSelesaiInput.value = webinar.jamSelesai;
        topikTextarea.value = webinar.topik;
        pilihPesertaBtn.href = `pilih_peserta.html?webinarId=${webinarId}`;
        
        const jenisOpsi = ['Internal - Jurusan', 'Internal - Dosen', 'Umum', 'Internasional'];
        jenisOpsi.forEach(opsi => {
            const option = document.createElement('option');
            option.value = opsi;
            option.textContent = opsi;
            if (opsi === webinar.jenis) {
                option.selected = true;
            }
            jenisSelect.appendChild(option);
        });
    } else {
        alert('Webinar tidak ditemukan!');
        window.location.href = 'list_IFWS.html';
    }

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        alert(`Data webinar (ID: ${webinarId}) berhasil diperbarui!`);
        window.location.href = 'list_IFWS.html';
    });
});