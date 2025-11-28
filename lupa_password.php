<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - IFWS</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* CSS Sama seperti Login */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Poppins', sans-serif; background-color: #f3f4f6; height: 100vh; display: flex; justify-content: center; align-items: center; }
        .container { width: 1000px; height: 600px; background: #fff; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); overflow: hidden; display: flex; }
        .left-panel { width: 50%; padding: 50px; display: flex; flex-direction: column; justify-content: center; }
        .logo-area { display: flex; align-items: center; gap: 12px; margin-bottom: 30px; }
        .logo-area img { width: 45px; height: auto; display: block; }
        .brand-text { display: flex; flex-direction: column; line-height: 1.2; color: #1e3a8a; }
        .brand-text span.bold { font-weight: 600; font-size: 16px; }
        .brand-text span.normal { font-weight: 400; font-size: 16px; }
        h2 { font-size: 24px; font-weight: 600; color: #111; margin-bottom: 8px; }
        .subtitle { font-size: 14px; color: #6b7280; margin-bottom: 30px; }
        .input-group { position: relative; margin-bottom: 20px; }
        .input-group input { width: 100%; padding: 12px 12px 12px 40px; border: 1px solid #d1d5db; border-radius: 8px; outline: none; font-size: 14px; font-family: 'Poppins', sans-serif; transition: border-color 0.3s; }
        .input-group input:focus { border-color: #2563eb; }
        .input-group i.icon-start { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af; }
        .btn-login { width: 100%; padding: 12px; background-color: #2e5a9e; color: white; border: none; border-radius: 8px; font-weight: 500; cursor: pointer; font-size: 14px; transition: background 0.3s; font-family: 'Poppins', sans-serif; }
        .btn-login:hover { background-color: #1e40af; }
        .back-link { display: block; text-align: center; margin-top: 20px; font-size: 13px; color: #6b7280; text-decoration: none; }
        .right-panel { width: 50%; background: linear-gradient(135deg, #1e3a8a 0%, #2e5a9e 100%); }
        .alert { padding: 10px; border-radius: 8px; font-size: 13px; margin-bottom: 20px; text-align: center; color: #b91c1c; background-color: #fee2e2; border: 1px solid #fecaca; }
        @media (max-width: 768px) { .container { flex-direction: column; width: 90%; height: auto; } .right-panel { display: none; } .left-panel { width: 100%; padding: 30px; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="left-panel">
            <div class="logo-area">
                <img src="/projek-ifws/assets/picture/logo.png" alt="Logo">
                <div class="brand-text"><span class="bold">Informatics</span><span class="normal">Webinar Series</span></div>
            </div>
            <h2>Lupa Password</h2>
            <p class="subtitle">Silahkan masukkan email anda untuk mengatur ulang kata sandi!</p>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert"><?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>

            <form action="api/send_reset_link.php" method="POST">
                <div class="input-group">
                    <i class="fa-regular fa-envelope icon-start"></i>
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <button type="submit" class="btn-login">Atur Ulang</button>
            </form>
            
            <a href="login.php" class="back-link">Kembali ke Login? <strong>Login</strong></a>
        </div>
        <div class="right-panel"></div>
    </div>
</body>
</html>