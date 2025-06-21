document.addEventListener('DOMContentLoaded', () => {
    // ... (Definisi rekapData, webinarData, MINIMUM_DURATION_MINUTES sama seperti lihat_rekap.js)

    let participants = []; // Simpan data peserta di variabel agar bisa dimanipulasi

    if (webinarId && webinarData[webinarId]) {
        // ... (logika isi info header)

        // Atur link tombol Kembali
        document.getElementById('kembali-btn').href = `lihat_rekap.html?id=${webinarId}`;

        participants = [...(rekapData[webinarId] || [])]; // Salin data agar bisa dihapus
        displayParticipants(participants);
    }

    function displayParticipants(data) {
        tableBody.innerHTML = '';
        data.forEach((p, index) => {
            const row = document.createElement('tr');
            // ... (logika status valid/tidak valid)
            row.innerHTML = `
                <td>${p.nama}</td>
                <td>${p.email}</td>
                <td>${p.durasi} Menit</td>
                <td>${p.jenis}</td>
                <td><span class="status ${statusClass}">${statusText}</span></td>
                <td><button class="btn btn-delete" data-index="${index}">Delete</button></td>
            `;
            tableBody.appendChild(row);
        });
    }

    // Event listener untuk hapus
    tableBody.addEventListener('click', (e) => {
        if (e.target.classList.contains('btn-delete')) {
            const index = e.target.dataset.index;
            participants.splice(index, 1); // Hapus data dari array
            displayParticipants(participants); // Tampilkan ulang tabel
        }
    });

    // Event listener untuk simpan
    document.getElementById('save-btn').addEventListener('click', () => {
        alert('Perubahan berhasil disimpan!');
        window.location.href = `lihat_rekap.html?id=${webinarId}`;
    });
});