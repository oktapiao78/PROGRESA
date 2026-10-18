<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PROGRESA</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" href="/css/login.css">
</head>

<body>
    <div class="login-wrapper">
        <header class="login-header">
            <h1>PROGRESA</h1>
            <p>Sistem Manajemen Bimbingan Skripsi & Tugas Akhir Berbasis Progres dan<br>Approval Bertingkat</p>
        </header>

        <main class="login-card">
            <form action="/login" method="POST">
                @csrf

                <div class="input-group">
                    <label for="username">NIM / NIP / EMAIL</label>
                    <div class="input-wrapper">
                        <i class="fa-regular fa-user"></i>
                        <input type="text" id="username" name="username" required placeholder=""
                            value="{{ old('username') }}">
                    </div>
                </div>

                <div class="input-group">
                    <div class="label-row">
                        <label for="password">KATA SANDI</label>
                        <a href="#" class="forgot-password">Lupa Kata Sandi?</a>
                    </div>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" required placeholder="">
                    </div>
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Ingat sesi saya</label>
                </div>

                <button type="submit" class="login-btn">Masuk ke Sistem</button>
            </form>
        </main>

        <footer class="login-footer">
            <p>Kendala akun? Hubungi Helpdesk Akademik</p>
            <p>&copy; 2026 Progresa Academic Portal. Hak Cipta Dilindungi.</p>
            <p>PROGRESA v2.4 &bull; Keamanan Terverifikasi SSL</p>
        </footer>
    </div>
</body>

</html>