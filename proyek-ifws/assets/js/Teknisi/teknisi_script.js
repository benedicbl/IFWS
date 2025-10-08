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
    
    const linkOverlay = document.getElementById('link-overlay');
    const closeLinkBtn = document.getElementById('close-link-overlay-btn');
    const linkOverlayTitleSpan = document.querySelector('#link-overlay-title span');
    const linkTextarea = document.getElementById('link-textarea');
    const updateLinkBtn = document.getElementById('update-link-btn');
    
    // DIMODIFIKASI: Menambahkan selector untuk teks tombol dan checkbox header
    const pilihTahunBtnText = document.getElementById('pilihTahunBtnText');
    const headerCheckbox = document.querySelector('thead input[type="checkbox"]');


    // --- FUNGSI ---
    // (Tidak ada perubahan pada semua fungsi)
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
                row.innerHTML = `
                    <td><input type="checkbox" /></td>
                    <td>${webinar.narasumber || 'N/A'}</td>
                    <td>${formattedDate}</td>
                    <td><span class="badge ${webinar.kategori_class || 'badge-umum'}">${webinar.kategori || 'Umum'}</span></td>
                    <td>${webinar.topik_webinar}</td>
                    <td>
                        <a href="#" class="btn btn-update" data-id="${webinar.id}" data-topic="${webinar.topik_webinar}">
                            Update
                        </a>
                    </td>
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
        fetch(`/proyek-ifws/api/get_ifws_for_teknisi.php?tahun=${tahun}&semester=${semester}`)
            .then(response => response.json())
            .then(data => { webinars = data; displayWebinars(); })
            .catch(error => console.error('Gagal memuat data webinar:', error));
    };
    const openLinkOverlay = (topic, currentLink = '') => {
        linkOverlayTitleSpan.textContent = topic;
        linkTextarea.value = currentLink;
        linkOverlay.classList.remove('hidden');
    };
    const closeLinkOverlay = () => linkOverlay.classList.add('hidden');

    // --- EVENT LISTENERS ---
    pilihTahunBtn.addEventListener('click', (e) => { e.stopPropagation(); tahunDropdown.classList.toggle('show'); });

    // DITAMBAHKAN: Event listener untuk fungsionalitas "Select All"
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
            
            // DIMODIFIKASI: Mengubah teks pada <span>, bukan seluruh tombol
            pilihTahunBtnText.textContent = link.textContent;
            
            tahunDropdown.classList.remove('show');
        }
    });
    window.addEventListener('click', (e) => { if (!e.target.closest('.dropdown')) { tahunDropdown.classList.remove('show'); } });
    
    tableBody.addEventListener('click', (e) => {
        const updateBtn = e.target.closest('.btn-update');
        if (updateBtn) {
            e.preventDefault();
            const webinarId = updateBtn.dataset.id;
            const topic = updateBtn.dataset.topic;
            const webinar = webinars.find(w => w.id == webinarId);
            const currentLink = webinar ? (webinar.link_akses || webinar.link_webinar || '') : '';
            currentEditingId = webinarId;
            openLinkOverlay(topic, currentLink);
        }
    });
    
    closeLinkBtn.addEventListener('click', closeLinkOverlay);
    linkOverlay.addEventListener('click', (e) => { if (e.target === linkOverlay) { closeLinkOverlay(); } });
    
    updateLinkBtn.addEventListener('click', () => {
        const newLink = linkTextarea.value.trim();
        if (currentEditingId === null) return;

        const dataToSend = {
            id: currentEditingId,
            linkZoom: newLink
        };

        fetch('/proyek-ifws/api/update_zoom_link.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(dataToSend)
        })
        .then(response => {
            if (!response.ok) { throw new Error('Server merespons dengan error: ' + response.status); }
            return response.json();
        })
        .then(data => {
            alert(data.pesan);
            if(data.sukses) {
                closeLinkOverlay();
                fetchWebinars(selectedYearData.tahun, selectedYearData.semester);
            }
        })
        .catch(error => {
            console.error('Error updating link:', error);
            alert('Gagal memperbarui link.');
        });
    });
    
    // --- Inisialisasi Halaman ---
    fetch('/proyek-ifws/api/get_tahun_akademik.php')
        .then(response => response.json())
        .then(data => populateTahunDropdown(data))
        .catch(error => console.error('Gagal memuat daftar tahun:', error));
});