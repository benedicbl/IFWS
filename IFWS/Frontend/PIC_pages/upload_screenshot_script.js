document.addEventListener('DOMContentLoaded', () => {
    // --- SIMULASI DATABASE ---
    // Di aplikasi nyata, data ini akan diambil dari server/API.
    const webinarData = {
        '101': { id: 101, narasumber: 'John Doe', tanggal: '10/08/2025', jenis: 'Internal - Jurusan', topik: 'Introduction To 3D Computer Vision', screenshots: [
            'https://via.placeholder.com/300x200.png/2c3e50/ecf0f1?text=Screenshot+1',
            'https://via.placeholder.com/300x200.png/2c3e50/ecf0f1?text=Screenshot+2',
            'https://via.placeholder.com/300x200.png/2c3e50/ecf0f1?text=Screenshot+3'
        ]},
        '102': { id: 102, narasumber: 'Joss Everman', tanggal: '12/08/2025', jenis: 'Internasional', topik: 'Automation in Tech Industry', screenshots: [] },
        '201': { id: 201, narasumber: 'Elon Musk', tanggal: '15/04/2024', jenis: 'Internasional', topik: 'Masa Depan Antariksa', screenshots: [] },
    };

    // --- DOM Elements ---
    const topicEl = document.getElementById('webinar-topic');
    const speakerEl = document.getElementById('webinar-speaker');
    const typeEl = document.getElementById('webinar-type');
    const dateEl = document.getElementById('webinar-date');
    const uploadButton = document.getElementById('upload-button');
    const fileInput = document.getElementById('file-input');
    const previewsContainer = document.getElementById('previews-container');
    const saveButton = document.getElementById('save-button');

    // --- Ambil ID dari URL dan Muat Data Webinar ---
    const urlParams = new URLSearchParams(window.location.search);
    const webinarId = urlParams.get('id');

    if (webinarId && webinarData[webinarId]) {
        const webinar = webinarData[webinarId];
        topicEl.textContent = webinar.topik;
        speakerEl.textContent = webinar.narasumber;
        typeEl.textContent = webinar.jenis;
        dateEl.textContent = webinar.tanggal;
        // Tampilkan screenshot yang sudah ada
        renderExistingScreenshots(webinar.screenshots);
    } else {
        topicEl.textContent = 'Webinar Tidak Ditemukan';
        document.querySelector('.screenshot-area').style.display = 'none';
    }

    // --- Fungsi untuk menampilkan screenshot ---
    function renderExistingScreenshots(screenshots) {
        screenshots.forEach(url => createPreviewElement(url));
    }
    
    function createPreviewElement(imageSrc) {
        const previewItem = document.createElement('div');
        previewItem.className = 'preview-item';
        previewItem.innerHTML = `
            <img src="${imageSrc}" alt="Preview">
            <button class="delete-preview-btn">&times;</button>
        `;
        previewsContainer.appendChild(previewItem);
    }

    // --- Logika Tombol Upload ---
    uploadButton.addEventListener('click', () => {
        fileInput.click(); // Memicu input file yang tersembunyi
    });

    fileInput.addEventListener('change', (event) => {
        const files = event.target.files;
        for (const file of files) {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = () => {
                    createPreviewElement(reader.result);
                };
                reader.readAsDataURL(file);
            }
        }
        // Reset input file agar bisa memilih file yang sama lagi
        fileInput.value = '';
    });

    // --- Logika Tombol Hapus (Event Delegation) ---
    previewsContainer.addEventListener('click', (event) => {
        if (event.target.classList.contains('delete-preview-btn')) {
            event.target.closest('.preview-item').remove();
        }
    });
    
    // --- Logika Tombol Simpan ---
    saveButton.addEventListener('click', () => {
        // Di aplikasi nyata, Anda akan mengirim daftar URL gambar ke server
        alert('Screenshot berhasil disimpan!');
        window.location.href = 'riwayat.html';
    });
});