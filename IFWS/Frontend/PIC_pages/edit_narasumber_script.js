document.addEventListener('DOMContentLoaded', () => {
    // Data ini seharusnya datang dari server, tapi kita simulasikan di sini.
    const narasumberData = [
        { id: 1, nama: 'Nathaniel Cooper', email: 'nathaniel.cooper@gmail.com' },
        { id: 2, nama: 'Victoria Bennett', email: 'victoria.bennett@gmail.com' },
        { id: 3, nama: 'Samuel Turner', email: 'samuel.turner@gmail.com' },
        // ... (data lainnya)
    ];

    const narasumberForm = document.getElementById('edit-narasumber-form');
    const narasumberIdInput = document.getElementById('narasumberId');
    const namaInput = document.getElementById('nama');
    const emailInput = document.getElementById('email');

    // Ambil ID dari URL
    const urlParams = new URLSearchParams(window.location.search);
    const speakerId = urlParams.get('id');

    if (!speakerId) {
        alert('ID Narasumber tidak valid!');
        window.location.href = 'list_narasumber.html';
        return;
    }

    // Cari data narasumber dan isi form
    const speaker = narasumberData.find(s => s.id == speakerId);
    if (speaker) {
        narasumberIdInput.value = speaker.id;
        namaInput.value = speaker.nama;
        emailInput.value = speaker.email;
    } else {
        alert('Narasumber tidak ditemukan!');
        window.location.href = 'list_narasumber.html';
    }

    // Logika saat form disubmit
    narasumberForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const id = narasumberIdInput.value;
        const nama = namaInput.value.trim();
        
        // Di aplikasi nyata, Anda akan mengirim data ini ke server.
        alert(`Data narasumber (ID: ${id}) dengan nama "${nama}" telah berhasil diperbarui!`);
        
        window.location.href = 'list_narasumber.html';
    });
});