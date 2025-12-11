document.addEventListener('DOMContentLoaded', function() {
    // --- Elemen Overlay ---
    const tambahOverlay = document.getElementById('tambah-anggota-overlay');
    const editOverlay = document.getElementById('edit-anggota-overlay'); // Overlay baru

    // --- Event Listener Utama (Delegasi) ---
    document.body.addEventListener('click', function(event) {
        const target = event.target;
        const targetClosest = (selector) => target.closest(selector);

        // --- Logika Tombol Buka Overlay ---
        // Tombol Buka Form Tambah
        if (targetClosest('#btn-tambah-anggota')) {
            handleOpenTambah();
        }
        // Tombol Buka Form Edit
        else if (targetClosest('.btn-edit-anggota')) {
            handleOpenEdit(targetClosest('.btn-edit-anggota'));
        }

        // --- Logika Tombol Lihat Password (jika tabel akun masih ada) ---
        // (Kode ini bisa diabaikan jika tabel akun sudah dihapus, tapi tidak error jika tetap ada)
        const toggleBtn = targetClosest('.btn-toggle-password');
        if (toggleBtn) {
            handleTogglePassword(toggleBtn);
        }

        // --- Logika Tombol Tutup Overlay ---
        if (target.classList.contains('overlay')) {
            target.classList.add('hidden');
        }
        if (targetClosest('.btn-close-overlay')) {
            targetClosest('.overlay').classList.add('hidden');
        }
    });

    // --- Fungsi Handler ---
    function handleOpenTambah() {
        if (tambahOverlay) {
            const form = tambahOverlay.querySelector('form');
            const errorDiv = tambahOverlay.querySelector('#tambah-form-error');
            if (form) form.reset();
            if(errorDiv) errorDiv.classList.add('hidden'); // Sembunyikan error lama
            tambahOverlay.classList.remove('hidden');
        }
    }

    function handleOpenEdit(button) {
        if (!editOverlay) return;
        const id = button.dataset.id;
        const nama = button.dataset.nama;
        const email = button.dataset.email;
        const role = button.dataset.role;

        const form = editOverlay.querySelector('form');
        const errorDiv = editOverlay.querySelector('#edit-form-error');

        // Isi form edit
        if (form) {
            form.querySelector('#edit_anggota_id').value = id;
            form.querySelector('#edit_nama_lengkap').value = nama;
            form.querySelector('#edit_email').value = email;
            form.querySelector('#edit_role').value = role;
        }

        if(errorDiv) errorDiv.classList.add('hidden'); // Sembunyikan error lama

        editOverlay.classList.remove('hidden');
    }

    function handleTogglePassword(button) {
         const passwordSpan = button.previousElementSibling; // Ambil span di sebelahnya
         const icon = button.querySelector('i');
         if (!passwordSpan || !icon) return;

         if (icon.classList.contains('fa-eye')) {
             passwordSpan.textContent = passwordSpan.dataset.password || ''; // Tampilkan password asli dari data-*
             icon.classList.replace('fa-eye', 'fa-eye-slash');
         } else {
             passwordSpan.textContent = '************'; // Sembunyikan lagi
             icon.classList.replace('fa-eye-slash', 'fa-eye');
         }
    }
});