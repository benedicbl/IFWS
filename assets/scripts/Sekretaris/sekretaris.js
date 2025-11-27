document.addEventListener('DOMContentLoaded', function() {
    // Cari tombol dan overlay di halaman
    const btnBukaUpload = document.getElementById('btn-buka-upload');
    const uploadOverlay = document.getElementById('upload-csv-overlay');
    
    // Pastikan kedua elemen ada
    if (btnBukaUpload && uploadOverlay) {
        
        // Tampilkan overlay saat tombol "Import CSV" diklik
        btnBukaUpload.addEventListener('click', function() {
            uploadOverlay.classList.remove('hidden');
        });

        // Temukan tombol close (X) di dalam overlay
        const btnClose = uploadOverlay.querySelector('.btn-close-overlay');
        if (btnClose) {
            btnClose.addEventListener('click', function() {
                uploadOverlay.classList.add('hidden');
            });
        }

        // Tambahkan event listener untuk menutup overlay saat mengklik background
        uploadOverlay.addEventListener('click', function(event) {
            // Cek apakah yang diklik adalah background overlay (bukan konten di dalamnya)
            if (event.target === uploadOverlay) {
                uploadOverlay.classList.add('hidden');
            }
        });
    }
});