document.addEventListener('DOMContentLoaded', function() {
    const buktiOverlay = document.getElementById('bukti-overlay');
    const buktiPreviewImg = document.getElementById('bukti-preview-img');
    const uploadOverlay = document.getElementById('upload-overlay');
    const uploadSubtitle = document.getElementById('upload-subtitle');
    const uploadWebinarIdInput = document.getElementById('upload_webinar_id');
    const uploadFileInput = document.getElementById('bukti_file');

    document.querySelectorAll('.btn-close-overlay').forEach(button => {
        button.addEventListener('click', () => button.closest('.overlay').classList.add('hidden'));
    });

    document.querySelectorAll('.btn-lihat-bukti').forEach(button => {
        button.addEventListener('click', function() {
            const buktiPath = this.dataset.bukti;
            if (buktiPath) {
                buktiPreviewImg.src = buktiPath;
                buktiOverlay.classList.remove('hidden');
            }
        });
    });

    document.querySelectorAll('.btn-buka-upload').forEach(button => {
        button.addEventListener('click', function() {
            uploadWebinarIdInput.value = this.dataset.id;
            uploadSubtitle.textContent = `Pilih file bukti untuk webinar: "${this.dataset.topik}"`;
            uploadFileInput.value = '';
            uploadOverlay.classList.remove('hidden');
        });
    });
});