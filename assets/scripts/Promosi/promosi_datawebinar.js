document.addEventListener('DOMContentLoaded', function() {
    const posterOverlay = document.getElementById('poster-overlay');
    const posterPreviewImg = document.getElementById('poster-preview-img');
    const uploadOverlay = document.getElementById('upload-overlay');
    const uploadSubtitle = document.getElementById('upload-subtitle');
    const uploadWebinarIdInput = document.getElementById('upload_webinar_id');
    const uploadFileInput = document.getElementById('poster_file');

    // Event listener global untuk semua tombol close
    document.body.addEventListener('click', function(event) {
        if (event.target.closest('.btn-close-overlay')) {
            event.target.closest('.overlay').classList.add('hidden');
        }
        if (event.target.classList.contains('overlay')) {
             event.target.classList.add('hidden');
        }
    });

    // Event listener untuk tombol "Lihat Poster"
    document.querySelectorAll('.btn-lihat-poster').forEach(button => {
        button.addEventListener('click', function() {
            const posterPath = this.dataset.poster;
            if (posterPath && posterPreviewImg && posterOverlay) {
                posterPreviewImg.src = posterPath;
                posterOverlay.classList.remove('hidden');
            }
        });
    });

    // Event listener untuk tombol "Upload"
    document.querySelectorAll('.btn-buka-upload').forEach(button => {
        button.addEventListener('click', function() {
             // Hanya buka jika tombol tidak disabled
             if(!this.disabled) {
                const webinarId = this.dataset.id;
                const topik = this.dataset.topik;

                if (uploadWebinarIdInput && uploadSubtitle && uploadFileInput && uploadOverlay) {
                    uploadWebinarIdInput.value = webinarId;
                    uploadSubtitle.textContent = `Pilih file poster untuk webinar: "${topik}"`;
                    uploadFileInput.value = ''; // Reset input file
                    uploadOverlay.classList.remove('hidden');
                }
             }
        });
    });
});