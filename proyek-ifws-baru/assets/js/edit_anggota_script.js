document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('form-anggota');
    const anggotaIdInput = document.getElementById('anggotaId');
    const namaInput = document.getElementById('nama');
    const emailInput = document.getElementById('email');
    const jabatanSelect = document.getElementById('jabatan');

    const jabatanOptions = ['PIC', 'Sekretariat', 'Bendahara', 'Promosi', 'Teknisi'];

    // Mengisi dropdown jabatan dengan pilihan yang ada
    jabatanOptions.forEach(opt => {
        const optionEl = document.createElement('option');
        optionEl.value = opt;
        optionEl.textContent = opt;
        jabatanSelect.appendChild(optionEl);
    });

    const urlParams = new URLSearchParams(window.location.search);
    const anggotaId = urlParams.get('id');

    // --- BAGIAN A: MENGAMBIL DAN MENGISI DATA FORM ---
    if (!anggotaId) {
        alert('ID Anggota tidak valid!');
        window.location.href = 'list_anggota.php';
        return;
    }

    fetch(`../api/get_single_anggota.php?id=${anggotaId}`)
        .then(response => response.json())
        .then(anggota => {
            if (anggota) {
                anggotaIdInput.value = anggota.id;
                namaInput.value = anggota.nama;
                emailInput.value = anggota.email;
                jabatanSelect.value = anggota.jabatan;
            } else {
                alert('Anggota tidak ditemukan!');
                window.location.href = 'list_anggota.php';
            }
        })
        .catch(error => console.error('Gagal memuat data anggota:', error));

    // --- BAGIAN B: MENGIRIM PERUBAHAN SAAT SUBMIT ---
    form.addEventListener('submit', (e) => {
        e.preventDefault();

        const formData = new FormData(form);

        fetch('../api/update_anggota_proses.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.status === 'success') {
                window.location.href = 'list_anggota.php';
            }
        })
        .catch(error => console.error('Error updating data:', error));
    });
});