document.addEventListener('DOMContentLoaded', () => {

    // --- DOM Elements ---
    const searchInput = document.getElementById('searchInput');
    const toggleEditBtn = document.getElementById('toggleEditBtn');
    const tableBody = document.getElementById('narasumber-table-body');
    const actionsHeader = document.querySelector('.actions-header');

    // --- Fungsi untuk merender/menampilkan data ke dalam tabel HTML ---
    function renderTable(data) {
        tableBody.innerHTML = ''; 

        if (data.length === 0) {
            const row = document.createElement('tr');
            row.innerHTML = `<td colspan="3" style="text-align: center;">Data narasumber tidak ditemukan.</td>`;
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

    // --- Fungsi untuk mengambil data dari server via API ---
    function fetchNarasumber() {
        fetch('../api/get_narasumber.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                renderTable(data);
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                tableBody.innerHTML = `<tr><td colspan="3" style="text-align: center; color: red;">Gagal memuat data.</td></tr>`;
            });
    }

    // --- Event Listeners ---

    // Event listener untuk tombol Edit (mengaktifkan/menonaktifkan mode edit)
    toggleEditBtn.addEventListener('click', () => {
        const isEditing = tableBody.classList.toggle('edit-mode');
        actionsHeader.classList.toggle('hidden', !isEditing);
        toggleEditBtn.textContent = isEditing ? 'Selesai' : 'Edit';
        toggleEditBtn.style.backgroundColor = isEditing ? '#2ecc71' : '#7f8c8d';
    });
    
    // Event listener untuk tombol-tombol di dalam tabel (Edit per baris dan Delete)
    tableBody.addEventListener('click', (event) => {
        const target = event.target;

        // Jika tombol Edit di salah satu baris diklik
        if (target.classList.contains('edit-btn')) {
            const row = target.closest('tr');
            const narasumberId = row.getAttribute('data-id').trim(); // Tambahkan .trim()
            window.location.href = `edit_narasumber.php?id=${narasumberId}`;
        }

        // Jika tombol Delete di salah satu baris diklik
        if (target.classList.contains('delete-btn')) {
            const row = target.closest('tr');
            const narasumberId = row.getAttribute('data-id');

            // Tampilkan dialog konfirmasi sebelum menghapus
            if (confirm(`Apakah Anda yakin ingin menghapus narasumber ini?`)) {
                
                // Kirim ID ke server menggunakan Fetch API dengan metode POST
                fetch('../api/delete_narasumber_proses.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${narasumberId}` 
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message); // Tampilkan pesan dari server (sukses/gagal)
                    if (data.status === 'success') {
                        // Jika berhasil, hapus baris dari tabel tanpa refresh halaman
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

    // --- Pencarian (Untuk saat ini dinonaktifkan agar tidak membingungkan) ---
    searchInput.addEventListener('input', (e) => {
        // Logika pencarian akan diimplementasikan nanti jika diperlukan
    });

    // Panggil fungsi untuk mengambil data pertama kali saat halaman dimuat
    fetchNarasumber();
});