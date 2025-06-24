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
    const webinarIdInput = document.getElementById('webinarId');
    const tanggalInput = document.getElementById('tanggal');
    const jenisIfwsSelect = document.getElementById('jenis-ifws');
    const jamMulaiInput = document.getElementById('jam-mulai');
    const jamSelesaiInput = document.getElementById('jam-selesai');
    const topikTextarea = document.getElementById('topik');
    const editPesertaBtn = document.getElementById('edit-peserta-btn');
    const judulWebinarTerpilih = document.getElementById('judul-webinar-terpilih');
    const narasumberListContainer = document.getElementById('narasumber-list');
    const panitiaListBody = document.getElementById('panitia-list-body');
    const savePesertaBtn = document.getElementById('save-peserta-btn');
    const kembaliKeFormUtamaBtn = document.getElementById('kembali-ke-form-utama-btn');
    const modal = document.getElementById('selection-modal');
    const modalTitle = document.getElementById('modal-title');
    const modalListContainer = document.getElementById('modal-list');
    const modalSearchInput = document.getElementById('modal-search');
    const roleInputContainer = document.getElementById('role-input-container');
    const panitiaRoleInput = document.getElementById('panitia-role');
    const addNarasumberBtn = document.getElementById('add-narasumber-btn');
    const addPanitiaBtn = document.getElementById('add-panitia-btn');
    const closeModalBtn = document.getElementById('close-modal-btn');

    // Ambil ID dari URL
    const urlParams = new URLSearchParams(window.location.search);
    currentWebinarId = urlParams.get('id');

    if (!currentWebinarId) {
        alert('ID Webinar tidak valid!');
        window.location.href = 'list_IFWS.php';
        return;
    }

    // ===================================================================
    // BAGIAN 1: FUNGSI-FUNGSI UTAMA
    // ===================================================================

    function populateJenisIfws(selectedValue) {
        fetch('../api/get_jenis_ifws.php')
            .then(res => res.json())
            .then(data => {
                jenisIfwsSelect.innerHTML = '';
                data.forEach(j => {
                    const option = document.createElement('option');
                    option.value = j.id;
                    option.textContent = j.nama_jenis;
                    if (j.id == selectedValue) {
                        option.selected = true;
                    }
                    jenisIfwsSelect.appendChild(option);
                });
            });
    }

    function fetchWebinarDetails() {
        fetch(`../api/get_single_webinar.php?id=${currentWebinarId}`)
            .then(res => res.json())
            .then(response => {
                if (response.status === 'success') {
                    const webinar = response.data.webinar;
                    const peserta = response.data.peserta;

                    webinarIdInput.value = webinar.id;
                    tanggalInput.value = webinar.tanggal;
                    jamMulaiInput.value = webinar.jam_mulai;
                    jamSelesaiInput.value = webinar.jam_selesai;
                    topikTextarea.value = webinar.topik;
                    judulWebinarTerpilih.textContent = `Topik: ${webinar.topik}`;
                    populateJenisIfws(webinar.id_jenis_ifws);

                    selectedNarasumber = peserta.filter(p => p.peran === 'Narasumber').map(p => ({ id: parseInt(p.id_narasumber), nama: p.nama, peran: p.peran }));
                    selectedPanitia = peserta.filter(p => p.peran !== 'Narasumber').map(p => ({ id: parseInt(p.id_narasumber), nama: p.nama, peran: p.peran }));
                    renderSelections();
                } else {
                    alert(response.message);
                }
            });
    }

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
        fetch('../api/get_narasumber.php').then(res => res.json()).then(data => { allAvailablePeople = data; });
    }

    function openModal(type) {
        currentSelectionType = type;
        modalTitle.textContent = `Pilih ${type}`;
        roleInputContainer.classList.toggle('hidden', type !== 'Panitia');
        populateModalList(allAvailablePeople);
        modal.classList.remove('hidden');
    }

    function populateModalList(people) {
        modalListContainer.innerHTML = '';
        const selectedIds = [...selectedNarasumber, ...selectedPanitia].map(p => p.id);
        const available = people.filter(p => !selectedIds.includes(p.id));
        available.forEach(p => {
            modalListContainer.innerHTML += `<div class="modal-list-item" data-id="${p.id}">${p.nama}</div>`;
        });
    }

    // ===================================================================
    // BAGIAN 2: EVENT LISTENERS
    // ===================================================================
    
    webinarForm.addEventListener('submit', e => {
        e.preventDefault();
        const formData = new FormData(webinarForm);
        fetch('../api/update_webinar_proses.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                // PERUBAHAN PERTAMA DI SINI
                if (data.status === 'success') {
                    window.location.href = 'list_IFWS.php';
                }
            });
    });
    
    editPesertaBtn.addEventListener('click', () => {
        formWebinarWrapper.classList.add('hidden');
        pilihPesertaWrapper.classList.remove('hidden');
        fetchAvailablePeople();
    });

    kembaliKeFormUtamaBtn.addEventListener('click', () => {
        pilihPesertaWrapper.classList.add('hidden');
        formWebinarWrapper.classList.remove('hidden');
    });

    addNarasumberBtn.addEventListener('click', () => openModal('Narasumber'));
    addPanitiaBtn.addEventListener('click', () => openModal('Panitia'));
    closeModalBtn.addEventListener('click', () => modal.classList.add('hidden'));
    modal.addEventListener('click', (e) => (e.target === modal) && modal.classList.add('hidden'));

    modalListContainer.addEventListener('click', e => {
        if (e.target.classList.contains('modal-list-item')) {
            const personId = parseInt(e.target.dataset.id);
            const person = allAvailablePeople.find(p => p.id == personId);
            if (!person) return;

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

    savePesertaBtn.addEventListener('click', () => {
        const payload = {
            id_webinar: currentWebinarId,
            peserta: [...selectedNarasumber, ...selectedPanitia]
        };
        fetch('../api/simpan_peserta_proses.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            // PERUBAHAN KEDUA DI SINI
            if (data.status === 'success') {
                window.location.href = 'list_IFWS.php';
            }
        });
    });

    // Panggil fungsi utama untuk memuat semua data saat halaman dibuka
    fetchWebinarDetails();
});