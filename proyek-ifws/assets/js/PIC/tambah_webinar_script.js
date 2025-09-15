document.addEventListener('DOMContentLoaded', () => {
    // --- Variabel Global ---
    let allAvailablePeople = [];
    let selectedNarasumber = [];
    let selectedPanitia = [];
    let currentWebinarId = null;
    let currentSelectionType = '';

    // --- DOM Elements ---
    const formWebinarWrapper = document.getElementById('form-webinar-wrapper');
    const pilihPesertaWrapper = document.getElementById('pilih-peserta-wrapper');
    const webinarForm = document.getElementById('webinar-form');
    const jenisIfwsSelect = document.getElementById('jenis-ifws');
    const topikTextarea = document.getElementById('topik');
    const judulWebinarTerpilih = document.getElementById('judul-webinar-terpilih');
    const narasumberListContainer = document.getElementById('narasumber-list');
    const panitiaListBody = document.getElementById('panitia-list-body');
    const savePesertaBtn = document.getElementById('save-peserta-btn');
    const modal = document.getElementById('selection-modal');
    const modalTitle = document.getElementById('modal-title');
    const modalListContainer = document.getElementById('modal-list');
    const modalSearchInput = document.getElementById('modal-search');
    const roleInputContainer = document.getElementById('role-input-container');
    const panitiaRoleInput = document.getElementById('panitia-role');
    const addNarasumberBtn = document.getElementById('add-narasumber-btn');
    const addPanitiaBtn = document.getElementById('add-panitia-btn');
    const closeModalBtn = document.getElementById('close-modal-btn');

    // ===================================================================
    // BAGIAN 1: LOGIKA FORM WEBINAR UTAMA
    // ===================================================================

    function populateJenisIfws() {
        if (!jenisIfwsSelect) return;
        fetch('../api/get_jenis_ifws.php')
            .then(res => res.ok ? res.json() : Promise.reject(res))
            .then(data => {
                jenisIfwsSelect.innerHTML = '<option value="" disabled selected>Pilih jenis...</option>';
                data.forEach(j => {
                    jenisIfwsSelect.innerHTML += `<option value="${j.id}">${j.nama_jenis}</option>`;
                });
            })
            .catch(error => console.error('Gagal memuat jenis IFWS:', error));
    }

    if(webinarForm) {
        webinarForm.addEventListener('submit', e => {
            e.preventDefault();
            const formData = new FormData(webinarForm);
            fetch('../api/tambah_webinar_proses.php', { method: 'POST', body: formData })
                .then(res => res.ok ? res.json() : Promise.reject(res))
                .then(data => {
                    if (data.status === 'success') {
                        alert(data.message);
                        currentWebinarId = data.new_webinar_id;
                        judulWebinarTerpilih.textContent = `Topik: ${topikTextarea.value}`;
                        formWebinarWrapper.classList.add('hidden');
                        pilihPesertaWrapper.classList.remove('hidden');
                        fetchAvailablePeople();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    }

    populateJenisIfws();

    // ===================================================================
    // BAGIAN 2: LOGIKA LENGKAP UNTUK PILIH PESERTA
    // ===================================================================

    function renderSelections() {
        narasumberListContainer.innerHTML = '';
        selectedNarasumber.forEach(p => {
            narasumberListContainer.innerHTML += `<div class="list-item"><span>${p.nama}</span><button class="btn btn-delete" data-id="${p.id}" data-type="narasumber">Delete</button></div>`;
        });
        panitiaListBody.innerHTML = '';
        selectedPanitia.forEach(p => {
            panitiaListBody.innerHTML += `<tr><td>${p.nama}</td><td>${p.peran}</td><td><button class="btn btn-delete" data-id="${p.id}" data-type="panitia">Delete</button></td></tr>`;
        });
    }

    function fetchAvailablePeople() {
        fetch('../api/get_narasumber.php')
            .then(res => res.ok ? res.json() : Promise.reject(res))
            .then(data => { allAvailablePeople = data; })
            .catch(error => console.error('Gagal memuat daftar orang:', error));
    }

    function openModal(type) {
        currentSelectionType = type;
        modalTitle.textContent = `Pilih ${type}`;
        roleInputContainer.classList.toggle('hidden', type !== 'Panitia');
        populateModalList(allAvailablePeople);
        modal.classList.remove('hidden');
        modalSearchInput.value = '';
        modalSearchInput.focus();
    }

    function populateModalList(people) {
        modalListContainer.innerHTML = '';
        const selectedIds = [...selectedNarasumber, ...selectedPanitia].map(p => p.id);
        const available = people.filter(p => !selectedIds.includes(p.id));
        if (available.length === 0) {
            modalListContainer.innerHTML = '<div class="modal-list-item-empty">Semua orang sudah ditambahkan.</div>';
            return;
        }
        available.forEach(p => {
            modalListContainer.innerHTML += `<div class="modal-list-item" data-id="${p.id}">${p.nama}</div>`;
        });
    }

    if (addNarasumberBtn) addNarasumberBtn.addEventListener('click', () => openModal('Narasumber'));
    if (addPanitiaBtn) addPanitiaBtn.addEventListener('click', () => openModal('Panitia'));
    if (closeModalBtn) closeModalBtn.addEventListener('click', () => modal.classList.add('hidden'));
    if (modal) modal.addEventListener('click', (e) => (e.target === modal) && modal.classList.add('hidden'));

    if (modalSearchInput) {
        modalSearchInput.addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            const filteredPeople = allAvailablePeople.filter(p => p.nama.toLowerCase().includes(searchTerm));
            populateModalList(filteredPeople);
        });
    }

    if (modalListContainer) {
        modalListContainer.addEventListener('click', e => {
            if (e.target.classList.contains('modal-list-item')) {
                const personId = parseInt(e.target.dataset.id);
                const person = allAvailablePeople.find(p => p.id == personId);

                if (currentSelectionType === 'Narasumber') {
                    selectedNarasumber.push({ ...person, peran: 'Narasumber' });
                } else {
                    const peran = panitiaRoleInput.value.trim();
                    if (!peran) { alert('Harap isi peran panitia.'); return; }
                    selectedPanitia.push({ ...person, peran: peran });
                    panitiaRoleInput.value = '';
                }
                renderSelections();
                modal.classList.add('hidden');
            }
        });
    }

    if(pilihPesertaWrapper) {
        pilihPesertaWrapper.addEventListener('click', e => {
            if (e.target.classList.contains('btn-delete')) {
                const id = parseInt(e.target.dataset.id);
                if (e.target.dataset.type === 'narasumber') {
                    selectedNarasumber = selectedNarasumber.filter(p => p.id !== id);
                } else {
                    selectedPanitia = selectedPanitia.filter(p => p.id !== id);
                }
                renderSelections();
            }
        });
    }
    
    if (savePesertaBtn) {
        savePesertaBtn.addEventListener('click', () => {
            const payload = {
                id_webinar: currentWebinarId,
                peserta: [...selectedNarasumber, ...selectedPanitia]
            };
            if (payload.peserta.length === 0) {
                alert('Harap tambahkan setidaknya satu narasumber atau panitia.');
                return;
            }

            fetch('../api/simpan_peserta_proses.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
            .then(res => res.ok ? res.json() : Promise.reject(res))
            .then(data => {
                alert(data.message);
                if (data.status === 'success') {
                    window.location.href = 'list_IFWS.php';
                }
            })
            .catch(error => console.error('Error:', error));
        });
    }
});