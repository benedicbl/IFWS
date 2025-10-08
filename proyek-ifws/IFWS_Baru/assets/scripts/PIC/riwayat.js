document.addEventListener('DOMContentLoaded', function() {

    // Ambil elemen-elemen yang dibutuhkan untuk overlay riwayat
    const historyOverlay = document.getElementById('history-overlay');
    const closeHistoryBtn = document.getElementById('close-history-overlay-btn');
    const speakerNameEl = document.getElementById('speaker-name');
    const viewHistoryBtns = document.querySelectorAll('.view-history-btn');

    // Pastikan semua elemen penting ada
    if (!historyOverlay || !closeHistoryBtn || !speakerNameEl) {
        console.error('Elemen untuk overlay riwayat tidak ditemukan!');
        return;
    }

    // Fungsi untuk membuka overlay riwayat
    function openHistoryOverlay(speakerName) {
        // Mengisi nama narasumber secara dinamis
        speakerNameEl.textContent = speakerName;
        
        // TODO: Di aplikasi nyata, di sini Anda akan mengambil data riwayat
        // spesifik untuk narasumber ini dan mengisi baris tabel (tbody).
        // Untuk sekarang, kita hanya menampilkan data statis yang sudah ada di HTML.
        
        historyOverlay.classList.remove('hidden');
    }

    // Fungsi untuk menutup overlay riwayat
    function closeHistoryOverlay() {
        historyOverlay.classList.add('hidden');
    }

    // Tambahkan event listener ke setiap tombol "Lihat"
    viewHistoryBtns.forEach(btn => {
        btn.addEventListener('click', function(event) {
            event.preventDefault();
            const speakerName = this.getAttribute('data-speaker-name');
            openHistoryOverlay(speakerName);
        });
    });

    // Event listener untuk tombol '< Back'
    closeHistoryBtn.addEventListener('click', closeHistoryOverlay);

    // Event listener untuk menutup saat klik background
    historyOverlay.addEventListener('click', function(event) {
        if (event.target === historyOverlay) {
            closeHistoryOverlay();
        }
    });

});