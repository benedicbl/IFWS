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
        const newValue = inputJumlah.value;
        alert(`Pengaturan syarat sidang berhasil disimpan: ${newValue} IFWS.`);
        modal.classList.add('hidden');
        // Arahkan kembali ke dashboard teknisi
        // window.location.href = '../homepage_teknisi.html';
    });
});