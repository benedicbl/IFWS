document.addEventListener('DOMContentLoaded', () => {
    // DOM Elements
    const topicEl = document.getElementById('webinar-topic');
    const speakerEl = document.getElementById('webinar-speaker');
    const uploadButton = document.getElementById('upload-button');
    const fileInput = document.getElementById('file-input');
    const previewsContainer = document.getElementById('previews-container');
    const uploadForm = document.getElementById('upload-form');

    const urlParams = new URLSearchParams(window.location.search);
    // Kita ganti nama parameter agar lebih konsisten
    const webinarId = urlParams.get('id');
    
    if (!webinarId) {
        alert('ID Webinar tidak valid!');
        window.location.href = 'riwayat.php';
        return;
    }

    // ---- FUNGSI BARU UNTUK MENAMPILKAN SCREENSHOT YANG SUDAH ADA ----
    function fetchAndDisplayExistingScreenshots(id) {
        fetch(`../api/get_screenshots.php?id_webinar=${id}`)
            .then(response => response.json())
            .then(screenshots => {
                screenshots.forEach(ss => {
                    // Path ke gambar yang sudah diupload
                    const imageUrl = `../assets/uploads/screenshots/${ss.nama_file}`;
                    createPreviewElement(imageUrl, ss.nama_file, ss.id, true); // true menandakan ini gambar lama
                });
            })
            .catch(error => console.error('Gagal memuat screenshot lama:', error));
    }

    // ---- Fungsi untuk membuat elemen preview (dimodifikasi) ----
    function createPreviewElement(imageSrc, fileName, fileId = null, isExisting = false) {
        const previewItem = document.createElement('div');
        previewItem.className = 'preview-item';
        // Tambahkan data-id jika ini adalah file yang sudah ada di DB
        if (isExisting) {
            previewItem.setAttribute('data-db-id', fileId);
        }
        
        previewItem.innerHTML = `
            <img src="${imageSrc}" alt="${fileName}">
            <button type="button" class="delete-preview-btn" data-name="${fileName}">&times;</button>
        `;
        previewsContainer.appendChild(previewItem);
    }

    if (topicEl) {
        topicEl.textContent = `Mengupload untuk Webinar ID: ${webinarId}`;
    }

    // --- Logika Tombol "Pilih Gambar" ---
    if (uploadButton) {
        uploadButton.addEventListener('click', () => {
            fileInput.click(); 
        });
    }

    let selectedFiles = []; 
    
    if (fileInput) {
        fileInput.addEventListener('change', () => {
            // Kita tidak mengosongkan preview lama, tapi menambahkan yang baru
            // previewsContainer.innerHTML = ''; 
            const newFiles = Array.from(fileInput.files);
            selectedFiles.push(...newFiles); // Gabungkan file lama dan baru

            newFiles.forEach(file => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = () => {
                        createPreviewElement(reader.result, file.name, null, false); // false menandakan ini file baru
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    }
    
    // Logika untuk hapus preview
    previewsContainer.addEventListener('click', (event) => {
        if (event.target.classList.contains('delete-preview-btn')) {
            const previewItem = event.target.closest('.preview-item');
            const fileName = event.target.getAttribute('data-name');
            const dbId = previewItem.getAttribute('data-db-id');

            if (dbId) {
                // Jika ini gambar lama dari DB, kita bisa tambahkan logika hapus dari DB di sini nanti
                alert(`Fitur hapus gambar (ID: ${dbId}) dari database akan dibuat selanjutnya.`);
                // Untuk sekarang, kita hanya sembunyikan
                 previewItem.remove();
            } else {
                // Jika ini file baru, hapus dari daftar upload
                selectedFiles = selectedFiles.filter(file => file.name !== fileName);
                previewItem.remove();
            }
        }
    });

    // --- Logika untuk Submit Form ---
    if (uploadForm) {
        uploadForm.addEventListener('submit', (e) => {
            e.preventDefault();

            if (selectedFiles.length === 0) {
                alert('Silakan pilih setidaknya satu gambar baru untuk di-upload.');
                return;
            }

            const formData = new FormData();
            formData.append('id_webinar', webinarId);
            selectedFiles.forEach(file => {
                formData.append('screenshots[]', file);
            });

            fetch('../api/upload_screenshot_proses.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.status === 'success') {
                    window.location.reload(); // Reload halaman agar menampilkan semua gambar
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat meng-upload file.');
            });
        });
    }

    // PANGGIL FUNGSI BARU SAAT HALAMAN DIMUAT
    fetchAndDisplayExistingScreenshots(webinarId);
});