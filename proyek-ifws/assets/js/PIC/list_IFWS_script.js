document.addEventListener('DOMContentLoaded', () => {

    // --- DOM Elements ---
    const pilihTahunBtn = document.getElementById('pilihTahunBtn');
    const tahunDropdown = document.getElementById('tahunDropdown');
    const initialMessage = document.getElementById('initial-message');
    const webinarSection = document.getElementById('webinar-section');
    const tahunAkademikTitle = document.getElementById('tahunAkademikTitle');
    const webinarTableBody = document.getElementById('webinar-table-body');
    const toggleEditBtn = document.getElementById('toggleEditBtn');
    const actionsHeader = document.querySelector('.actions-header');

    // --- Dropdown & Year Selection Logic ---
    pilihTahunBtn.addEventListener('click', () => tahunDropdown.classList.toggle('show'));

    window.addEventListener('click', (event) => {
        if (!event.target.matches('.dropdown-btn') && tahunDropdown.classList.contains('show')) {
            tahunDropdown.classList.remove('show');
        }
    });

    tahunDropdown.addEventListener('click', (event) => {
        event.preventDefault();
        const selectedTahun = event.target.getAttribute('data-tahun');
        if (selectedTahun) {
            fetchAndDisplayWebinars(selectedTahun);
            tahunDropdown.classList.remove('show');
        }
    });

    // --- Fungsi untuk mengambil & menampilkan webinar ---
    function fetchAndDisplayWebinars(tahun) {
        tahunAkademikTitle.textContent = `Tahun Akademik ${tahun}`;
        webinarTableBody.innerHTML = `<tr><td colspan="5" style="text-align: center;">Memuat data...</td></tr>`; 
        fetch(`../api/get_webinar.php?tahun=${tahun}`)
            .then(response => response.json())
            .then(data => renderTable(data))
            .catch(error => {
                console.error('Error fetching webinar data:', error);
                webinarTableBody.innerHTML = `<tr><td colspan="5" style="text-align: center; color: red;">Gagal memuat data.</td></tr>`;
            });
    }
    
    // --- Fungsi untuk merender data ke tabel ---
    function renderTable(data) {
        webinarTableBody.innerHTML = '';
        webinarTableBody.classList.remove('edit-mode');
        if(actionsHeader) actionsHeader.classList.add('hidden');
        if(toggleEditBtn) {
            toggleEditBtn.textContent = 'Edit';
            toggleEditBtn.style.backgroundColor = '#7f8c8d';
        }
        if (data.length > 0) {
            data.forEach(webinar => {
                const row = document.createElement('tr');
                row.setAttribute('data-id', webinar.id);
                const formattedDate = new Date(webinar.tanggal).toLocaleDateString('id-ID', {
                    day: '2-digit', month: 'long', year: 'numeric'
                });
                row.innerHTML = `
                    <td>${webinar.narasumber}</td>
                    <td>${formattedDate}</td>
                    <td>${webinar.jenis_ifws || 'N/A'}</td>
                    <td class="webinar-topic">${webinar.topik}</td>
                    <td class="row-actions">
                        <button class="action-btn edit-btn">Edit</button>
                        <button class="action-btn delete-btn">Delete</button>
                    </td>
                `;
                webinarTableBody.appendChild(row);
            });
        } else {
             const row = document.createElement('tr');
             row.innerHTML = `<td colspan="5" style="text-align: center;">Tidak ada data webinar untuk tahun ini.</td>`;
             webinarTableBody.appendChild(row);
        }
        if(initialMessage) initialMessage.classList.add('hidden');
        if(webinarSection) webinarSection.classList.remove('hidden');
    }
    
    // --- Event Listeners ---
    if(toggleEditBtn) {
        toggleEditBtn.addEventListener('click', () => {
            const isEditing = webinarTableBody.classList.toggle('edit-mode');
            if(actionsHeader) actionsHeader.classList.toggle('hidden', !isEditing);
            toggleEditBtn.textContent = isEditing ? 'Selesai' : 'Edit';
            toggleEditBtn.style.backgroundColor = isEditing ? '#2ecc71' : '#7f8c8d';
        });
    }

    if(webinarTableBody) {
        webinarTableBody.addEventListener('click', (event) => {
            const target = event.target;
            
            if (target.classList.contains('edit-btn')) {
                const row = target.closest('tr');
                const webinarId = row.getAttribute('data-id').trim();
                if (webinarId) {
                    window.location.href = `edit_webinar.php?id=${webinarId}`;
                } else {
                    alert('Gagal mendapatkan ID webinar dari baris ini.');
                }
            }

            // --- LOGIKA DELETE YANG DIPERBARUI ---
            if (target.classList.contains('delete-btn')) {
                const row = target.closest('tr');
                const webinarId = row.getAttribute('data-id');
                const webinarTopik = row.querySelector('.webinar-topic').textContent;

                if (confirm(`Apakah Anda yakin ingin menghapus webinar dengan topik "${webinarTopik}"?`)) {
                    fetch('../api/delete_webinar_proses.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `id=${webinarId}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        alert(data.message);
                        if (data.status === 'success') {
                            row.remove(); // Hapus baris dari tabel
                        }
                    })
                    .catch(error => {
                        console.error('Error deleting data:', error);
                        alert('Terjadi kesalahan saat menghapus data.');
                    });
                }
            }
        });
    }
});