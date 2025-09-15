document.addEventListener('DOMContentLoaded', () => {
    // Simulasi data yang sudah direkap
    const rekapData = {
        '101': [
            { nama: 'James Carter', email: '6182101032@student.unpar.ac.id', durasi: 60, jenis: 'Mahasiswa' },
            { nama: 'Sophia Anderson', email: '6182101057@student.unpar.ac.id', durasi: 60, jenis: 'Mahasiswa' },
        ],
        '104': [ /* data rekap lain */ ]
    };
    const webinarData = {
        '101': { narasumber: 'John Doe', topik: 'Introduction To 3D Computer Vision' },
        '104': { narasumber: 'Arthur Nightingale', topik: 'Cloud Reliability' },
    };
    const MINIMUM_DURATION_MINUTES = 45;

    const urlParams = new URLSearchParams(window.location.search);
    const webinarId = urlParams.get('id');
    const tableBody = document.getElementById('peserta-table-body');
    const editBtn = document.getElementById('edit-rekap-btn');

    if (webinarId && webinarData[webinarId]) {
        // ... (kode untuk isi info header webinar)

        // Atur link tombol Edit
        editBtn.href = `edit_rekap.html?id=${webinarId}`;

        const participants = rekapData[webinarId] || [];
        // ... (kode untuk displayParticipants seperti di rekap_peserta.js)
    } else {
        // ... (kode untuk handle webinar tidak ditemukan)
    }
});