document.addEventListener('DOMContentLoaded', () => {
    const narasumberForm = document.getElementById('tambah-narasumber-form');

    narasumberForm.addEventListener('submit', (e) => {
        // Mencegah perilaku default form (yang akan me-refresh halaman)
        e.preventDefault();

        // Mengumpulkan semua data dari form
        const formData = new FormData(narasumberForm);
        
        // Menggunakan Fetch API untuk mengirim data ke server (AJAX)
        // Ini membuat halaman tidak perlu refresh saat menyimpan data.
        fetch('../api/tambah_narasumber_proses.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json()) // Mengubah respons dari server menjadi objek JSON
        .then(data => {
            // Menampilkan pesan dari server
            alert(data.message);

            // Jika status dari server adalah 'success', arahkan ke halaman list
            if (data.status === 'success') {
                window.location.href = 'list_narasumber.php';
            }
        })
        .catch(error => {
            // Menangani jika ada error jaringan atau masalah lainnya
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengirim data.');
        });
    });
});