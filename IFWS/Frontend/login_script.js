document.addEventListener('DOMContentLoaded', () => {
    // --- DATABASE PENGGUNA SEMU ---
    // Di aplikasi nyata, ini akan diverifikasi di server.
    const users = {
        // Pengguna yang sudah ada
        'pic@ifws.com': { password: 'pic123', role: 'pic', redirect: 'pages/homepage.html' },
        // Pengguna baru yang akan dibuat halamannya
        'sekretariat@ifws.com': { password: 'sekre123', role: 'sekretariat', redirect: 'pages/dashboard_sekretariat.html' },
        'bendahara@ifws.com': { password: 'benda123', role: 'bendahara', redirect: 'pages/dashboard_bendahara.html' },
        'promosi@ifws.com': { password: 'promo123', role: 'promosi', redirect: 'pages/dashboard_promosi.html' },
        'teknisi@ifws.com': { password: 'tek123', role: 'teknisi', redirect: 'pages/dashboard_teknisi.html' },
        'user@ifws.com': { password: 'user123', role: 'user', redirect: 'pages/dashboard_user.html' },
    };

    const loginForm = document.getElementById('login-form');

    loginForm.addEventListener('submit', (e) => {
        e.preventDefault(); // Mencegah form dari refresh halaman

        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        // Cek apakah email ada di database
        if (users[email]) {
            // Cek apakah password cocok
            if (users[email].password === password) {
                alert(`Login berhasil! Selamat datang, ${users[email].role}.`);
                // Arahkan ke halaman yang sesuai dengan perannya
                window.location.href = users[email].redirect;
            } else {
                alert('Password yang Anda masukkan salah!');
            }
        } else {
            alert('Email tidak ditemukan!');
        }
    });
});