document.addEventListener('DOMContentLoaded', function() {

    // === Logika untuk Tooltip/Popover Info ===
    const tooltip = document.getElementById('info-tooltip');
    const infoTriggers = document.querySelectorAll('.info-tooltip-trigger');

    if (tooltip && infoTriggers.length > 0) {
        
        infoTriggers.forEach(trigger => {
            trigger.addEventListener('click', function(event) {
                event.stopPropagation(); // Mencegah klik menyebar ke document

                // Dapatkan posisi tombol info yang diklik
                const rect = trigger.getBoundingClientRect();
                
                // Posisikan tooltip di sebelah kiri tombol
                tooltip.style.left = (rect.left - tooltip.offsetWidth - 10) + 'px';
                tooltip.style.top = (window.scrollY + rect.top + (rect.height / 2) - (tooltip.offsetHeight / 2)) + 'px';

                // Tampilkan atau sembunyikan tooltip
                tooltip.classList.toggle('hidden');
            });
        });

        // Sembunyikan tooltip jika klik di luar
        document.addEventListener('click', function() {
            if (!tooltip.classList.contains('hidden')) {
                tooltip.classList.add('hidden');
            }
        });

        // Jangan sembunyikan jika klik di dalam tooltip itu sendiri
        tooltip.addEventListener('click', function(event) {
            event.stopPropagation();
        });
    }
});