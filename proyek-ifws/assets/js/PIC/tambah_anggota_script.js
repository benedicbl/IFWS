document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('form-anggota');

    form.addEventListener('submit', (e) => {
        e.preventDefault(); // Mencegah form refresh halaman

        // Kumpulkan data dari form
        const formData = new FormData(form);

        // Kirim data ke server menggunakan Fetch API
        fetch('../api/tambah_anggota_proses.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message); // Tampilkan pesan dari server
            if (data.status === 'success') {
                // Jika sukses, arahkan ke halaman list
                window.location.href = 'list_anggota.php';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengirim data.');
        });
    });
});