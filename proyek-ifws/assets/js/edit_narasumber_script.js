document.addEventListener('DOMContentLoaded', () => {
    // DOM Elements
    const narasumberForm = document.getElementById('edit-narasumber-form');
    const narasumberIdInput = document.getElementById('narasumberId');
    const namaInput = document.getElementById('nama');
    const emailInput = document.getElementById('email');

    const urlParams = new URLSearchParams(window.location.search);
    const speakerId = urlParams.get('id');

    // --- BAGIAN A: MENGAMBIL DAN MENAMPILKAN DATA ---
    if (!speakerId) {
        alert('ID Narasumber tidak valid!');
        window.location.href = 'list_narasumber.php';
        return;
    }

    fetch(`../api/get_single_narasumber.php?id=${speakerId}`)
        .then(response => response.json())
        .then(speaker => {
            if (speaker) {
                narasumberIdInput.value = speaker.id;
                namaInput.value = speaker.nama;
                emailInput.value = speaker.email;
            } else {
                alert('Narasumber tidak ditemukan!');
                window.location.href = 'list_narasumber.php';
            }
        })
        .catch(error => {
            console.error('Error fetching speaker data:', error);
            alert('Gagal memuat data narasumber.');
        });

    // --- BAGIAN B: MENYIMPAN PERUBAHAN ---
    narasumberForm.addEventListener('submit', (e) => {
        e.preventDefault(); // Mencegah refresh halaman

        const formData = new FormData(narasumberForm);

        fetch('../api/update_narasumber_proses.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message); // Tampilkan pesan dari server
            if (data.status === 'success') {
                // Jika sukses, kembali ke halaman list
                window.location.href = 'list_narasumber.php';
            }
        })
        .catch(error => {
            console.error('Error updating data:', error);
            alert('Terjadi kesalahan saat menyimpan perubahan.');
        });
    });
});