document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchInput');
    const toggleEditBtn = document.getElementById('toggleEditBtn');
    const tableBody = document.getElementById('anggota-table-body');
    const actionsHeader = document.querySelector('.actions-header');

    let originalAnggotaData = [];

    function renderTable(data) {
        tableBody.innerHTML = '';
        if (data.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="4" style="text-align:center;">Data anggota tidak ditemukan.</td></tr>`;
            return;
        }
        data.forEach(item => {
            const row = document.createElement('tr');
            row.setAttribute('data-id', item.id);
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

    function fetchAnggota() {
        fetch('../api/get_anggota.php')
            .then(response => response.json())
            .then(data => {
                originalAnggotaData = data;
                renderTable(originalAnggotaData);
            })
            .catch(error => console.error('Gagal memuat data anggota:', error));
    }

    searchInput.addEventListener('input', () => {
        const query = searchInput.value.toLowerCase();
        const filteredData = originalAnggotaData.filter(item => 
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
        const target = event.target;

        if (target.classList.contains('edit-btn')) {
            const anggotaId = target.closest('tr').getAttribute('data-id');
            window.location.href = `edit_anggota.php?id=${anggotaId}`;
        }

        // --- BLOK KODE DELETE YANG DIPERBARUI ---
        if (target.classList.contains('delete-btn')) {
            const row = target.closest('tr');
            const anggotaId = row.getAttribute('data-id');
            const anggotaNama = row.querySelector('td').textContent; // Ambil nama dari sel pertama

            // Tampilkan dialog konfirmasi sebelum menghapus
            if (confirm(`Apakah Anda yakin ingin menghapus anggota "${anggotaNama}"?`)) {
                
                // Kirim ID ke server menggunakan Fetch API
                fetch('../api/delete_anggota_proses.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${anggotaId}` 
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message); // Tampilkan pesan dari server
                    if (data.status === 'success') {
                        // Jika berhasil, hapus baris dari tabel tanpa perlu refresh halaman
                        row.remove();
                    }
                })
                .catch(error => {
                    console.error('Error deleting data:', error);
                    alert('Terjadi kesalahan saat menghapus data.');
                });
            }
        }
    });

    fetchAnggota();
});