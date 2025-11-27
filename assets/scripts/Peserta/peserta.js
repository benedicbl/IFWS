document.addEventListener('DOMContentLoaded', function() {
    const overlay = document.getElementById('webinar-detail-overlay');
    if (!overlay) return; 

    const btnClose = overlay.querySelector('.btn-close-overlay');
    
    // Elemen di dalam overlay
    const overlayPoster = document.getElementById('detail-poster');
    const overlayTitle = document.getElementById('detail-title');
    const overlayNarasumber = document.getElementById('detail-narasumber');
    const overlayKategori = document.getElementById('detail-kategori');
    const overlayTanggal = document.getElementById('detail-tanggal');
    const overlayWaktu = document.getElementById('detail-waktu');
    const overlayDeskripsi = document.getElementById('detail-deskripsi');

    // PERUBAHAN: Event listener sekarang ada di tombol, bukan di kartu
    document.querySelectorAll('.btn-open-detail').forEach(button => {
        button.addEventListener('click', function() {
            // Ambil data dari parent .webinar-card
            const card = this.closest('.webinar-card');
            if (!card) return;
            
            const data = card.dataset;

            // Isi overlay dengan data
            overlayTitle.textContent = data.topik || 'Detail Webinar';
            overlayNarasumber.textContent = data.narasumber || '-';
            overlayKategori.textContent = data.kategori || '-';
            overlayTanggal.textContent = data.tanggal || '-';
            overlayWaktu.textContent = data.waktu || '-';
            overlayDeskripsi.textContent = data.deskripsi || 'Tidak ada deskripsi.';
            
            if (data.poster) {
                overlayPoster.src = '/projek-ifws/' + data.poster;
                overlayPoster.style.display = 'block';
            } else {
                overlayPoster.style.display = 'none'; 
            }

            overlay.classList.remove('hidden');
        });
    });

    // Fungsi untuk menutup overlay
    function closeOverlay() {
        overlay.classList.add('hidden');
    }

    if (btnClose) {
        btnClose.addEventListener('click', closeOverlay);
    }
    
    overlay.addEventListener('click', function(event) {
        if (event.target === overlay) {
            closeOverlay();
        }
    });
});