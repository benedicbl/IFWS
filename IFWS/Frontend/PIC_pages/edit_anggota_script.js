document.addEventListener('DOMContentLoaded', () => {
    const anggotaData = [ // Data yang sama dari list_anggota_script.js
        { id: 1, nama: 'Benny Luswana', email: 'benny@example.com', jabatan: 'PIC' },
        // ...data lainnya
    ];
    
    const jabatanOptions = ['PIC', 'Sekretariat', 'Bendahara', 'Promosi', 'Teknisi'];

    const form = document.getElementById('form-anggota');
    const anggotaIdInput = document.getElementById('anggotaId');
    const namaInput = document.getElementById('nama');
    const emailInput = document.getElementById('email');
    const jabatanSelect = document.getElementById('jabatan');

    // Populate dropdown jabatan
    jabatanOptions.forEach(opt => {
        const optionEl = document.createElement('option');
        optionEl.value = opt;
        optionEl.textContent = opt;
        jabatanSelect.appendChild(optionEl);
    });

    const urlParams = new URLSearchParams(window.location.search);
    const anggotaId = urlParams.get('id');
    const anggota = anggotaData.find(a => a.id == anggotaId);

    if (anggota) {
        anggotaIdInput.value = anggota.id;
        namaInput.value = anggota.nama;
        emailInput.value = anggota.email;
        jabatanSelect.value = anggota.jabatan;
    } else {
        alert('Anggota tidak ditemukan!');
        window.location.href = 'list_anggota.html';
    }

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        alert('Data anggota berhasil diperbarui!');
        window.location.href = 'list_anggota.html';
    });
});
