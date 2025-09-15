document.addEventListener('DOMContentLoaded', () => {
    // --- Variabel Global ---
    let allAvailablePeople = []; // Akan diisi dari database
    let selectedNarasumber = [];
    let selectedPanitia = [];
    let currentSelectionType = '';

    // --- DOM Elements ---
    const narasumberListContainer = document.getElementById('narasumber-list');
    const panitiaListBody = document.getElementById('panitia-list-body');
    const modal = document.getElementById('selection-modal');
    const modalTitle = document.getElementById('modal-title');
    const modalListContainer = document.getElementById('modal-list');
    const modalSearchInput = document.getElementById('modal-search');
    const roleInputContainer = document.getElementById('role-input-container');
    const panitiaRoleInput = document.getElementById('panitia-role');
    const saveButton = document.getElementById('save-btn');

    // --- Ambil ID Webinar dari URL ---
    const urlParams = new URLSearchParams(window.location.search);
    const webinarId = urlParams.get('id_webinar');

    if (!webinarId) {
        alert('ID Webinar tidak valid!');
        window.location.href = 'list_IFWS.php';
        return;
    }

    // --- Fungsi Render & Tampilan ---
    function renderSelectedNarasumber() {
        narasumberListContainer.innerHTML = '';
        selectedNarasumber.forEach((person) => {
            const li = document.createElement('div');
            li.className = 'list-item';
            li.innerHTML = `<span>${person.nama}</span><button class="btn btn-delete" data-type="narasumber" data-id="${person.id}">Delete</button>`;
            narasumberListContainer.appendChild(li);
        });
    }

    function renderSelectedPanitia() {
        panitiaListBody.innerHTML = '';
        selectedPanitia.forEach((p) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `<td>${p.nama}</td><td>${p.peran}</td><td><button class="btn btn-delete" data-type="panitia" data-id="${p.id}">Delete</button></td>`;
            panitiaListBody.appendChild(tr);
        });
    }

    // --- Fungsi Modal & Pemilihan ---
    function openModal(type) {
        currentSelectionType = type;
        modalTitle.textContent = `Pilih ${type}`;
        roleInputContainer.classList.toggle('hidden', type !== 'Panitia');
        populateModalList(allAvailablePeople); // Tampilkan semua orang
        modal.classList.remove('hidden');
        modalSearchInput.value = '';
        modalSearchInput.focus();
    }

    function closeModal() {
        modal.classList.add('hidden');
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

    // --- Pengambilan Data Awal (dari DB) ---
    function fetchAvailablePeople() {
        // Kita bisa pakai ulang API get_narasumber.php
        fetch('../api/get_narasumber.php')
            .then(response => response.json())
            .then(data => {
                allAvailablePeople = data;
            })
            .catch(error => console.error('Gagal memuat daftar orang:', error));
    }

    // --- Event Listeners ---
    document.getElementById('add-narasumber-btn').addEventListener('click', () => openModal('Narasumber'));
    document.getElementById('add-panitia-btn').addEventListener('click', () => openModal('Panitia'));
    document.getElementById('close-modal-btn').addEventListener('click', closeModal);
    modal.addEventListener('click', (e) => (e.target === modal) && closeModal());

    modalSearchInput.addEventListener('input', (e) => {
        const searchTerm = e.target.value.toLowerCase();
        const filteredPeople = allAvailablePeople.filter(p => p.nama.toLowerCase().includes(searchTerm));
        populateModalList(filteredPeople);
    });

    modalListContainer.addEventListener('click', (e) => {
        if (e.target.classList.contains('modal-list-item')) {
            const personId = parseInt(e.target.dataset.id, 10);
            const person = allAvailablePeople.find(p => p.id === personId);

            if (currentSelectionType === 'Narasumber') {
                if (!selectedNarasumber.some(p => p.id === personId)) {
                    selectedNarasumber.push({ id: person.id, nama: person.nama, peran: 'Narasumber' });
                    renderSelectedNarasumber();
                } else { alert(`${person.nama} sudah ditambahkan.`); }
            } else if (currentSelectionType === 'Panitia') {
                const peran = panitiaRoleInput.value.trim();
                if (!peran) { alert('Harap isi peran panitia.'); return; }
                if (!selectedPanitia.some(p => p.id === personId)) {
                    selectedPanitia.push({ id: person.id, nama: person.nama, peran: peran });
                    renderSelectedPanitia();
                    panitiaRoleInput.value = ''; // Kosongkan input peran
                } else { alert(`${person.nama} sudah ditambahkan.`); }
            }
            closeModal();
        }
    });

    // Event Delegation untuk tombol delete
    document.querySelector('.main-content').addEventListener('click', e => {
        if (e.target.classList.contains('btn-delete')) {
            const type = e.target.dataset.type;
            const personId = parseInt(e.target.dataset.id, 10);
            if (type === 'narasumber') {
                selectedNarasumber = selectedNarasumber.filter(p => p.id !== personId);
                renderSelectedNarasumber();
            } else if (type === 'panitia') {
                selectedPanitia = selectedPanitia.filter(p => p.id !== personId);
                renderSelectedPanitia();
            }
        }
    });

    // --- Aksi Simpan Final ---
    saveButton.addEventListener('click', () => {
        // Gabungkan array narasumber dan panitia menjadi satu
        const semuaPeserta = [...selectedNarasumber, ...selectedPanitia];

        const payload = {
            id_webinar: webinarId,
            peserta: semuaPeserta
        };

        if (confirm('Apakah Anda yakin ingin menyimpan daftar peserta ini?')) {
            fetch('../api/simpan_peserta_proses.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.status === 'success') {
                    window.location.href = 'list_IFWS.php';
                }
            })
            .catch(error => console.error('Error:', error));
        }
    });

    // Panggil fungsi untuk mengambil data orang saat halaman dimuat
    fetchAvailablePeople();
});