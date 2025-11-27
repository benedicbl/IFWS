<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sertifikat</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; margin: 0; padding: 0; text-align: center; }
        
        .sertifikat-container {
            width: 29.7cm; height: 21cm;
            position: relative;
            background: url('{BACKGROUND_PLACEHOLDER}') no-repeat center center;
            background-size: 100% 100%;
        }

        /* 1. NAMA (Di bawah "diberikan kepada") */
        .nama {
            font-weight:bold;
            font-size: 36pt
            padding: 
        }
        
        /* 2. ROLE (Di bawah "atas partisipasinya sebagai") */
        .sebagai {
            position: absolute;
            text-align: center;   /* biar tetap rata tengah */
            margin-top: 200px;     /* ubah angka ini untuk geser ke bawah */
            font-size: 24pt;      /* opsional */
            font-weight: bold;
        }
        /* 3. TOPIK (Di dalam kotak ungu) */
        .webinar {
            position: absolute;
            text-align: center;   /* biar tetap rata tengah */
            margin-top: 50px;     /* ubah angka ini untuk geser ke bawah */
            font-size: 20pt;      /* opsional */
            font-weight:bold;
            color: #000
        }
        
        /* 4. TANGGAL (Di posisi tanggal statis) */
        .tanggal {
            position: absolute;
            text-align: center;   /* biar tetap rata tengah */
            margin-top: 100px;     /* ubah angka ini untuk geser ke bawah */
            font-size: 14pt;      /* opsional */
        }
    </style>
</head>
<body>
    <div class="sertifikat-container">
        <div class="nama">{NAMA_LENGKAP}</div>
        <div class="sebagai">{PERAN}</div>
        <div class="webinar">{TOPIK_WEBINAR}</div>
        <div class="tanggal">{TANGGAL_WEBINAR}</div>
    </div>
</body>
</html>