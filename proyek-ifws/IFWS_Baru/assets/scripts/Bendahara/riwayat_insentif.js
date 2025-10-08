document.addEventListener('DOMContentLoaded', function() {

    // ===== Elemen & Fungsi untuk Overlay Riwayat Narasumber =====
    const historyOverlay = document.getElementById('history-overlay');
    const closeHistoryBtn = document.getElementById('close-history-overlay-btn');
    const speakerNameEl = document.getElementById('speaker-name');
    const viewHistoryBtns = document.querySelectorAll('.view-history-btn');

    if (historyOverlay && closeHistoryBtn && speakerNameEl) {
        function openHistoryOverlay(speakerName) {
            speakerNameEl.textContent = speakerName;
            historyOverlay.classList.remove('hidden');
        }

        function closeHistoryOverlay() {
            historyOverlay.classList.add('hidden');
        }

        viewHistoryBtns.forEach(btn => {
            btn.addEventListener('click', function(event) {
                event.preventDefault();
                const speakerName = this.getAttribute('data-speaker-name');
                openHistoryOverlay(speakerName);
            });
        });

        closeHistoryBtn.addEventListener('click', closeHistoryOverlay);

        historyOverlay.addEventListener('click', function(event) {
            if (event.target === historyOverlay) {
                closeHistoryOverlay();
            }
        });
    }

    // ===== Elemen & Fungsi untuk Overlay Preview Gambar =====
    const posterOverlay = document.getElementById('poster-overlay');
    const closePosterBtn = document.getElementById('close-overlay-btn');
    const posterPreviewImg = document.getElementById('poster-preview-img');
    const viewPosterBtns = document.querySelectorAll('.view-poster-btn');

    if (posterOverlay && closePosterBtn && posterPreviewImg) {
        function openPosterOverlay(imageSrc) {
            if (imageSrc) {
                posterPreviewImg.src = imageSrc;
                posterOverlay.classList.remove('hidden');
            } else {
                console.error('Sumber gambar tidak ditemukan.');
            }
        }

        function closePosterOverlay() {
            posterOverlay.classList.add('hidden');
        }

        viewPosterBtns.forEach(btn => {
            btn.addEventListener('click', function(event) {
                event.preventDefault();
                const posterSrc = this.getAttribute('data-poster-src');
                openPosterOverlay(posterSrc);
            });
        });

        closePosterBtn.addEventListener('click', closePosterOverlay);

        posterOverlay.addEventListener('click', function(event) {
            if (event.target === posterOverlay) {
                closePosterOverlay();
            }
        });
    }
});