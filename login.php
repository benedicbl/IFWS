<?php
session_start();

// Redirect jika sudah login
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    $role = $_SESSION['role'];
    $path = '';
    switch ($role) {
        case 'admin': $path = '/projek-ifws/PIC/admin.php'; break;
        case 'teknisi': $path = '/projek-ifws/Teknisi/teknisi.php'; break;
        case 'promosi': $path = '/projek-ifws/Promosi/promosi.php'; break;
        case 'sekretaris': $path = '/projek-ifws/Sekretaris/sekretaris.php'; break;
        case 'bendahara': $path = '/projek-ifws/Bendahara/bendahara.php'; break;
    }
    if ($path) { header("Location: " . $path); exit(); }
} elseif (isset($_SESSION['peserta_id'])) {
    header("Location: /projek-ifws/Peserta/dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Informatics Webinar Series</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Poppins', sans-serif; /* Menggunakan Poppins */
            background-color: #f3f4f6;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            width: 1000px;
            height: 700px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            overflow: hidden;
            display: flex;
        }

        .left-panel {
            width: 50%;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        /* --- LOGO AREA (POPPINS STYLE) --- */
        .logo-area { 
            display: flex;          
            align-items: center;    
            gap: 12px;              
            margin-bottom: 40px; 
        }
        
        .logo-area img { 
            width: 45px;            
            height: auto; 
            display: block;
        }

        .brand-text {
            display: flex;
            flex-direction: column; 
            line-height: 1.2;
            color: #1e3a8a;       /* Warna Biru Tua */
        }

        .brand-text span.bold {
            font-weight: 600;     /* Semi-Bold untuk "Informatics" */
            font-size: 16px;
        }

        .brand-text span.normal {
            font-weight: 400;     /* Regular untuk "Webinar Series" */
            font-size: 16px;
        }
        /* --------------------------------- */

        h2 { font-size: 24px; font-weight: 600; color: #111; margin-bottom: 8px; }
        .subtitle { font-size: 14px; color: #6b7280; margin-bottom: 30px; font-weight: 400; }

        .input-group { position: relative; margin-bottom: 20px; }
        .input-group input {
            width: 100%;
            padding: 12px 12px 12px 40px;
            border: 1px solid #d1d5db;
            border-radius: 8px; /* Sedikit lebih bulat agar sesuai style Poppins */
            outline: none;
            font-size: 14px;
            font-family: 'Poppins', sans-serif;
            transition: border-color 0.3s;
        }
        .input-group input:focus { border-color: #2563eb; }
        
        .input-group i.icon-start {
            position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af;
        }
        .input-group i.icon-toggle {
            position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af; cursor: pointer;
        }

        .options {
            display: flex; justify-content: space-between; align-items: center; font-size: 13px; margin-bottom: 25px;
        }
        .remember-me { display: flex; align-items: center; gap: 8px; color: #6b7280; cursor: pointer;}
        .forgot-pass { color: #2563eb; text-decoration: none; font-weight: 500; }

        .btn-login {
            width: 100%; padding: 12px; background-color: #2563eb; color: white; border: none; border-radius: 8px; font-weight: 500; cursor: pointer; font-size: 14px; transition: background 0.3s; font-family: 'Poppins', sans-serif;
        }
        .btn-login:hover { background-color: #1d4ed8; }

        .divider { display: flex; align-items: center; margin: 25px 0; font-size: 12px; color: #9ca3af; }
        .divider::before, .divider::after { content: ""; flex: 1; height: 1px; background: #e5e7eb; }
        .divider span { padding: 0 10px; font-weight: 500; }

        .btn-google {
            width: 100%; padding: 10px; background: white; border: 1px solid #d1d5db; border-radius: 8px; display: flex; justify-content: center; align-items: center; gap: 10px; cursor: pointer; font-size: 14px; font-weight: 500; color: #374151; transition: background 0.2s; font-family: 'Poppins', sans-serif;
        }
        .btn-google:hover { background-color: #f9fafb; }
        .btn-google img { width: 18px; }

        .footer-text { margin-top: 30px; text-align: center; font-size: 13px; color: #6b7280; }
        .footer-text a { color: #2563eb; text-decoration: none; font-weight: 600; }

        .right-panel {
            width: 50%;
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
        }
        
        .error-msg { background-color: #fee2e2; color: #b91c1c; padding: 10px; border-radius: 8px; font-size: 13px; margin-bottom: 20px; text-align: center; }

        @media (max-width: 768px) {
            .container { flex-direction: column; width: 90%; height: auto; }
            .right-panel { display: none; }
            .left-panel { width: 100%; padding: 30px; }
        }

        /* --- Tambahkan kode ini di bagian paling bawah dalam tag <style> --- */

/* Menghilangkan icon mata bawaan dari Microsoft Edge / IE */
input::-ms-reveal,
input::-ms-clear {
    display: none;
}

/* Menghilangkan icon mata bawaan (jika ada) di browser lain */
input::-webkit-contacts-auto-fill-button,
input::-webkit-credentials-auto-fill-button {
    visibility: hidden;
    display: none !important;
    pointer-events: none;
    height: 0;
    width: 0;
    margin: 0;
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

            <h2>Masuk ke akun anda</h2>
            <p class="subtitle">Silahkan login untuk mengakses halaman</p>

            <?php if (isset($_GET['error'])): ?>
                <div class="error-msg">Email atau password salah!</div>
            <?php endif; ?>

            <form action="proses_login.php" method="POST">
                <div class="input-group">
                    <i class="fa-regular fa-envelope icon-start"></i>
                    <input type="email" name="email" placeholder="Email" required>
                </div>

                <div class="input-group">
                    <i class="fa-solid fa-lock icon-start"></i>
                    <input type="password" name="password" id="passwordInput" placeholder="Password" required>
                    <i class="fa-regular fa-eye-slash icon-toggle" onclick="togglePassword()"></i>
                </div>

                <div class="options">
                    <label class="remember-me">
                        <input type="checkbox" name="remember"> Ingatkan saya
                    </label>
                    <a href="#" class="forgot-pass">Lupa Password?</a>
                </div>

                <button type="submit" class="btn-login">Login</button>
            </form>

            <div class="divider">
                <span>atau lanjutkan dengan</span>
            </div>

            <button type="button" class="btn-google">
                <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google">
                Google
            </button>

            <div class="footer-text">
                Belum memiliki akun? <a href="/projek-ifws/register.php">Buat Akun</a>
            </div>
        </div>

        <div class="right-panel"></div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('passwordInput');
            const icon = document.querySelector('.icon-toggle');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        }
    </script>
</body>
</html>