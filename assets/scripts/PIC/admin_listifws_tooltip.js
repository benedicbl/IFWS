document.addEventListener('DOMContentLoaded', function() {
    const tooltip = document.getElementById('info-tooltip');
    const triggers = document.querySelectorAll('.info-tooltip-trigger');

    if (!tooltip) return;

    // Elemen di dalam tooltip
    const posterCheckIcon = tooltip.querySelector('#poster-check i');
    const posterStatusSpan = tooltip.querySelector('#poster-status');
    const linkCheckIcon = tooltip.querySelector('#link-check i');
    const linkStatusSpan = tooltip.querySelector('#link-status');

    triggers.forEach(trigger => {
        trigger.addEventListener('click', function(event) {
            event.stopPropagation(); // Mencegah event click menyebar ke body

            // Ambil status dari data attribute tombol yang diklik
            const posterStatus = this.dataset.posterStatus;
            const linkStatus = this.dataset.linkStatus;

            // Update konten tooltip berdasarkan status
            updateTooltipContent(posterStatus, linkStatus);

            // Posisikan tooltip di sebelah tombol
            positionTooltip(this);

            // Tampilkan tooltip
            tooltip.classList.remove('hidden');
        });
    });

    function updateTooltipContent(poster, link) {
        // Update status Poster
        if (poster === 'ada') {
            posterCheckIcon.className = 'fas fa-check-circle icon-success';
            posterStatusSpan.textContent = 'Sudah Ada';
        } else {
            posterCheckIcon.className = 'fas fa-exclamation-triangle icon-warning';
            posterStatusSpan.textContent = 'Belum Ada';
        }

        // Update status Link Akses
        if (link === 'ada') {
            linkCheckIcon.className = 'fas fa-check-circle icon-success';
            linkStatusSpan.textContent = 'Sudah Ada';
        } else {
            linkCheckIcon.className = 'fas fa-exclamation-triangle icon-warning';
            linkStatusSpan.textContent = 'Belum Ada';
        }
    }

    function positionTooltip(triggerElement) {
        const rect = triggerElement.getBoundingClientRect();
        // Posisikan tooltip di atas tombol, sedikit ke kiri
        tooltip.style.top = `${window.scrollY + rect.top - tooltip.offsetHeight - 10}px`;
        tooltip.style.left = `${window.scrollX + rect.left - tooltip.offsetWidth + (rect.width / 2)}px`;
    }

    // Sembunyikan tooltip jika user mengklik di mana saja di luar tooltip
    document.addEventListener('click', function(event) {
        if (!tooltip.contains(event.target) && !event.target.closest('.info-tooltip-trigger')) {
            tooltip.classList.add('hidden');
        }
    });
});