document.addEventListener('DOMContentLoaded', () => {
    const narasumberForm = document.getElementById('tambah-narasumber-form');

    narasumberForm.addEventListener('submit', (e) => {
        e.preventDefault();

        const nama = document.getElementById('nama').value.trim();
        const email = document.getElementById('email').value.trim();

        if (nama === '' || email === '') {
            alert('Nama dan Email tidak boleh kosong!');
            return;
        }

        // Di aplikasi nyata, Anda akan mengirim data ini ke server.
        alert(`Narasumber baru "${nama}" telah berhasil ditambahkan!`);

        // Kembali ke halaman list
        window.location.href = 'list_narasumber.html';
    });
});