document.addEventListener('DOMContentLoaded', () => {
    const uploadTa1Btn = document.getElementById('upload-ta1-btn');
    const uploadTa2Btn = document.getElementById('upload-ta2-btn');
    const csvInput = document.getElementById('csv-input');
    const tableBody = document.getElementById('upload-preview-body');
    const emptyState = document.getElementById('preview-empty-state');
    const addDataBtn = document.getElementById('add-data-btn');

    let currentUploadType = '';

    uploadTa1Btn.addEventListener('click', () => {
        currentUploadType = 'TA 1';
        csvInput.click();
    });
    uploadTa2Btn.addEventListener('click', () => {
        currentUploadType = 'TA 2';
        csvInput.click();
    });

    csvInput.addEventListener('change', (event) => {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const csvContent = e.target.result;
                parseAndDisplayCSV(csvContent, currentUploadType);
            };
            reader.readAsText(file);
        }
        csvInput.value = ''; 
    });

    function parseAndDisplayCSV(csvText, taType) {
        // --- DIUBAH: 'existingNrps' menjadi 'existingNpms' ---
        const existingNpms = new Set();
        tableBody.querySelectorAll('tr').forEach(row => {
            const npmCell = row.cells[1]; // Kolom NPM
            if (npmCell) {
                existingNpms.add(npmCell.textContent.trim());
            }
        });

        const lines = csvText.split('\n');
        // --- DIUBAH: mencari 'npm' di header ---
        const headerIndex = lines.findIndex(line => line.toLowerCase().includes("npm") && line.toLowerCase().includes("nama"));

        if (headerIndex === -1) {
            alert("Format CSV tidak valid. Pastikan ada kolom 'NPM' dan 'Nama'.");
            return;
        }

        const dataRows = lines.slice(headerIndex + 1);
        let hasData = tableBody.children.length > 0;

        dataRows.forEach(row => {
            let cleanRow = row.trim();
            if (cleanRow === '') return;
            
            if (cleanRow.startsWith('"') && cleanRow.endsWith('"')) {
                cleanRow = cleanRow.slice(1, -1);
            }
            
            const cols = cleanRow.split(',');

            if (cols.length >= 3) {
                // --- DIUBAH: variabel 'nrp' menjadi 'npm' ---
                const npm = cols[1] ? cols[1].trim() : '';
                const nama = cols[2] ? cols[2].trim() : '';

                // --- DIUBAH: Pengecekan duplikasi menggunakan 'npm' ---
                if (npm && !existingNpms.has(npm)) {
                    hasData = true;
                    const newRow = document.createElement('tr');
                    newRow.innerHTML = `
                        <td contenteditable="true">${nama}</td>
                        <td contenteditable="true">${npm}</td>
                        <td>${taType}</td>
                    `;
                    tableBody.appendChild(newRow);
                    existingNpms.add(npm);
                }
            }
        });
        
        emptyState.style.display = hasData ? 'none' : 'block';
    }

    addDataBtn.addEventListener('click', () => {
        if (tableBody.children.length === 0) {
            alert('Tidak ada data untuk ditambahkan.');
            return;
        }
        
        alert('Data peserta TA berhasil ditambahkan ke rekap!');
        window.location.href = 'peserta_ta.html';
    });
});