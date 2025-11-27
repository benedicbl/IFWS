document.addEventListener('DOMContentLoaded', function() {
    // ==========================================================
    // BAGIAN 1: PENGATURAN ELEMEN & OVERLAY GLOBAL
    // ==========================================================
    const overlays = {
        tambah: document.getElementById('tambah-overlay'),
        tooltip: document.getElementById('info-tooltip'),
        poster: document.getElementById('poster-overlay'),
        linkPreview: document.getElementById('link-preview-overlay'),
        publishConfirm: document.getElementById('publish-confirm-overlay'),
        reschedule: document.getElementById('reschedule-overlay'),
        selesaiConfirm: document.getElementById('selesai-confirm-overlay')
    };
    let lastClickedTrigger = null; // Untuk tombol info (i)
    const formTambahUpdate = overlays.tambah ? overlays.tambah.querySelector('form') : null;
    const formTitle = overlays.tambah ? overlays.tambah.querySelector('.overlay-main-title') : null;

    // Setup input hidden di form tambah/update
    if (formTambahUpdate) {
        let webinarIdInput = formTambahUpdate.querySelector('input[name="webinar_id"]');
        if (!webinarIdInput) {
            webinarIdInput = document.createElement('input');
            webinarIdInput.type = 'hidden';
            webinarIdInput.name = 'webinar_id';
            formTambahUpdate.appendChild(webinarIdInput);
        }
    }

    // ==========================================================
    // BAGIAN 2: EVENT LISTENER UTAMA (DELEGASI UNTUK SEMUA KLIK)
    // ==========================================================
    document.body.addEventListener('click', function(event) {
        const target = event.target;
        const targetClosest = (selector) => target.closest(selector); // Helper

        // --- Logika Tombol Buka Overlay ---
        if (targetClosest('#btn-tambah')) {
            resetFormToAddMode();
            if (overlays.tambah) overlays.tambah.classList.remove('hidden');
        }
        if (targetClosest('.btn-update')) {
            handleUpdateClick(targetClosest('.btn-update'));
        }
        if (targetClosest('.info-tooltip-trigger')) {
            event.stopPropagation();
            handleInfoTooltip(targetClosest('.info-tooltip-trigger'));
        }
        if (targetClosest('#view-poster-btn')) {
            event.preventDefault();
            handlePosterView();
        }
        if (targetClosest('#view-link-btn')) {
            event.preventDefault();
            handleLinkView();
        }
        if (targetClosest('.btn-publish')) {
            handlePublishConfirm(targetClosest('.btn-publish'));
        }
        if (targetClosest('.btn-reschedule')) {
            handleRescheduleClick(targetClosest('.btn-reschedule'));
        }
        if (targetClosest('.btn-selesai')) {
            handleSelesaiConfirm(targetClosest('.btn-selesai'));
        }

        // --- Logika Tombol Tutup Overlay ---
        if (target.classList.contains('overlay')) {
            target.classList.add('hidden');
        }
        if (targetClosest('.btn-close-overlay')) {
            targetClosest('.overlay').classList.add('hidden');
        }

        // --- Logika Tutup Lainnya ---
        // Sembunyikan tooltip jika klik di luar
        if (overlays.tooltip && !overlays.tooltip.contains(target) && !targetClosest('.info-tooltip-trigger')) {
            overlays.tooltip.classList.add('hidden');
        }
        // Sembunyikan dropdown narasumber jika klik di luar
        const multiSelectContainer = document.querySelector('.multi-select-container');
        if (multiSelectContainer && !multiSelectContainer.contains(target)) {
            const checkboxesContainer = document.getElementById('narasumber-checkboxes');
            if (checkboxesContainer) checkboxesContainer.style.display = 'none';
        }
    });

    // ==========================================================
    // BAGIAN 3: LOGIKA KHUSUS UNTUK DROPDOWN MULTI-SELECT
    // ==========================================================
    const multiSelectContainer = document.querySelector('.multi-select-container');
    if (multiSelectContainer) {
        const selectBox = multiSelectContainer.querySelector('.select-box');
        const checkboxesContainer = multiSelectContainer.querySelector('.checkboxes-container');
        const narasumberCheckboxes = checkboxesContainer.querySelectorAll('input[type="checkbox"]');

        selectBox.addEventListener('click', () => {
            checkboxesContainer.style.display = checkboxesContainer.style.display === 'block' ? 'none' : 'block';
        });

        narasumberCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedNarasumberText);
        });
    }

    // ==========================================================
    // BAGIAN 4: FUNGSI-FUNGSI BANTU
    // ==========================================================

    function handleUpdateClick(button) {
        const webinarId = button.dataset.id;
        fetch(`/projek-ifws/api/get_webinar_details.php?id=${webinarId}`)
            .then(response => response.json())
            .then(data => {
                if (data.error || !data.webinar) { alert(data.error || 'Data webinar tidak ditemukan.'); return; }
                if (!formTambahUpdate) return;
                formTitle.textContent = 'Update Rencana Webinar';
                formTambahUpdate.action = '/projek-ifws/api/update_webinar.php';
                formTambahUpdate.querySelector('input[name="webinar_id"]').value = data.webinar.id;
                formTambahUpdate.querySelector('#tanggal').value = data.webinar.tanggal_direncanakan;
                formTambahUpdate.querySelector('#jam_mulai').value = data.webinar.jam_mulai;
                formTambahUpdate.querySelector('#jam_selesai').value = data.webinar.jam_selesai;
                formTambahUpdate.querySelector('#kategori').value = data.webinar.kategori;
                formTambahUpdate.querySelector('#topik').value = data.webinar.topik;
                formTambahUpdate.querySelectorAll('input[name="narasumber_ids[]"]').forEach(checkbox => {
                    checkbox.checked = data.narasumber_ids.includes(parseInt(checkbox.value));
                });
                updateSelectedNarasumberText();
                overlays.tambah.classList.remove('hidden');
            });
    }

    function handleInfoTooltip(trigger) {
        lastClickedTrigger = trigger;
        updateTooltipContent(trigger.dataset.posterStatus, trigger.dataset.linkStatus);
        positionTooltip(trigger);
        if (overlays.tooltip) overlays.tooltip.classList.remove('hidden');
    }

    function handlePosterView() {
        if (lastClickedTrigger) {
            const posterPath = lastClickedTrigger.dataset.posterPath;
            if (posterPath && overlays.poster) {
                overlays.poster.querySelector('#poster-preview-img').src = '/projek-ifws/' + posterPath;
                overlays.poster.classList.remove('hidden');
            }
        }
    }

    function handleLinkView() {
        if (lastClickedTrigger) {
            const linkUrl = lastClickedTrigger.dataset.linkUrl;
            if (linkUrl && overlays.linkPreview) {
                overlays.linkPreview.querySelector('#link-preview-text').value = linkUrl;
                overlays.linkPreview.classList.remove('hidden');
            }
        }
    }

    function handlePublishConfirm(button) {
        const webinarId = button.dataset.id;
        const topik = button.dataset.topik;
        if (overlays.publishConfirm) {
            overlays.publishConfirm.querySelector('#publish_webinar_id').value = webinarId;
            overlays.publishConfirm.querySelector('#publish-confirm-text').textContent = `Apakah Anda yakin ingin mem-publish webinar "${topik}"? Data akan muncul di list peserta.`;
            overlays.publishConfirm.classList.remove('hidden');
        }
    }

    function handleRescheduleClick(button) {
        const webinarId = button.dataset.id;
        const tanggal = button.dataset.tanggal;
        const mulai = button.dataset.mulai;
        const selesai = button.dataset.selesai;
        if (overlays.reschedule) {
            overlays.reschedule.querySelector('#reschedule_webinar_id').value = webinarId;
            overlays.reschedule.querySelector('#reschedule_tanggal').value = tanggal;
            overlays.reschedule.querySelector('#reschedule_jam_mulai').value = mulai;
            overlays.reschedule.querySelector('#reschedule_jam_selesai').value = selesai;
            overlays.reschedule.classList.remove('hidden');
        }
    }

    function handleSelesaiConfirm(button) {
        const webinarId = button.dataset.id;
        const topik = button.dataset.topik;
         if (overlays.selesaiConfirm) {
            overlays.selesaiConfirm.querySelector('#selesai_webinar_id').value = webinarId;
            overlays.selesaiConfirm.querySelector('#selesai-confirm-text').textContent = `Apakah Anda yakin ingin menyelesaikan webinar "${topik}"? Status tidak dapat diubah kembali.`;
            overlays.selesaiConfirm.classList.remove('hidden');
        }
    }

    function resetFormToAddMode() {
        if (!formTambahUpdate) return;
        formTambahUpdate.reset();
        formTambahUpdate.action = '/projek-ifws/api/tambah_webinar.php';
        if (formTitle) formTitle.textContent = 'Tambah Rencana Webinar';
        const webinarIdInput = formTambahUpdate.querySelector('input[name="webinar_id"]');
        if (webinarIdInput) webinarIdInput.value = '';
        formTambahUpdate.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
        updateSelectedNarasumberText();
    }

    function updateSelectedNarasumberText() {
        const selectBoxText = document.querySelector('.select-box-text');
        if (!selectBoxText) return;
        const selected = Array.from(document.querySelectorAll('#narasumber-checkboxes input[type="checkbox"]:checked')).map(cb => cb.dataset.name);
        if (selected.length > 0) {
            selectBoxText.textContent = selected.join(', ');
            selectBoxText.classList.add('selected');
        } else {
            selectBoxText.textContent = 'Pilih Narasumber...';
            selectBoxText.classList.remove('selected');
        }
    }

    function updateTooltipContent(poster, link) {
        if (!overlays.tooltip) return;
        const posterViewBtn = overlays.tooltip.querySelector('#view-poster-btn');
        const linkViewBtn = overlays.tooltip.querySelector('#view-link-btn');
        const posterCheckIcon = overlays.tooltip.querySelector('#poster-check i');
        const posterStatusSpan = overlays.tooltip.querySelector('#poster-status');
        if (poster === 'ada') { posterCheckIcon.className = 'fas fa-check-circle icon-success'; posterStatusSpan.textContent = 'Sudah Ada'; posterViewBtn.classList.remove('hidden'); }
        else { posterCheckIcon.className = 'fas fa-exclamation-triangle icon-warning'; posterStatusSpan.textContent = 'Belum Ada'; posterViewBtn.classList.add('hidden'); }
        const linkCheckIcon = overlays.tooltip.querySelector('#link-check i');
        const linkStatusSpan = overlays.tooltip.querySelector('#link-status');
        if (link === 'ada') { linkCheckIcon.className = 'fas fa-check-circle icon-success'; linkStatusSpan.textContent = 'Sudah Ada'; linkViewBtn.classList.remove('hidden'); }
        else { linkCheckIcon.className = 'fas fa-exclamation-triangle icon-warning'; linkStatusSpan.textContent = 'Belum Ada'; linkViewBtn.classList.add('hidden'); }
    }

    function positionTooltip(triggerElement) {
        if (!overlays.tooltip) return;
        const rect = triggerElement.getBoundingClientRect();
        overlays.tooltip.style.top = `${window.scrollY + rect.top - overlays.tooltip.offsetHeight - 10}px`;
        overlays.tooltip.style.left = `${window.scrollX + rect.left - overlays.tooltip.offsetWidth + (rect.width / 2)}px`;
    }
});