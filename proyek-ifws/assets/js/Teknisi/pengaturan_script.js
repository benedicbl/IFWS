document.addEventListener('DOMContentLoaded', () => {
    const simpanBtn = document.getElementById('simpan-pengaturan-btn');
    const minDurationInput = document.getElementById('min-duration');
    const minIfwsInput = document.getElementById('min-ifws');

    simpanBtn.addEventListener('click', (e) => {
        e.preventDefault();

        // MODIFIKASI 1: Sesuaikan nama key agar cocok dengan peraturan_handler.php
        const settingsData = {
            peraturan_waktu: minDurationInput.value, // sebelumnya 'min_duration'
            peraturan_sidang: minIfwsInput.value      // sebelumnya 'min_ifws'
        };

        // MODIFIKASI 2: Ubah URL API ke file handler yang sudah ada
        fetch('/proyek-ifws/api/peraturan_handler.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(settingsData)
        })
        .then(response => response.json())
        .then(data => {
            // Respons akan ditangani oleh peraturan_handler.php
            alert(data.pesan);
        })
        .catch(error => {
            console.error('Error updating settings:', error);
            alert('Gagal menyimpan pengaturan.');
        });
    });
});