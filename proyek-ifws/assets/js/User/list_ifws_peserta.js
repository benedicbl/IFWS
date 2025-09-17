document.addEventListener('DOMContentLoaded', () => {

    // --- FAKE DATA WEBINAR (dengan dateTime untuk perbandingan) ---
    const webinarData = {
        '2025-Ganjil': [
            // Contoh webinar yang akan datang (tombol aktif)
            // Karena sekarang tanggal 12 September 2025, webinar ini akan datang
            { 
                id: 101, narasumber: 'John Doe', tanggal: '25 / 10 / 2025', 
                waktuMulai: '09:00', waktuSelesai: '11:00', jenis: 'Internal - Jurusan', 
                topik: 'Introduction To 3D Computer Vision', dateTime: '2025-10-25T09:00:00' 
            },
            // Contoh webinar yang sudah lewat (tombol nonaktif)
            // Karena sekarang tanggal 12 September 2025, webinar ini sudah lewat
            { 
                id: 102, narasumber: 'Arthur Nightingale', tanggal: '15 / 08 / 2025',
                waktuMulai: '13:00', waktuSelesai: '15:00', jenis: 'Umum',
                topik: 'Real-World Examples of Cloud Reliability...', dateTime: '2025-08-15T13:00:00'
            },
             { 
                id: 103, narasumber: 'Kushala Daora', tanggal: '30 / 11 / 2025',
                waktuMulai: '10:00', waktuSelesai: '12:00', jenis: 'Internal',
                topik: 'Sistem Informasi Apotek Adora', dateTime: '2025-11-30T10:00:00'
            },
        ],
        '2024-Genap': [] 
    };

    const pilihTahunBtn = document.getElementById('pilihTahunBtn');
    const tahunDropdown = document.getElementById('tahunDropdown');
    const initialMessage = document.getElementById('initial-message');
    const webinarSection = document.getElementById('webinar-section');
    const tahunAkademikTitle = document.getElementById('tahunAkademikTitle');
    const webinarTableBody = document.getElementById('webinar-table-body');

    // --- Logika Dropdown ---
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
            displayWebinars(selectedTahun);
            tahunDropdown.classList.remove('show');
        }
    });

    // --- Fungsi Utama untuk Menampilkan Webinar ---
    function displayWebinars(tahun) {
        tahunAkademikTitle.textContent = `Tahun Akademik ${tahun}`;
        webinarTableBody.innerHTML = '';
        const now = new Date(); // Ambil waktu saat ini

        const data = webinarData[tahun] || [];
        if (data.length > 0) {
            data.forEach(webinar => {
                const webinarDate = new Date(webinar.dateTime);
                const isPast = webinarDate < now;

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${webinar.narasumber}</td>
                    <td>${webinar.tanggal}</td>
                    <td>${webinar.waktuMulai}</td>
                    <td>${webinar.waktuSelesai}</td>
                    <td>${webinar.jenis}</td>
                    <td>${webinar.topik}</td>
                    <td>
                        <button class="btn-daftar" ${isPast ? 'disabled' : ''}>
                            ${isPast ? 'Selesai' : 'Daftar'}
                        </button>
                    </td>
                `;
                webinarTableBody.appendChild(row);
            });
        } else {
             const row = document.createElement('tr');
             row.innerHTML = `<td colspan="7" style="text-align: center;">Tidak ada webinar yang tersedia untuk tahun ini.</td>`;
             webinarTableBody.appendChild(row);
        }

        initialMessage.classList.add('hidden');
        webinarSection.classList.remove('hidden');
    }
    
    // Event listener untuk tombol daftar
    webinarTableBody.addEventListener('click', (e) => {
        if (e.target.classList.contains('btn-daftar') && !e.target.disabled) {
            alert('Anda berhasil mendaftar untuk webinar ini!');
            e.target.disabled = true;
            e.target.textContent = 'Terdaftar';
        }
    });
});