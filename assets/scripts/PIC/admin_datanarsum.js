document.addEventListener('DOMContentLoaded', function() {
    console.log("Script admin_datanarsum.js Dimuat.");

    // --- Elemen Global ---
    const tambahNarsumOverlay = document.getElementById('tambah-narsum-overlay');
    const editNarsumOverlay = document.getElementById('edit-narsum-overlay');
    const viewSliderOverlay = document.getElementById('view-slider-overlay'); // Overlay untuk slider foto

    // Elemen di dalam Slider Foto
    const sliderImage = document.getElementById('slider-image');
    const sliderPrevBtn = document.getElementById('slider-prev');
    const sliderNextBtn = document.getElementById('slider-next');
    const btnDeletePhoto = document.getElementById('btn-delete-current-photo'); // Tombol delete di slider

    // Variabel state untuk slider
    let currentImageIndex = 0;
    let imagesForSlider = []; // Array of objects {id, path}
    let currentWebinarIdForSlider = null; // Menyimpan ID webinar yang fotonya sedang dilihat

    // ==========================================================
    // BAGIAN 1: EVENT LISTENER UTAMA (DELEGASI UNTUK SEMUA KLIK)
    // ==========================================================
    document.body.addEventListener('click', function(event) {
        const target = event.target;
        const targetClosest = (selector) => target.closest(selector); // Helper

        // --- Logika Tombol Buka Overlay ---
        // Tombol Buka Form Tambah Narasumber
        if (targetClosest('#btn-tambah-narsum')) {
            handleOpenTambahNarsum();
        }
        // Tombol Buka Form Edit Narasumber
        else if (targetClosest('.btn-edit-narsum')) {
            handleEditClick(targetClosest('.btn-edit-narsum'));
        }
        // Tombol Buka Overlay Riwayat Narasumber
        else if (targetClosest('.view-history-btn')) {
            event.preventDefault(); // Mencegah link default
            handleHistoryClick(targetClosest('.view-history-btn'));
        }
        // Tombol Buka Slider Foto dari dalam Riwayat
        else if (targetClosest('.btn-view-photos')) {
            handleViewPhotos(targetClosest('.btn-view-photos'));
        }

        // --- Logika Tombol Navigasi Slider ---
        else if (targetClosest('#slider-prev')) {
            handleSliderPrev();
        }
        else if (targetClosest('#slider-next')) {
            handleSliderNext();
        }
        // Tombol Hapus Foto di Slider
        else if (targetClosest('#btn-delete-current-photo')) {
            handleDeletePhoto();
        }

        // --- Logika Tombol Tutup Overlay ---
        else if (target.classList.contains('overlay')) {
            target.classList.add('hidden'); // Tutup jika klik background
        }
        else if (targetClosest('.btn-close-overlay, .close-history-overlay')) {
            targetClosest('.overlay').classList.add('hidden'); // Tutup jika klik tombol close (X atau Back)
        }
    });

    // ==========================================================
    // BAGIAN 2: FUNGSI-FUNGSI HANDLER
    // ==========================================================

    function handleOpenTambahNarsum() {
        if (tambahNarsumOverlay) {
            const form = tambahNarsumOverlay.querySelector('form');
            if (form) form.reset(); // Reset form tambah
            tambahNarsumOverlay.classList.remove('hidden');
        }
    }

    function handleEditClick(button) {
        if (!editNarsumOverlay) return;
        const id = button.dataset.id;
        const nama = button.dataset.nama;
        const email = button.dataset.email;

        // Isi form edit
        const form = editNarsumOverlay.querySelector('form');
        if (form) {
            form.querySelector('#edit_narasumber_id').value = id;
            form.querySelector('#edit_nama_narsum').value = nama;
            form.querySelector('#edit_email_narsum').value = email;
        }

        // Sembunyikan pesan error lama (jika ada)
        const errorDiv = editNarsumOverlay.querySelector('#edit-form-error');
        if(errorDiv) errorDiv.classList.add('hidden');

        editNarsumOverlay.classList.remove('hidden');
    }

    function handleHistoryClick(button) {
        const targetOverlayId = button.getAttribute('data-target-overlay');
        const overlay = document.getElementById(targetOverlayId);
        if (overlay) {
            overlay.classList.remove('hidden');
        }
    }

    function handleViewPhotos(button) {
        try {
            // Data gambar (array of object {id, path}) diambil dari atribut data-images
            imagesForSlider = JSON.parse(button.dataset.images);
            // Simpan ID webinar (jika diperlukan untuk delete)
            currentWebinarIdForSlider = button.closest('tr').querySelector('.btn-trigger-upload')?.dataset.id; // Contoh cara ambil ID webinar

            if (imagesForSlider && imagesForSlider.length > 0) {
                currentImageIndex = 0; // Mulai dari gambar pertama
                updateSliderView(); // Tampilkan gambar pertama
                if(viewSliderOverlay) viewSliderOverlay.classList.remove('hidden'); // Tampilkan overlay slider
            } else {
                 console.log("Tidak ada gambar untuk ditampilkan.");
                 // Opsional: Beri tahu pengguna jika tidak ada gambar
                 // alert("Tidak ada foto pelaksanaan untuk riwayat ini.");
            }
        } catch (e) {
            console.error("Gagal parsing JSON data gambar:", button.dataset.images, e);
            alert("Gagal memuat data gambar.");
        }
    }

    function handleSliderPrev() {
        if (imagesForSlider.length > 0) {
            currentImageIndex = (currentImageIndex - 1 + imagesForSlider.length) % imagesForSlider.length;
            updateSliderView();
        }
    }

    function handleSliderNext() {
        if (imagesForSlider.length > 0) {
            currentImageIndex = (currentImageIndex + 1) % imagesForSlider.length;
            updateSliderView();
        }
    }

    function handleDeletePhoto() {
        if (imagesForSlider.length === 0 || !imagesForSlider[currentImageIndex]) return;

        const currentImage = imagesForSlider[currentImageIndex];
        const fotoId = currentImage.id;
        const filePath = currentImage.path;
        const fileName = filePath.split('/').pop();

        if (confirm(`Anda yakin ingin menghapus foto ini?\n(${fileName})`)) {
            console.log(`Menghapus foto ID: ${fotoId}, Path: ${filePath}`);
            const formData = new FormData();
            formData.append('foto_id', fotoId);
            formData.append('file_path', filePath);

            fetch('/projek-ifws/api/hapus_foto_pelaksanaan.php', { // Pastikan path API benar
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) { throw new Error(`HTTP error! status: ${response.status}`); }
                return response.json();
            })
            .then(data => {
                console.log("Respon dari server hapus:", data);
                if (data.status === 'success') {
                    // Hapus gambar dari array JS
                    imagesForSlider.splice(currentImageIndex, 1);

                    // Update dataset di tombol mata yang asli di tabel DARI MANA SLIDER DIBUKA
                    // Ini bagian yang agak tricky, kita perlu cara untuk menemukan tombol mata yang benar
                    // Kita bisa menggunakan currentWebinarIdForSlider jika sudah disimpan
                     const eyeButton = document.querySelector(`.btn-view-photos[data-target-overlay*='${currentWebinarIdForSlider}']`); // Contoh selector, mungkin perlu disesuaikan
                     if(eyeButton) eyeButton.dataset.images = JSON.stringify(imagesForSlider);


                    // Pindah ke gambar sebelumnya (atau tutup jika habis)
                    if(currentImageIndex >= imagesForSlider.length) {
                        currentImageIndex = Math.max(0, imagesForSlider.length - 1);
                    }
                    updateSliderView(); // Update tampilan slider (akan otomatis menutup jika gambar habis)
                } else {
                    alert('Gagal menghapus foto: ' + (data.message || 'Error tidak diketahui'));
                }
            })
            .catch(error => {
                console.error('Error saat hapus:', error);
                alert('Terjadi kesalahan saat menghubungi server: ' + error.message);
            });
        }
    }

    // Fungsi bantu untuk update tampilan slider
    function updateSliderView() {
        if (!viewSliderOverlay || !sliderImage || !sliderPrevBtn || !sliderNextBtn) {
             console.error("Elemen slider tidak lengkap.");
             return;
        }
        if (imagesForSlider.length === 0) {
            viewSliderOverlay.classList.add('hidden'); // Tutup overlay jika tidak ada gambar
             console.log("Tidak ada gambar tersisa, overlay slider ditutup.");
             // Update icon mata di tabel (jika perlu)
             // ... (logika update icon mata bisa ditambahkan di sini) ...
            return;
        }
        // Pastikan index valid
        currentImageIndex = Math.max(0, Math.min(currentImageIndex, imagesForSlider.length - 1));
        const currentImage = imagesForSlider[currentImageIndex];
        if (currentImage && currentImage.path) {
             sliderImage.src = '/projek-ifws/' + currentImage.path;
             console.log("Slider menampilkan gambar:", sliderImage.src);
        } else {
             console.error("Data gambar tidak valid pada index:", currentImageIndex, imagesForSlider);
             sliderImage.src = ''; // Kosongkan jika error
        }
        const showNav = imagesForSlider.length > 1;
        sliderPrevBtn.style.display = showNav ? 'block' : 'none';
        sliderNextBtn.style.display = showNav ? 'block' : 'none';
    }

});