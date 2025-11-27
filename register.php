<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Akun - Informatics Webinar Series</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f3f4f6;
            min-height: 100vh; /* Menggunakan min-height agar aman di layar kecil */
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px; /* Padding tambahan untuk responsif */
        }

        .container {
            width: 1000px;
            /* Tinggi dibuat auto dengan min-height agar bisa memanjang sesuai isi form */
            min-height: 750px; 
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            overflow: hidden;
            display: flex;
        }

        .left-panel {
            width: 50%;
            padding: 40px 50px; /* Padding sedikit diperkecil agar muat */
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        /* --- LOGO AREA --- */
        .logo-area { 
            display: flex; align-items: center; gap: 12px; margin-bottom: 25px; 
        }
        .logo-area img { width: 40px; height: auto; display: block; }
        .brand-text { display: flex; flex-direction: column; line-height: 1.2; color: #1e3a8a; }
        .brand-text span.bold { font-weight: 600; font-size: 16px; }
        .brand-text span.normal { font-weight: 400; font-size: 16px; }

        h2 { font-size: 24px; font-weight: 600; color: #111; margin-bottom: 5px; }
        .subtitle { font-size: 14px; color: #6b7280; margin-bottom: 25px; }

        /* Input Styles */
        .input-group { position: relative; margin-bottom: 15px; }
        .input-group input {
            width: 100%;
            padding: 12px 12px 12px 40px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            outline: none;
            font-size: 14px;
            font-family: 'Poppins', sans-serif;
            transition: border-color 0.3s;
        }
        .input-group input:focus { border-color: #2563eb; }
        
        .input-group i.icon-start {
            position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 14px;
        }
        .input-group i.icon-toggle {
            position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af; cursor: pointer; font-size: 14px;
        }

        /* Buttons */
        .btn-register {
            width: 100%; padding: 12px; background-color: #2563eb; color: white; border: none; border-radius: 8px; font-weight: 500; cursor: pointer; font-size: 14px; transition: background 0.3s; font-family: 'Poppins', sans-serif; margin-top: 10px;
        }
        .btn-register:hover { background-color: #1d4ed8; }

        .divider { display: flex; align-items: center; margin: 20px 0; font-size: 12px; color: #9ca3af; }
        .divider::before, .divider::after { content: ""; flex: 1; height: 1px; background: #e5e7eb; }
        .divider span { padding: 0 10px; font-weight: 500; }

        .btn-google {
            width: 100%; padding: 10px; background: white; border: 1px solid #d1d5db; border-radius: 8px; display: flex; justify-content: center; align-items: center; gap: 10px; cursor: pointer; font-size: 14px; font-weight: 500; color: #374151; transition: background 0.2s; font-family: 'Poppins', sans-serif;
        }
        .btn-google:hover { background-color: #f9fafb; }
        .btn-google img { width: 18px; }

        .footer-text { margin-top: 25px; text-align: center; font-size: 13px; color: #6b7280; }
        .footer-text a { color: #2563eb; text-decoration: none; font-weight: 600; }
        .footer-text a:hover { text-decoration: underline; }

        /* Tombol Kembali (Link sederhana di bawah) */
        .back-link {
            display: block; text-align: center; margin-top: 15px; font-size: 13px; color: #6b7280; text-decoration: none;
        }
        .back-link i { margin-right: 5px; }
        .back-link:hover { color: #111; }

        .right-panel {
            width: 50%;
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
        }
        
        /* Alert Messages */
        .alert { padding: 10px; border-radius: 8px; font-size: 13px; margin-bottom: 20px; text-align: center; }
        .alert-error { background-color: #fee2e2; color: #b91c1c; }
        .alert-success { background-color: #dcfce7; color: #166534; }

        @media (max-width: 768px) {
            .container { flex-direction: column; width: 100%; height: auto; }
            .right-panel { display: none; }
            .left-panel { width: 100%; padding: 30px; }
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="left-panel">
            
            <div class="logo-area">
                <img src="/projek-ifws/assets/picture/logo.png" alt="Logo IFWS">
                <div class="brand-text">
                    <span class="bold">Informatics</span>
                    <span class="normal">Webinar Series</span>
                </div>
            </div>

            <h2>Buat Akun</h2>
            <p class="subtitle">Masukkan data yang diperlukan untuk membuat akun</p>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>

            <form action="proses_register.php" method="POST">
                
                <div class="input-group">
                    <i class="fa-regular fa-user icon-start"></i>
                    <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" required>
                </div>

                <div class="input-group">
                    <i class="fa-solid fa-id-card icon-start"></i>
                    <input type="text" name="npm" placeholder="NPM (Nomor Pokok Mahasiswa)" required>
                </div>

                <div class="input-group">
                    <i class="fa-regular fa-envelope icon-start"></i>
                    <input type="email" name="email" placeholder="Email" required>
                </div>

                <div class="input-group">
                    <i class="fa-solid fa-lock icon-start"></i>
                    <input type="password" name="password" id="passInput" placeholder="Password" required>
                    <i class="fa-regular fa-eye-slash icon-toggle" onclick="togglePass('passInput', this)"></i>
                </div>

                <div class="input-group">
                    <i class="fa-solid fa-lock icon-start"></i>
                    <input type="password" name="confirm_password" id="confPassInput" placeholder="Konfirmasi Password" required>
                    <i class="fa-regular fa-eye-slash icon-toggle" onclick="togglePass('confPassInput', this)"></i>
                </div>

                <button type="submit" class="btn-register">Buat Akun</button>
            </form>

            <div class="divider">
                <span>atau lanjutkan dengan</span>
            </div>

            <button type="button" class="btn-google">
                <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google">
                Google
            </button>

            <div class="footer-text">
                Sudah memiliki akun? <a href="login.php">Login</a>
            </div>
    
        </div>

        <div class="right-panel"></div>
    </div>

    <script>
        // Fungsi Toggle Password yang fleksibel untuk 2 input berbeda
        function togglePass(inputId, iconElement) {
            const input = document.getElementById(inputId);
            if (input.type === 'password') {
                input.type = 'text';
                iconElement.classList.remove('fa-eye-slash');
                iconElement.classList.add('fa-eye');
            } else {
                input.type = 'password';
                iconElement.classList.remove('fa-eye');
                iconElement.classList.add('fa-eye-slash');
            }
        }
    </script>
</body>
</html>