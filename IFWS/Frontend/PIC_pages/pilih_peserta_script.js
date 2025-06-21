document.addEventListener('DOMContentLoaded', () => {
    // --- DATABASE SEMU ---
    const allAvailablePeople = [
        { id: 1, nama: 'Nathaniel Cooper' }, { id: 2, nama: 'Victoria Bennett' },
        { id: 3, nama: 'Samuel Turner' }, { id: 4, nama: 'Ryan Reynolds' },
        { id: 5, nama: 'Elijah Murphy' }, { id: 6, nama: 'Hugh Jackman' },
    ];
    const webinarData = {
        '101': { id: 101, narasumberTerpilih: [{id: 1, nama: 'Nathaniel Cooper'}], panitiaTerpilih: [{id: 2, nama: 'Victoria Bennett', peran: 'Moderator'}] },
        '102': { id: 102, narasumberTerpilih: [{id: 4, nama: 'Ryan Reynolds'}], panitiaTerpilih: [] },
        '103': { id: 103, narasumberTerpilih: [], panitiaTerpilih: [] }
    };

    // --- DATA TERPILIH (diisi sesuai mode) ---
    let selectedNarasumber = [];
    let selectedPanitia = [];

    // --- DOM Elements ---
    const narasumberListContainer = document.getElementById('narasumber-list');
    const panitiaListBody = document.getElementById('panitia-list-body');
    const modal = document.getElementById('selection-modal');
    const modalTitle = document.getElementById('modal-title');
    const modalListContainer = document.getElementById('modal-list');
    const modalSearchInput = document.getElementById('modal-search');
    const roleInputContainer = document.getElementById('role-input-container');
    const panitiaRoleInput = document.getElementById('panitia-role');
    const kembaliLink = document.querySelector('.btn-kembali');
    
    let currentSelectionType = '';

    // --- LOGIKA UTAMA: Cek Mode Edit atau Tambah ---
    const urlParams = new URLSearchParams(window.location.search);
    const webinarId = urlParams.get('webinarId');

    if (webinarId && webinarData[webinarId]) {
        // --- MODE EDIT ---
        const webinar = webinarData[webinarId];
        // Salin data yang sudah ada ke dalam array terpilih
        selectedNarasumber = [...webinar.narasumberTerpilih];
        selectedPanitia = [...webinar.panitiaTerpilih];
        // Atur link kembali ke halaman edit webinar yang benar
        kembaliLink.href = `edit_webinar.html?id=${webinarId}`;
    } else {
        // --- MODE TAMBAH ---
        // Biarkan array terpilih kosong dan link kembali ke halaman tambah
        kembaliLink.href = 'tambah_webinar.html';
    }

    // --- Render data awal (penting untuk mode edit) ---
    renderSelectedNarasumber();
    renderSelectedPanitia();

    // --- Semua fungsi dan event listener lainnya tetap sama ---
    function renderSelectedNarasumber() {
        narasumberListContainer.innerHTML = '';
        selectedNarasumber.forEach((person, index) => {
            const li = document.createElement('div');
            li.className = 'list-item';
            li.innerHTML = `<span>${person.nama}</span><button class="btn btn-delete" data-type="narasumber" data-index="${index}">Delete</button>`;
            narasumberListContainer.appendChild(li);
        });
    }

    function renderSelectedPanitia() {
        panitiaListBody.innerHTML = '';
        selectedPanitia.forEach((p, index) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `<td>${p.nama}</td><td>${p.peran}</td><td><button class="btn btn-delete" data-type="panitia" data-index="${index}">Delete</button></td>`;
            panitiaListBody.appendChild(tr);
        });
    }

    function openModal(type) {
        currentSelectionType = type;
        modalTitle.textContent = `Pilih ${type}`;
        roleInputContainer.classList.toggle('hidden', type !== 'Panitia');
        populateModalList(allAvailablePeople);
        modal.classList.remove('hidden');
        modalSearchInput.focus();
    }

    function closeModal() {
        modal.classList.add('hidden');
        modalSearchInput.value = '';
        panitiaRoleInput.value = '';
    }

    function populateModalList(people) {
        modalListContainer.innerHTML = '';
        people.forEach(person => {
            const div = document.createElement('div');
            div.className = 'modal-list-item';
            div.textContent = person.nama;
            div.dataset.id = person.id;
            modalListContainer.appendChild(div);
        });
    }

    document.getElementById('add-narasumber-btn').addEventListener('click', () => openModal('Narasumber'));
    document.getElementById('add-panitia-btn').addEventListener('click', () => openModal('Panitia'));
    document.getElementById('close-modal-btn').addEventListener('click', closeModal);
    modal.addEventListener('click', (e) => e.target === modal && closeModal());

    modalSearchInput.addEventListener('input', (e) => {
        const searchTerm = e.target.value.toLowerCase();
        const filteredPeople = allAvailablePeople.filter(p => p.nama.toLowerCase().includes(searchTerm));
        populateModalList(filteredPeople);
    });

    modalListContainer.addEventListener('click', (e) => {
        if (e.target.classList.contains('modal-list-item')) {
            const personId = parseInt(e.target.dataset.id);
            const person = allAvailablePeople.find(p => p.id === personId);
            if (currentSelectionType === 'Narasumber') {
                if (!selectedNarasumber.some(p => p.id === personId)) {
                    selectedNarasumber.push(person);
                    renderSelectedNarasumber();
                } else { alert(`${person.nama} sudah ada.`); }
            } else if (currentSelectionType === 'Panitia') {
                const peran = panitiaRoleInput.value.trim();
                if (!peran) { alert('Harap isi peran panitia.'); return; }
                selectedPanitia.push({ ...person, peran: peran });
                renderSelectedPanitia();
            }
            closeModal();
        }
    });

    document.getElementById('save-btn').addEventListener('click', () => {
        alert('Data narasumber dan panitia berhasil disimpan!');
        window.location.href = kembaliLink.href;
    });

    // Event Delegation untuk tombol delete
    document.querySelector('.main-content').addEventListener('click', e => {
        if(e.target.classList.contains('btn-delete')) {
            const type = e.target.dataset.type;
            const index = e.target.dataset.index;
            if(type === 'narasumber') {
                selectedNarasumber.splice(index, 1);
                renderSelectedNarasumber();
            } else if(type === 'panitia') {
                selectedPanitia.splice(index, 1);
                renderSelectedPanitia();
            }
        }
    });
});