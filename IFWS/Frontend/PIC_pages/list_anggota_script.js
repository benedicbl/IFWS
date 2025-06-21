document.addEventListener('DOMContentLoaded', () => {
    // --- DATABASE ANGGOTA SEMU ---
    const anggotaData = [
        { id: 1, nama: 'Benny Luswana', email: 'benny@example.com', jabatan: 'PIC' },
        { id: 2, nama: 'Sekretaris Satu', email: 'sekre1@example.com', jabatan: 'Sekretariat' },
        { id: 3, nama: 'Bendahara Utama', email: 'bendahara@example.com', jabatan: 'Bendahara' },
        { id: 4, nama: 'Tim Promosi', email: 'promosi@example.com', jabatan: 'Promosi' },
        { id: 5, nama: 'Ahli Teknis', email: 'teknisi@example.com', jabatan: 'Teknisi' },
    ];

    const searchInput = document.getElementById('searchInput');
    const toggleEditBtn = document.getElementById('toggleEditBtn');
    const tableBody = document.getElementById('anggota-table-body');
    const actionsHeader = document.querySelector('.actions-header');

    function renderTable(data) {
        tableBody.innerHTML = '';
        data.forEach(item => {
            const row = document.createElement('tr');
            row.setAttribute('data-id', item.id);
            // Tombol diubah menjadi teks
            row.innerHTML = `
                <td>${item.nama}</td>
                <td>${item.email}</td>
                <td>${item.jabatan}</td>
                <td class="row-actions">
                    <button class="action-btn edit-btn">Edit</button>
                    <button class="action-btn delete-btn">Delete</button>
                </td>
            `;
            tableBody.appendChild(row);
        });
    }

    renderTable(anggotaData);

    searchInput.addEventListener('input', () => {
        const query = searchInput.value.toLowerCase();
        const filteredData = anggotaData.filter(item => 
            item.nama.toLowerCase().includes(query) ||
            item.email.toLowerCase().includes(query) ||
            item.jabatan.toLowerCase().includes(query)
        );
        renderTable(filteredData);
    });

    toggleEditBtn.addEventListener('click', () => {
        const isEditing = tableBody.classList.toggle('edit-mode');
        actionsHeader.classList.toggle('hidden', !isEditing);
    });

    tableBody.addEventListener('click', (event) => {
        // Logika di sini tidak perlu diubah karena mengandalkan class, bukan isi tombol
        const target = event.target;
        if (target.classList.contains('edit-btn')) {
            const row = target.closest('tr');
            const anggotaId = row.getAttribute('data-id');
            window.location.href = `edit_anggota.html?id=${anggotaId}`;
        }

        if (target.classList.contains('delete-btn')) {
            const row = target.closest('tr');
            if (confirm('Apakah Anda yakin ingin menghapus anggota ini?')) {
                row.remove();
                alert('Anggota telah dihapus.');
            }
        }
    });
});