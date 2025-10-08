document.addEventListener('DOMContentLoaded', () => {
    let currentEditingId = null;
    let selectedYearData = null;
    let webinars = [];

    // --- DOM Elements ---
    const pilihTahunBtn = document.getElementById('pilihTahunBtn');
    const tahunDropdown = document.getElementById('tahunDropdown');
    const tableBody = document.getElementById('webinar-table-body');
    const initialMessage = document.getElementById('initial-message');
    const dataSection = document.getElementById('data-section');
    
    const uploadModal = document.getElementById('poster-modal');
    const modalTitle = document.getElementById('modal-webinar-title');
    const modalFileInput = document.getElementById('poster-file-input');
    const cancelModalBtn = document.getElementById('cancel-modal-btn');
    const savePosterBtn = document.getElementById('save-poster-btn');

    const posterOverlay = document.getElementById("poster-overlay");
    const closeOverlayBtn = document.getElementById("close-overlay-btn");
    const posterPreviewImg = document.getElementById("poster-preview-img");

    const headerCheckbox = document.querySelector('thead input[type="checkbox"]');


    // --- FUNGSI ---
    
    function openOverlay(imageSrc) {
        if (!posterOverlay || !posterPreviewImg) {
            console.error("Elemen overlay poster tidak ditemukan di HTML!");
            return;
        }
        posterPreviewImg.src = imageSrc;
        posterOverlay.classList.remove("hidden");
    }

    function closeOverlay() {
        if (!posterOverlay) return;
        posterOverlay.classList.add("hidden");
    }

    const populateTahunDropdown = (tahunList) => {
        tahunDropdown.innerHTML = '';
        if (tahunList && tahunList.length > 0) {
            tahunList.forEach(item => {
                const link = document.createElement('a');
                link.href = '#';
                link.dataset.tahun = `${item.tahun_akd}-${item.semester_akd}`;
                link.textContent = `${item.tahun_akd} - ${item.semester_akd}`;
                tahunDropdown.appendChild(link);
            });
        } else {
            tahunDropdown.innerHTML = '<a>Tidak ada data.</a>';
        }
    };

    const displayWebinars = () => {
        tableBody.innerHTML = '';
        if (webinars && webinars.length > 0) {
            webinars.forEach(webinar => {
                const row = document.createElement('tr');
                row.setAttribute('data-id', webinar.id);
                const formattedDate = new Date(webinar.tanggal).toLocaleDateString('id-ID', { day: 'numeric', month: 'numeric', year: 'numeric' });
                
                let posterCellHTML = `<div class="action-buttons"><a href="#" class="btn btn-upload"><i class="fas fa-upload"></i> Upload</a>`;
                if (webinar.poster) {
                    const imgSrc = `data:image/jpeg;base64,${webinar.poster}`;
                    posterCellHTML += `<a href="#" class="btn btn-icon-only view-poster-btn" data-poster-src="${imgSrc}" title="Lihat Poster"><i class="fas fa-eye"></i></a>`;
                }
                posterCellHTML += `</div>`;
                
                row.innerHTML = `
                    <td><input type="checkbox" /></td>
                    <td>${webinar.narasumber || 'N/A'}</td>
                    <td>${formattedDate}</td>
                    <td><span class="badge ${webinar.kategori_class || 'badge-umum'}">${webinar.kategori || 'Umum'}</span></td>
                    <td>${webinar.topik_webinar}</td>
                    <td>${posterCellHTML}</td>
                `;
                tableBody.appendChild(row);
            });
        } else {
            tableBody.innerHTML = `<tr><td colspan="6" style="text-align:center;">Tidak ada data webinar.</td></tr>`;
        }
        dataSection.classList.remove('hidden');
        initialMessage.classList.add('hidden');
    };

    const fetchWebinars = (tahun, semester) => {
        tableBody.innerHTML = `<tr><td colspan="6" style="text-align:center;">Memuat data...</td></tr>`;
        fetch(`/proyek-ifws/api/get_ifws_for_promosi.php?tahun=${tahun}&semester=${semester}`)
            .then(response => response.json())
            .then(data => { 
                webinars = data; 
                displayWebinars(); 
            })
            .catch(error => console.error('Gagal memuat data webinar:', error));
    };

    const openUploadModal = (webinar) => {
        currentEditingId = webinar.id;
        modalTitle.textContent = webinar.topik_webinar;
        modalFileInput.value = '';
        uploadModal.classList.remove('hidden');
    };

    const closeUploadModal = () => {
        uploadModal.classList.add('hidden');
    };


    // --- EVENT LISTENERS ---

    pilihTahunBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        tahunDropdown.classList.toggle('show');
    });

    if (headerCheckbox) {
        headerCheckbox.addEventListener('change', () => {
            const isChecked = headerCheckbox.checked;
            const rowCheckboxes = tableBody.querySelectorAll('input[type="checkbox"]');
            
            rowCheckboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
            });
        });
    }

    tahunDropdown.addEventListener('click', (e) => {
        e.preventDefault();
        const link = e.target.closest('a');
        if (link && link.dataset.tahun) {
            const tahunData = link.getAttribute('data-tahun');
            const [tahun_akd, semester] = tahunData.split('-');
            selectedYearData = { tahun: tahun_akd, semester: semester };
            fetchWebinars(tahun_akd, semester);
            pilihTahunBtn.innerHTML = `${link.textContent} <i class="fa-solid fa-chevron-down"></i>`;
            tahunDropdown.classList.remove('show');
        }
    });

    window.addEventListener('click', (e) => {
        if (!e.target.closest('.dropdown')) {
            tahunDropdown.classList.remove('show');
        }
    });

    tableBody.addEventListener('click', (e) => {
        const uploadBtn = e.target.closest('.btn-upload');
        if (uploadBtn) {
            e.preventDefault();
            const row = uploadBtn.closest('tr');
            const webinarId = parseInt(row.dataset.id);
            const webinarToEdit = webinars.find(w => w.id === webinarId);
            if (webinarToEdit) openUploadModal(webinarToEdit);
        }
        
        const viewBtn = e.target.closest('.view-poster-btn');
        if (viewBtn) {
            e.preventDefault();
            const posterSrc = viewBtn.getAttribute("data-poster-src");
            openOverlay(posterSrc);
        }
    });

    cancelModalBtn.addEventListener('click', closeUploadModal);

    savePosterBtn.addEventListener('click', () => {
        const file = modalFileInput.files[0];
        if (!file) { 
            alert('Harap pilih file poster terlebih dahulu!'); 
            return; 
        }
        if (currentEditingId === null) return;
        
        const formData = new FormData();
        formData.append('id_webinar', currentEditingId);
        formData.append('posterFile', file);
        
        savePosterBtn.textContent = 'Mengupload...';
        savePosterBtn.disabled = true;
        
        fetch('/proyek-ifws/api/upload_poster.php', { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                alert(data.pesan);
                if (data.sukses) {
                    closeUploadModal();
                    fetchWebinars(selectedYearData.tahun, selectedYearData.semester);
                }
            })
            .catch(error => { 
                console.error('Error saat upload:', error); 
                alert('Terjadi kesalahan saat mengupload file.'); 
            })
            .finally(() => { 
                savePosterBtn.textContent = 'Simpan'; 
                savePosterBtn.disabled = false; 
            });
    });
    
    if (closeOverlayBtn) { 
        closeOverlayBtn.addEventListener("click", closeOverlay); 
    }
    
    if (posterOverlay) { 
        posterOverlay.addEventListener("click", function (event) { 
            if (event.target === posterOverlay) { 
                closeOverlay(); 
            } 
        }); 
    }
    
    // --- Inisialisasi Halaman ---
    fetch('/proyek-ifws/api/get_tahun_akademik.php')
        .then(response => response.json())
        .then(data => populateTahunDropdown(data))
        .catch(error => console.error('Gagal memuat daftar tahun:', error));
});