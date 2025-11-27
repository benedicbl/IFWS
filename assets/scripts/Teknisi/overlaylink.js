/* ================================== */
/* FUNGSI UNTUK OVERLAY LINK AKSES    */
/* ================================== */
document.addEventListener('DOMContentLoaded', function() {
    // Ambil elemen-elemen untuk overlay link
    const linkOverlay = document.getElementById('link-overlay');
    const closeLinkBtn = document.getElementById('close-link-overlay-btn');
    const updateLinkBtns = document.querySelectorAll('.btn-update');
    const linkOverlayTitleSpan = document.querySelector('#link-overlay-title span');

    // Periksa apakah elemen ada sebelum menambahkan event listener
    if (linkOverlay && closeLinkBtn && updateLinkBtns.length > 0) {
        
        // Fungsi untuk membuka overlay link
        function openLinkOverlay(topic) {
            linkOverlayTitleSpan.textContent = topic; // Atur judul dinamis
            linkOverlay.classList.remove('hidden');
        }

        // Fungsi untuk menutup overlay link
        function closeLinkOverlay() {
            linkOverlay.classList.add('hidden');
        }

        // Tambahkan event listener ke setiap tombol "Update" di tabel
        updateLinkBtns.forEach(btn => {
            btn.addEventListener('click', function(event) {
                event.preventDefault();
                const topic = this.getAttribute('data-topic');
                openLinkOverlay(topic);
            });
        });

        // Event listener untuk tombol '< Back' di overlay link
        closeLinkBtn.addEventListener('click', closeLinkOverlay);

        // Event listener untuk menutup saat klik background
        linkOverlay.addEventListener('click', function(event) {
            if (event.target === linkOverlay) {
                closeLinkOverlay();
            }
        });
    }

    // ... (kode JavaScript untuk overlay poster sebelumnya bisa tetap ada di sini jika masih diperlukan) ...
});