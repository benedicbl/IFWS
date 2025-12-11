document.addEventListener('DOMContentLoaded', function() {
    const overlay = document.getElementById('link-overlay');
    const btnClose = document.getElementById('close-overlay');
    const overlaySubtitle = document.getElementById('overlay-subtitle');
    const linkTextarea = document.getElementById('link_textarea');
    const webinarIdInput = document.getElementById('webinar_id_input');
    const bukaButtons = document.querySelectorAll('.btn-buka-overlay');

    if (!overlay || !btnClose || !overlaySubtitle || !linkTextarea || !webinarIdInput) {
        console.error("Satu atau lebih elemen overlay tidak ditemukan!");
        return; // Hentikan jika elemen penting tidak ada
    }

    bukaButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Hanya buka jika tombol tidak disabled
            if (!this.disabled) { 
                const webinarId = this.dataset.id;
                const topik = this.dataset.topik;
                const currentLink = this.dataset.link;

                overlaySubtitle.textContent = `Silahkan masukkan link untuk webinar: "${topik}"`;
                webinarIdInput.value = webinarId;
                linkTextarea.value = currentLink;

                overlay.classList.remove('hidden');
            }
        });
    });

    btnClose.addEventListener('click', () => {
        overlay.classList.add('hidden');
    });

    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) {
            overlay.classList.add('hidden');
        }
    });
});