document.addEventListener('DOMContentLoaded', () => {

    // --- DOM Elements ---
    const pilihTahunBtn = document.getElementById('pilihTahunBtn');
    const tahunDropdown = document.getElementById('tahunDropdown');
    const initialMessage = document.getElementById('initial-message');
    const webinarSection = document.getElementById('webinar-section');
    const tahunAkademikTitle = document.getElementById('tahunAkademikTitle');
    const webinarTableBody = document.getElementById('webinar-table-body');

    // --- Dropdown Logic ---
    pilihTahunBtn.addEventListener('click', () => {
        tahunDropdown.classList.toggle('show');
    });

    window.addEventListener('click', (event) => {
        if (!event.target.matches('.dropdown-btn')) {
            if (tahunDropdown.classList.contains('show')) {
                tahunDropdown.classList.remove('show');
            }
        }
    });

    tahunDropdown.addEventListener('click', (event) => {
        event.preventDefault();
        const selectedTahun = event.target.getAttribute('data-tahun');
        if (selectedTahun) {
            fetchAndDisplayRiwayat(selectedTahun);
            tahunDropdown.classList.remove('show');
        }
    });

    // --- Fungsi utama untuk mengambil dan menampilkan data riwayat dari API ---
    function fetchAndDisplayRiwayat(tahun) {
        tahunAkademikTitle.textContent = `Tahun Akademik ${tahun}`;
        webinarTableBody.innerHTML = `<tr><td colspan="5" style="text-align: center;">Memuat data...</td></tr>`;

        // Panggil API baru kita
        fetch(`../api/get_riwayat.php?tahun=${tahun}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Setelah data diterima, render ke tabel
                renderTable(data);
            })
            .catch(error => {
                console.error('Error fetching riwayat data:', error);
                webinarTableBody.innerHTML = `<tr><td colspan="5" style="text-align: center; color: red;">Gagal memuat data.</td></tr>`;
            });
    }

    // --- Fungsi untuk merender tabel ---
    function renderTable(data) {
        webinarTableBody.innerHTML = '';
        
        if (data.length > 0) {
            data.forEach(webinar => {
                const row = document.createElement('tr');
                
                // --- Logika BARU untuk tombol berdasarkan data dari API ---
                let actionButtonHtml = '';
                // Cek properti 'jumlah_screenshot' yang dikirim dari API
                if (webinar.jumlah_screenshot > 0) {
                    actionButtonHtml = `<a href="upload_screenshot.php?id=${webinar.id}" class="btn-lihat">Lihat (${webinar.jumlah_screenshot})</a>`;
                } else {
                    actionButtonHtml = `<a href="upload_screenshot.php?id=${webinar.id}" class="upload-btn">Upload</a>`;
                }

                // Format tanggal ke format Indonesia
                const formattedDate = new Date(webinar.tanggal).toLocaleDateString('id-ID', {
                    day: '2-digit', month: 'long', year: 'numeric'
                });

                row.innerHTML = `
                    <td>${webinar.narasumber}</td>
                    <td>${formattedDate}</td>
                    <td>${webinar.jenis_ifws || 'N/A'}</td>
                    <td>${webinar.topik}</td>
                    <td>
                        ${actionButtonHtml}
                    </td>
                `;
                webinarTableBody.appendChild(row);
            });
        } else {
             const row = document.createElement('tr');
             row.innerHTML = `<td colspan="5" style="text-align: center;">Tidak ada data webinar untuk tahun ini.</td>`;
             webinarTableBody.appendChild(row);
        }

        initialMessage.classList.add('hidden');
        webinarSection.classList.remove('hidden');
    }
});