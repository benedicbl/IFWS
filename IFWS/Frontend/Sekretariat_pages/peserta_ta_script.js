document.addEventListener('DOMContentLoaded', () => {
    const MINIMUM_IFWS_VALID = 5;

    // --- DIUBAH: 'nrp' menjadi 'npm' ---
    const pesertaTaData = {
        '2025-Ganjil': [
            { nama: 'James Carter', npm: '6182101032', jenisTa: 'TA 2', totalIfws: 8 },
            { nama: 'Sophia Anderson', npm: '6182101057', jenisTa: 'TA 2', totalIfws: 7 },
            { nama: 'Ethan Mitchell', npm: '6182101098', jenisTa: 'TA 1', totalIfws: 4 },
        ],
        '2024-Genap': [
            { nama: 'Olivia Roberts', npm: '6182101024', jenisTa: 'TA 2', totalIfws: 3 },
            { nama: 'William Scott', npm: '6182101050', jenisTa: 'TA 2', totalIfws: 9 },
            { nama: 'Ava Thompson', npm: '6182001002', jenisTa: 'TA 1', totalIfws: 6 },
            { nama: 'Noah Harris', npm: '6182001014', jenisTa: 'TA 1', totalIfws: 2 },
        ]
    };

    let currentDisplayedData = [];

    // --- DOM Elements ---
    const pilihTahunBtn = document.getElementById('pilihTahunBtn');
    const tahunDropdown = document.getElementById('tahunDropdown');
    const dataSection = document.getElementById('data-section');
    const tableBody = document.getElementById('peserta-ta-table-body');
    const summaryInfo = document.getElementById('summary-info');
    const emptyStateMsg = document.getElementById('empty-state-message');
    const downloadCsvBtn = document.getElementById('download-csv-btn');

    // --- Dropdown Logic ---
    pilihTahunBtn.addEventListener('click', () => {
        tahunDropdown.classList.toggle('show');
    });
    window.addEventListener('click', (e) => {
        if (!e.target.matches('.dropdown-btn')) {
            if (tahunDropdown.classList.contains('show')) {
                tahunDropdown.classList.remove('show');
            }
        }
    });
    tahunDropdown.addEventListener('click', (e) => {
        e.preventDefault();
        const selectedTahun = e.target.getAttribute('data-tahun');
        if (selectedTahun) {
            displayData(selectedTahun);
            pilihTahunBtn.innerHTML = `${selectedTahun} <i class="fa-solid fa-chevron-down"></i>`;
            tahunDropdown.classList.remove('show');
        }
    });

    function displayData(tahun) {
        const data = pesertaTaData[tahun] || [];
        currentDisplayedData = data.map(p => ({ ...p, isValid: p.totalIfws >= MINIMUM_IFWS_VALID }));
        
        tableBody.innerHTML = '';
        if (currentDisplayedData.length > 0) {
            currentDisplayedData.forEach(p => {
                const row = document.createElement('tr');
                const statusClass = p.isValid ? 'status-valid' : 'status-invalid';
                const statusText = p.isValid ? 'Valid' : 'Tidak Valid';

                // --- DIUBAH: p.nrp menjadi p.npm ---
                row.innerHTML = `
                    <td>${p.nama}</td>
                    <td>${p.npm}</td>
                    <td>${p.jenisTa}</td>
                    <td>${p.totalIfws}</td>
                    <td><span class="status ${statusClass}">${statusText}</span></td>
                `;
                tableBody.appendChild(row);
            });
            updateSummary(currentDisplayedData);
            dataSection.classList.remove('hidden');
            emptyStateMsg.classList.add('hidden');
        } else {
            dataSection.classList.add('hidden');
            emptyStateMsg.classList.remove('hidden');
            summaryInfo.innerHTML = '';
            currentDisplayedData = [];
        }
    }

    function updateSummary(data) {
        const totalPeserta = data.length;
        const totalValid = data.filter(p => p.isValid).length;
        summaryInfo.innerHTML = `
            <p>Total Peserta: ${totalPeserta}</p>
            <p>Total Peserta Valid: ${totalValid}</p>
        `;
    }

    downloadCsvBtn.addEventListener('click', () => {
        if (currentDisplayedData.length === 0) {
            alert('Tidak ada data untuk di-download.');
            return;
        }
        downloadCSV(currentDisplayedData);
    });

    function downloadCSV(data) {
        // --- DIUBAH: 'NRP' menjadi 'NPM' ---
        const headers = ['Nama', 'NPM', 'Jenis TA', 'Total IFWS', 'Status Valid'];
        const csvRows = [headers.join(',')];

        data.forEach(row => {
            // --- DIUBAH: row.nrp menjadi row.npm ---
            const values = [
                `"${row.nama}"`,
                row.npm,
                row.jenisTa,
                row.totalIfws,
                row.isValid ? 'Valid' : 'Tidak Valid'
            ];
            csvRows.push(values.join(','));
        });

        const csvString = csvRows.join('\n');
        const blob = new Blob([csvString], { type: 'text/csv;charset=utf-8;' });
        
        const link = document.createElement('a');
        if (link.download !== undefined) {
            const url = URL.createObjectURL(blob);
            const tahunAkademik = pilihTahunBtn.textContent.split(' ')[0];
            link.setAttribute('href', url);
            link.setAttribute('download', `rekap_peserta_ta_${tahunAkademik}.csv`);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    }
});