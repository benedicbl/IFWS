document.addEventListener('DOMContentLoaded', function() {
    console.log("Script admin_riwayatifws.js Dimuat.");

    // --- Elemen Global ---
    const initialUploadOverlay = document.getElementById('initial-upload-overlay');
    const initialPreviewContainer = document.getElementById('initial-image-preview-container');
    const initialFileInput = document.getElementById('initial-file-input');
    const initialWebinarIdInput = document.getElementById('initial_webinar_id');
    const btnTriggerFileInput = document.getElementById('btn-trigger-file-input');
    const btnLanjutkanUpload = document.getElementById('btn-lanjutkan-upload');
    const noImagesTextDefault = '<p class="no-images-selected">Belum ada gambar yang dipilih.</p>';

    // Overlay Slider & Elemennya
    const viewSliderOverlay = document.getElementById('view-slider-overlay');
    const sliderImage = document.getElementById('slider-image');
    const sliderPrevBtn = document.getElementById('slider-prev');
    const sliderNextBtn = document.getElementById('slider-next');
    const btnDeletePhoto = document.getElementById('btn-delete-current-photo');

    let currentImageIndex = 0;
    let imagesForSlider = [];
    let currentWebinarIdForSlider = null;
    let selectedFiles = []; // Menyimpan File object untuk upload

    // ==========================================================
    // BAGIAN 1: EVENT LISTENER UTAMA (DELEGASI UNTUK SEMUA KLIK)
    // ==========================================================
    document.body.addEventListener('click', function(event) {
        const target = event.target;
        const targetClosest = (selector) => target.closest(selector);

        // --- Logika Tombol Buka Overlay ---
        const openUploadBtn = targetClosest('.btn-open-initial-upload');
        if (openUploadBtn) {
            handleOpenInitialUpload(openUploadBtn);
            return; // Hentikan eksekusi lebih lanjut
        }

        const viewPhotosBtn = targetClosest('.btn-view-photos');
        if (viewPhotosBtn) {
            handleViewPhotos(viewPhotosBtn);
            return;
        }

        // --- Logika Tombol di Dalam Overlay Awal ---
        if (targetClosest('#btn-trigger-file-input')) {
            if (initialFileInput) initialFileInput.click();
            return;
        }
        if (targetClosest('#btn-lanjutkan-upload')) {
            handleFinalUploadConfirm();
            return;
        }
        const removePreviewBtn = targetClosest('.btn-remove-preview');
        if (removePreviewBtn) {
            handleRemovePreview(removePreviewBtn);
            return;
        }

        // --- Logika Tombol di Dalam Overlay Konfirmasi (jika ada) ---
        // (Saat ini tidak ada, karena konfirmasi dihapus)

         // --- Logika Tombol di Dalam Overlay Slider ---
         if (targetClosest('#slider-prev')) {
            if(imagesForSlider.length > 0) {
                 currentImageIndex = (currentImageIndex - 1 + imagesForSlider.length) % imagesForSlider.length;
                 updateSliderView();
            }
            return;
         }
         if (targetClosest('#slider-next')) {
            if(imagesForSlider.length > 0) {
                 currentImageIndex = (currentImageIndex + 1) % imagesForSlider.length;
                 updateSliderView();
            }
             return;
         }
         if (targetClosest('#btn-delete-current-photo')) {
            handleDeletePhoto();
            return;
         }

        // --- Logika Tombol Tutup Overlay ---
        if (target.classList.contains('overlay')) {
            target.classList.add('hidden');
        }
        if (targetClosest('.btn-close-overlay')) {
            targetClosest('.overlay').classList.add('hidden');
        }
    });


    // ==========================================================
    // BAGIAN 2: LOGIKA PEMILIHAN FILE DI OVERLAY AWAL
    // ==========================================================
    if(initialFileInput) {
        initialFileInput.addEventListener('change', function(event) {
            const newFiles = Array.from(event.target.files);
            if (newFiles.length > 0) {
                if (initialPreviewContainer.querySelector('.no-images-selected')) { initialPreviewContainer.innerHTML = ''; }
                newFiles.forEach(file => {
                    if (!selectedFiles.some(f => f.name === file.name && f.size === file.size)) {
                        selectedFiles.push(file);
                        const previewWrapper = document.createElement('div'); previewWrapper.classList.add('preview-image-wrapper');
                        const img = document.createElement('img'); img.src = URL.createObjectURL(file); img.onload = () => URL.revokeObjectURL(img.src);
                        const removeBtn = document.createElement('button'); removeBtn.innerHTML = '&times;'; removeBtn.classList.add('btn-remove-preview'); removeBtn.type = 'button'; removeBtn.associatedFile = file;
                        previewWrapper.appendChild(img); previewWrapper.appendChild(removeBtn); initialPreviewContainer.appendChild(previewWrapper);
                    }
                });
                if(btnLanjutkanUpload) btnLanjutkanUpload.classList.toggle('hidden', selectedFiles.length === 0);
            }
            event.target.value = ''; // Reset input file
        });
    }

    // ==========================================================
    // BAGIAN 3: FUNGSI-FUNGSI HANDLER
    // ==========================================================

    function handleOpenInitialUpload(button) {
        if(!initialWebinarIdInput || !initialPreviewContainer || !initialFileInput || !btnLanjutkanUpload || !initialUploadOverlay) return;
        initialWebinarIdInput.value = button.dataset.id;
        selectedFiles = [];
        initialPreviewContainer.innerHTML = noImagesTextDefault;
        initialFileInput.value = '';
        btnLanjutkanUpload.classList.add('hidden');
        initialUploadOverlay.classList.remove('hidden');
    }

    function handleRemovePreview(removeButton) {
        const fileToRemove = removeButton.associatedFile;
        selectedFiles = selectedFiles.filter(f => f !== fileToRemove);
        removeButton.parentElement.remove();
        if (selectedFiles.length === 0) {
            initialPreviewContainer.innerHTML = noImagesTextDefault;
            if(btnLanjutkanUpload) btnLanjutkanUpload.classList.add('hidden');
        }
    }

    function handleFinalUploadConfirm() {
        if (selectedFiles.length === 0) { alert("Pilih setidaknya satu foto."); return; }
        if (!btnLanjutkanUpload || !initialWebinarIdInput) return;

        btnLanjutkanUpload.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengupload...';
        btnLanjutkanUpload.disabled = true;
        const formData = new FormData();
        formData.append('id_webinar', initialWebinarIdInput.value);
        selectedFiles.forEach(file => { formData.append('foto_pelaksanaan[]', file); });

        fetch('/projek-ifws/api/upload_foto_pelaksanaan.php', { method: 'POST', body: formData })
            .then(response => { if (!response.ok) { throw new Error(`HTTP error! status: ${response.status}`); } return response.json(); })
            .then(data => {
                if (data.status === 'success') { window.location.reload(); }
                else { alert('Upload gagal: ' + data.message); btnLanjutkanUpload.innerHTML = '<i class="fas fa-check"></i> Upload Sekarang'; btnLanjutkanUpload.disabled = false; }
            })
            .catch(error => { console.error('Error:', error); alert('Terjadi kesalahan.'); btnLanjutkanUpload.innerHTML = '<i class="fas fa-check"></i> Upload Sekarang'; btnLanjutkanUpload.disabled = false; });
    }

    function handleViewPhotos(button) {
        try {
            imagesForSlider = JSON.parse(button.dataset.images);
            currentWebinarIdForSlider = button.dataset.webinarId;
            if (imagesForSlider && imagesForSlider.length > 0) {
                currentImageIndex = 0;
                updateSliderView();
                if(viewSliderOverlay) viewSliderOverlay.classList.remove('hidden');
            } else { alert("Tidak ada foto pelaksanaan."); }
        } catch (e) { console.error("Gagal parsing JSON:", button.dataset.images, e); }
    }

    function handleDeletePhoto() {
        if (imagesForSlider.length === 0 || !imagesForSlider[currentImageIndex]) return;
        const currentImage = imagesForSlider[currentImageIndex];
        if (confirm(`Hapus foto ini?\n(${currentImage.path.split('/').pop()})`)) {
            const formData = new FormData(); formData.append('foto_id', currentImage.id); formData.append('file_path', currentImage.path);
            fetch('/projek-ifws/api/hapus_foto_pelaksanaan.php', { method: 'POST', body: formData })
                .then(response => { if (!response.ok) { throw new Error(`HTTP error! status: ${response.status}`); } return response.json(); })
                .then(data => {
                    if (data.status === 'success') {
                        imagesForSlider.splice(currentImageIndex, 1);
                        const eyeButton = document.querySelector(`.btn-view-photos[data-webinar-id='${currentWebinarIdForSlider}']`);
                        if(eyeButton) eyeButton.dataset.images = JSON.stringify(imagesForSlider);
                        updateSliderView();
                    } else { alert('Gagal menghapus foto: ' + data.message); }
                })
                .catch(error => { console.error('Error saat hapus:', error); alert('Gagal menghubungi server.'); });
        }
    }

    function updateSliderView() {
        if (!viewSliderOverlay || !sliderImage || !sliderPrevBtn || !sliderNextBtn) return;
        if (imagesForSlider.length === 0) {
            viewSliderOverlay.classList.add('hidden');
            const eyeButton = document.querySelector(`.btn-view-photos[data-webinar-id='${currentWebinarIdForSlider}']`);
            if (eyeButton) { eyeButton.classList.remove('has-photos'); eyeButton.dataset.images = '[]'; }
            return;
        }
        currentImageIndex = Math.max(0, Math.min(currentImageIndex, imagesForSlider.length - 1));
        const currentImage = imagesForSlider[currentImageIndex];
        if (currentImage && currentImage.path) { sliderImage.src = '/projek-ifws/' + currentImage.path; }
        else { sliderImage.src = ''; }
        const showNav = imagesForSlider.length > 1;
        sliderPrevBtn.style.display = showNav ? 'block' : 'none';
        sliderNextBtn.style.display = showNav ? 'block' : 'none';
    }
});