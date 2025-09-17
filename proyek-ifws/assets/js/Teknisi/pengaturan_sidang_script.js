document.addEventListener('DOMContentLoaded', () => {
    const saveBtn = document.getElementById('save-btn');
    const modal = document.getElementById('confirmation-modal');
    const cancelBtn = document.getElementById('cancel-btn');
    const confirmSaveBtn = document.getElementById('confirm-save-btn');
    const inputJumlah = document.getElementById('jumlah-ifws');

    // Tampilkan modal saat tombol Simpan utama diklik
    saveBtn.addEventListener('click', (e) => {
        e.preventDefault();
        modal.classList.remove('hidden');
    });

    // Sembunyikan modal saat Batal diklik
    cancelBtn.addEventListener('click', () => {
        modal.classList.add('hidden');
    });

    // Lakukan aksi simpan saat Ya, Simpan diklik
    confirmSaveBtn.addEventListener('click', () => {
        const nilaiJumlahIfws = inputJumlah.value;
        
        confirmSaveBtn.textContent = 'Menyimpan...';
        confirmSaveBtn.disabled = true;

        // Kirim data ke API
        fetch('/proyek-ifws/api/peraturan_handler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ peraturan_sidang: nilaiJumlahIfws })
        })
            .then(response => response.json())
            .then(data => {
                if (data.sukses) {
                    alert('Perubahan berhasil disimpan!');
                } else {
                    alert('Gagal menyimpan perubahan: ' + data.pesan);
                }
                modal.classList.add('hidden');
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menghubungi server.');
                modal.classList.add('hidden');
            })
            .finally(() => {
                confirmSaveBtn.textContent = 'Ya, Simpan';
                confirmSaveBtn.disabled = false;
            });
    });
});