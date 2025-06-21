document.addEventListener('DOMContentLoaded', () => {

    // --- FAKE DATA NARASUMBER ---
    const narasumberData = [
        { id: 1, nama: 'Nathaniel Cooper', email: 'nathaniel.cooper@gmail.com' },
        { id: 2, nama: 'Victoria Bennett', email: 'victoria.bennett@gmail.com' },
        { id: 3, nama: 'Samuel Turner', email: 'samuel.turner@gmail.com' },
        { id: 4, nama: 'Lily Parker', email: 'lily.parker@gmail.com' },
        { id: 5, nama: 'Elijah Murphy', email: 'elijah.murphy@gmail.com' },
        { id: 6, nama: 'Grace Collins', email: 'grace.collins@gmail.com' },
        { id: 7, nama: 'Matthew Richardson', email: 'matthew.richardson@gmail.com' },
        { id: 8, nama: 'Scarlett Hayes', email: 'scarlett.hayes@gmail.com' },
        { id: 9, nama: 'David Brooks', email: 'david.brooks@gmail.com' },
        { id: 10, nama: 'Zoey Campbell', email: 'zoey.campbell@gmail.com' }
    ];

    // --- DOM Elements ---
    const searchInput = document.getElementById('searchInput');
    const toggleEditBtn = document.getElementById('toggleEditBtn');
    const tableBody = document.getElementById('narasumber-table-body');
    const actionsHeader = document.querySelector('.actions-header');

    // --- Function to Render Table ---
    function renderTable(data) {
        tableBody.innerHTML = ''; // Kosongkan tabel sebelum render

        if (data.length === 0) {
            const row = document.createElement('tr');
            row.innerHTML = `<td colspan="3" style="text-align: center;">Narasumber tidak ditemukan.</td>`;
            tableBody.appendChild(row);
            return;
        }

        data.forEach(narasumber => {
            const row = document.createElement('tr');
            row.setAttribute('data-id', narasumber.id);
            row.innerHTML = `
                <td>${narasumber.nama}</td>
                <td>${narasumber.email}</td>
                <td class="row-actions">
                    <button class="action-btn edit-btn">Edit</button>
                    <button class="action-btn delete-btn">Delete</button>
                </td>
            `;
            tableBody.appendChild(row);
        });
    }

    // --- Search Logic ---
    searchInput.addEventListener('input', (e) => {
        const searchTerm = e.target.value.toLowerCase().trim();
        const filteredData = narasumberData.filter(narasumber =>
            narasumber.nama.toLowerCase().includes(searchTerm) ||
            narasumber.email.toLowerCase().includes(searchTerm)
        );
        renderTable(filteredData);
    });

    // --- Toggle Edit Mode Logic (Sama seperti List IFWS) ---
    toggleEditBtn.addEventListener('click', () => {
        const isEditing = tableBody.classList.toggle('edit-mode');
        actionsHeader.classList.toggle('hidden', !isEditing);

        if (isEditing) {
            toggleEditBtn.textContent = 'Selesai';
            toggleEditBtn.style.backgroundColor = '#2ecc71';
        } else {
            toggleEditBtn.textContent = 'Edit';
            toggleEditBtn.style.backgroundColor = '#7f8c8d';
        }
    });

    // --- Row Action Logic (Sama seperti List IFWS) ---
    tableBody.addEventListener('click', (event) => {
        const target = event.target;

        if (target.classList.contains('edit-btn')) {
            const row = target.closest('tr');
            const narasumberId = row.getAttribute('data-id');
            // Arahkan ke halaman edit yang baru
            window.location.href = `edit_narasumber.html?id=${narasumberId}`;
        }

        if (target.classList.contains('delete-btn')) {
            const row = target.closest('tr');
            if (confirm('Apakah Anda yakin ingin menghapus narasumber ini?')) {
                row.remove();
                // Di aplikasi nyata, kirim permintaan hapus ke server
                alert('Narasumber telah dihapus.');
            }
        }
    });

    // --- Initial Render ---
    renderTable(narasumberData);
});