<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - IFWS</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');
        :root { --primary-color: #3498db; --dark-blue: #2D64C3; }
        body { font-family: 'Roboto', sans-serif; background-color: #f0f2f5; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-container { background-color: #fff; padding: 2.5rem; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); width: 100%; max-width: 400px; text-align: center; }
        .login-container img { width: 60px; margin-bottom: 1rem; }
        .login-container h1 { font-size: 1.8rem; color: var(--dark-blue); margin-bottom: 0.5rem; }
        .login-container p { color: #777; margin-bottom: 2rem; }
        .form-group { margin-bottom: 1.5rem; text-align: left; }
        .form-group label { display: block; font-weight: 500; margin-bottom: 0.5rem; }
        .form-group input { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 16px; box-sizing: border-box; }
        .btn-login { background-color: var(--dark-blue); color: white; width: 100%; border: none; padding: 14px; border-radius: 6px; font-size: 16px; font-weight: 700; cursor: pointer; transition: background-color 0.3s; }
        .btn-login:hover { background-color: #2555A5; }
        .error-message { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px; border-radius: 6px; margin-bottom: 1.5rem; }
    </style>
</head>
<body>
    <div class="login-container">
        <img src="assets/images/Logo.jpg" alt="Logo">
        <h1>Selamat Datang</h1>
        <p>Silakan masuk untuk melanjutkan</p>

        <?php
            // Menampilkan pesan error jika ada (dari proses_login.php nanti)
            if (isset($_GET['error'])) {
                echo '<div class="error-message">' . htmlspecialchars($_GET['error']) . '</div>';
            }
        ?>

        <form action="proses_login.php" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn-login">Login</button>
        </form>
    </div>
</body>
</html>