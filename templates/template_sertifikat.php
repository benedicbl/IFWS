<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page {
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Helvetica', sans-serif;
            text-align: center;
            margin: 0;
            padding: 0;
            color: #333;
            width: 100%;
        }

        /* --- LOGIKA SPACER (PENDORONG) --- */

        /* 1. PENDORONG NAMA */
        .spacer-top {
            height: 55mm; 
            width: 100%;
        }

        /* 2. STYLE NAMA */
        .nama {
            font-size: 28pt; 
            font-weight: bold;
            color: #2c3e50;
            text-transform: uppercase;
            line-height: 1;
            margin: 0; padding: 0; 
        }

        /* 3. PENDORONG ROLE */
        .spacer-role {
            height: 20mm; 
        }

        /* 4. STYLE ROLE */
        .role {
            font-size: 20pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #555;
            letter-spacing: 1px;
            margin: 0; padding: 0;
        }

        /* 5. PENDORONG TOPIK */
        .spacer-topik {
            height: 18mm; 
        }

        /* 6. STYLE TOPIK (PERBAIKAN MARGIN KIRI KANAN) */
        .topik-container {
            /* Lebar A4 = 297mm.
               Dengan padding 45mm kiri + 45mm kanan = 90mm.
               Sisa ruang teks = 207mm (Cukup untuk masuk ke kotak ungu)
            */
            padding-left: 45mm;  /* Sebelumnya 20mm, ditambah agar teks menjorok ke tengah */
            padding-right: 45mm; /* Sebelumnya 20mm */
            
            /* Opsional: Batasi tinggi jika perlu, tapi biarkan auto agar teks panjang ke bawah */
            /* height: 35mm; */ 
            display: block;
        }
        
        .topik {
            /* Font size dan Line Height DINAMIS dari PHP */
            font-size: {FONT_SIZE_TOPIK};
            line-height: {LINE_HEIGHT}; 
            
            font-weight: bold;
            color: #ffffff; 
            margin: 0; padding: 0;
        }

        /* 7. PENDORONG TANGGAL */
        .spacer-tanggal {
            height: 25mm; 
        }

        /* 8. STYLE TANGGAL */
        .tanggal {
            font-size: 11pt;
            color: #6B7AA1;
            font-weight: bold;
            margin: 0; padding: 0;
        }
    </style>
</head>
<body>
    
    <div class="spacer-top"></div>

    <div class="nama">{NAMA_LENGKAP}</div>

    <div class="spacer-role"></div>

    <div class="role">{PERAN}</div>

    <div class="spacer-topik"></div>

    <div class="topik-container">
        <div class="topik">{TOPIK_WEBINAR}</div>
    </div>

    <div class="spacer-tanggal"></div>

    <div class="tanggal">{TANGGAL_WEBINAR}</div>

</body>
</html>