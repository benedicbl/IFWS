document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('form-anggota');
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        const nama = document.getElementById('nama').value;
        const email = document.getElementById('email').value;
        const jabatan = document.getElementById('jabatan').value;

        alert(`Anggota baru "${nama}" dengan jabatan "${jabatan}" berhasil ditambahkan!`);
        window.location.href = 'list_anggota.html';
    });
});