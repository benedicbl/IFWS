document.addEventListener('DOMContentLoaded', () => {
    const webinarForm = document.getElementById('webinar-form');

    webinarForm.addEventListener('submit', (e) => {
        // Mencegah form dari refresh halaman
        e.preventDefault();

        // Mengambil data dari form (contoh)
        const topik = document.getElementById('topik').value;
        const tanggal = document.getElementById('tanggal').value;

        // Validasi sederhana
        if (!topik || !tanggal) {
            alert('Harap isi semua kolom yang wajib diisi.');
            return;
        }

        // Di aplikasi nyata, Anda akan mengirim semua data form ke server.
        alert(`Webinar dengan topik "${topik}" berhasil ditambahkan!`);

        // Setelah submit, kembali ke halaman list
        window.location.href = 'list_IFWS.html';
    });
});