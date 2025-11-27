document.addEventListener('DOMContentLoaded', function() {
    // === Elemen Global ===
    const btnGenerate = document.getElementById('btn-generate');
    const formGenerate = document.getElementById('form-generate');
    const alertBox = document.getElementById('upload-alert');
    
    const uploadOverlay = document.getElementById('upload-template-overlay');
    const formUpload = document.getElementById('form-upload-template');
    const btnSubmitTemplate = document.getElementById('btn-submit-template');
    
    const overrideOverlay = document.getElementById('override-confirm-overlay');
    const formOverride = document.getElementById('form-override');
    const btnConfirmOverride = document.getElementById('btn-confirm-override');

    // ===================================
    // === LOGIKA GENERATE SERTIFIKAT ===
    // ===================================
    if (btnGenerate && formGenerate) {
        btnGenerate.addEventListener('click', function(event) {
            event.preventDefault(); 
            if (confirm('Anda yakin ingin men-generate semua sertifikat untuk webinar ini? Proses ini mungkin memakan waktu.')) {
                btnGenerate.classList.add('is-loading', 'btn-disabled');
                btnGenerate.disabled = true;
                const formData = new FormData(formGenerate);
                
                fetch(formGenerate.action, { method: 'POST', body: formData })
                    .then(response => {
                        if (response.ok && response.headers.get("Content-Type")?.includes("application/json")) { return response.json(); }
                        return response.text().then(text => { throw new Error("Respon server tidak valid: " + text); });
                    })
                    .then(data => {
                        if (data.status === 'success') {
                            alert(data.message);
                            window.location.reload(); 
                        } else {
                            alert('Error: ' + data.message);
                            btnGenerate.classList.remove('is-loading', 'btn-disabled');
                            btnGenerate.disabled = false;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        const errorMessage = error.message.includes('Unexpected token') ? "Error: Respon server tidak valid (bukan JSON)." : error.message;
                        alert('Terjadi kesalahan fatal.\n\nDetail:\n' + errorMessage);
                        btnGenerate.classList.remove('is-loading', 'btn-disabled');
                        btnGenerate.disabled = false;
                    });
            }
        });
    }

    // ===================================
    // === LOGIKA UPLOAD TEMPLATE ===
    // ===================================
    document.querySelectorAll('.btn-open-upload').forEach(button => {
        button.addEventListener('click', function() {
            if (uploadOverlay) {
                // Selalu set ke 'template' karena kita hanya punya satu
                uploadOverlay.querySelector('#upload-template-title').textContent = 'Upload Template Sertifikat';
                uploadOverlay.querySelector('#template_type_input').value = 'template';
                if (formUpload) formUpload.reset(); 
                uploadOverlay.classList.remove('hidden');
            }
        });
    });

    if (formUpload && btnSubmitTemplate) {
        formUpload.addEventListener('submit', function(event) {
            event.preventDefault(); 
            btnSubmitTemplate.textContent = 'Mengupload...';
            btnSubmitTemplate.disabled = true;
            const formData = new FormData(formUpload);
            
            fetch('/projek-ifws/api/upload_template.php', { method: 'POST', body: formData })
                .then(response => response.json())
                .then(data => {
                    if (alertBox) {
                        alertBox.textContent = data.message;
                        alertBox.classList.remove('hidden', 'alert-success', 'alert-danger');
                        alertBox.classList.add(data.status === 'success' ? 'alert-success' : 'alert-danger');
                    } else {
                        alert(data.message);
                    }
                    if (data.status === 'success') {
                        window.location.reload(); 
                    } else {
                         btnSubmitTemplate.textContent = 'Simpan Template';
                         btnSubmitTemplate.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan. Cek console.');
                    btnSubmitTemplate.textContent = 'Simpan Template';
                    btnSubmitTemplate.disabled = false;
                });
        });
    }
    
    // ===================================
    // === LOGIKA OVERRIDE SERTIFIKAT ===
    // ===================================
    document.querySelectorAll('.btn-buka-override').forEach(button => {
        button.addEventListener('click', function() {
            const idKehadiran = this.dataset.id;
            const nama = this.dataset.nama;
            if (overrideOverlay) {
                overrideOverlay.querySelector('#override_kehadiran_id').value = idKehadiran;
                overrideOverlay.querySelector('#override-confirm-text').textContent = `Izinkan sertifikat untuk "${nama}" (status: Tidak Valid)?`;
                overrideOverlay.classList.remove('hidden');
            }
        });
    });
    
    if(formOverride && btnConfirmOverride) {
        formOverride.addEventListener('submit', function(event) {
            event.preventDefault();
            btnConfirmOverride.disabled = true;
            btnConfirmOverride.textContent = "Memproses...";
            
            const formData = new FormData(formOverride);
            fetch(formOverride.action, { method: 'POST', body: formData })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert(data.message);
                        window.location.reload();
                    } else {
                        alert('Gagal: ' + data.message);
                        btnConfirmOverride.disabled = false;
                        btnConfirmOverride.textContent = "Ya, Izinkan";
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan.');
                    btnConfirmOverride.disabled = false;
                    btnConfirmOverride.textContent = "Ya, Izinkan";
                });
        });
    }

    // ===================================
    // === LOGIKA TUTUP OVERLAY GLOBAL ===
    // ===================================
    document.querySelectorAll('.overlay').forEach(overlay => {
        overlay.addEventListener('click', function(event) {
            if (event.target === overlay || event.target.closest('.btn-close-overlay')) {
                overlay.classList.add('hidden');
            }
        });
    });
});