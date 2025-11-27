document.addEventListener('DOMContentLoaded', function() {
    // Menemukan elemen-elemen yang diperlukan
    const btnTambah = document.getElementById('btn-tambah');
    const overlay = document.getElementById('tambah-overlay');
    const btnClose = document.getElementById('close-overlay');

    // Pastikan semua elemen ada sebelum menambahkan event listener
    if (btnTambah && overlay && btnClose) {
        // Tampilkan overlay ketika tombol 'Tambah' diklik
        btnTambah.addEventListener('click', () => {
            overlay.classList.remove('hidden');
        });

        // Sembunyikan overlay ketika tombol 'Close' (X) diklik
        btnClose.addEventListener('click', () => {
            overlay.classList.add('hidden');
        });

        // Sembunyikan overlay jika area di luar kontennya diklik
        overlay.addEventListener('click', (event) => {
            if (event.target === overlay) {
                overlay.classList.add('hidden');
            }
        });
    }
});