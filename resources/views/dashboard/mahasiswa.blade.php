<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Mahasiswa - PROGRESA</title>
    <!-- Font & Icon -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Panggil CSS -->
    <link rel="stylesheet" href="/css/mahasiswa.css">
</head>
<body>

    <!-- Header Atas -->
    <header class="top-header">
        <div class="header-title">
            <h1>Portal Tugas Akhir <span>| Proses & Bimbingan Skripsi</span></h1>
        </div>
        <div class="header-icons">
            <div class="icon-btn alert-icon">
                <i class="fa-regular fa-bell"></i>
                <span class="badge"></span>
            </div>
            <div class="icon-btn profile-icon">
                <i class="fa-regular fa-user"></i>
            </div>
        </div>
    </header>

    <!-- Konten Utama -->
    <main class="dashboard-container">
        
        <!-- Info Skripsi -->
        <section class="card thesis-info-card">
            <div class="thesis-header-row">
                <div class="thesis-title-area">
                    <div class="badges">
                        <span class="badge-yellow">Bimbingan Berjalan</span>
                        <span class="badge-purple">Risiko Rendah</span>
                    </div>
                    <h2>Judul Skripsi</h2>
                    <p class="subtitle">Tujuan Penelitian</p>
                </div>
                
                <div class="progress-box">
                    <div class="progress-text">
                        <span>Total Progres Bab</span>
                        <span class="percentage">40%</span>
                    </div>
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill" style="width: 40%;"></div>
                    </div>
                    <p class="progress-detail">2 dari 5 Bab telah diperbarui</p>
                </div>
            </div>
            
            <div class="supervisors">
                <p>Pembimbing 1 | <strong>Ir. Dr. Arka Mahendrata., S.T., S.kom.</strong></p>
                <p>Pembimbing 2 | <strong>Anindya Sekar Pranadya., S.pd., M. Cs.</strong></p>
            </div>
        </section>

        <!-- Peringatan Revisi -->
        <section class="alert-section">
            <div class="alert-header">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <div>
                    <h4>Perhatian : Anda memiliki 1 butir revisi yang perlu di perbaiki</h4>
                    <p>Periksa catatan dari dosen pembimbing dan berikan respons atau unggah draf bab versi terbaru</p>
                </div>
            </div>
            <div class="alert-action-card">
                <div>
                    <h5>BAB III: METODOLOGI PENELITIAN</h5>
                    <p>Tambahkan diagram arsitektur modular monolith pada sub-bab perancangan sistem</p>
                </div>
                <button class="btn-red">Lihat Bab</button>
            </div>
        </section>

        <!-- Struktur Bab -->
        <section class="chapters-section">
            <h3>Struktur & Pengembangan Bab</h3>
            <p class="section-desc">Unggah draft berkas per bab, pantau riwayat versi dan bandingkan perubahan.</p>
            
            <div class="chapters-grid">
                <div class="chapter-card">
                    <div class="chapter-head">
                        <span class="chap-num">BAB 1</span>
                        <span class="status-badge status-green">Disetujui</span>
                    </div>
                    <h4>BAB I: Pendahuluan</h4>
                    <div class="chap-stats">
                        <div class="stat-row"><span>Versi Terkini:</span> <span>v1</span></div>
                        <div class="stat-row"><span>Butir Revisi:</span> <span>0 Terbuka, 0 Total</span></div>
                    </div>
                    <button class="btn-purple-light">Buka Bab & Versi</button>
                </div>

                <div class="chapter-card">
                    <div class="chapter-head">
                        <span class="chap-num">BAB 2</span>
                        <span class="status-badge status-green">Disetujui</span>
                    </div>
                    <h4>BAB II: Tinjauan Pustaka</h4>
                    <div class="chap-stats">
                        <div class="stat-row"><span>Versi Terkini:</span> <span>v1</span></div>
                        <div class="stat-row"><span>Butir Revisi:</span> <span>0 Terbuka, 0 Total</span></div>
                    </div>
                    <button class="btn-purple-light">Buka Bab & Versi</button>
                </div>

                <div class="chapter-card">
                    <div class="chapter-head">
                        <span class="chap-num">BAB 3</span>
                        <span class="status-badge status-red">Perlu Revisi</span>
                    </div>
                    <h4>BAB III: Metodologi Penelitian</h4>
                    <div class="chap-stats">
                        <div class="stat-row"><span>Versi Terkini:</span> <span>v1</span></div>
                        <div class="stat-row"><span>Butir Revisi:</span> <span>0 Terbuka, 0 Total</span></div>
                    </div>
                    <button class="btn-purple-light">Buka Bab & Versi</button>
                </div>

                <div class="chapter-card">
                    <div class="chapter-head">
                        <span class="chap-num">BAB 4</span>
                        <span class="status-badge status-gray">Belum Dimulai</span>
                    </div>
                    <h4>BAB IV: Hasil Penelitian</h4>
                    <div class="chap-stats">
                        <div class="stat-row"><span>Versi Terkini:</span> <span>v1</span></div>
                        <div class="stat-row"><span>Butir Revisi:</span> <span>0 Terbuka, 0 Total</span></div>
                    </div>
                    <button class="btn-purple-light">Buka Bab & Versi</button>
                </div>
            </div>
        </section>

        <!-- Agenda -->
        <section class="agenda-section">
            <h3>Agenda Bimbingan Terjadwal</h3>
            <p class="section-desc">Daftar sesi konsultasi dan bimbingan aktif bersama dosen pembimbing</p>
            
            <div class="agenda-card">
                <span class="agenda-badge">Disetujui/Terjadwal</span>
                <h4>Konsultasi Perbaikan Metodologi BAB III & Diagram Arsitektur</h4>
                <p>Waktu : Sunday, 20 Oktober 2026 - 09.00 WIB | Tempat: Ruang Dosen F7.3, Gedung Filkom</p>
            </div>
        </section>
        
        <!-- Ruang Kosong agar konten tidak tertutup menu bawah -->
        <div style="height: 100px;"></div>
    </main>

    <!-- Navigasi Bawah -->
    <div class="bottom-bar">
        <nav class="pill-nav">
            <a href="#" class="nav-item"><i class="fa-solid fa-book-open"></i></a>
            <a href="#" class="nav-item active"><i class="fa-solid fa-house"></i></a>
            <a href="#" class="nav-item"><i class="fa-regular fa-calendar-days"></i></a>
        </nav>
        
        <form action="/" method="GET" class="logout-form">
            <button type="submit" class="logout-btn">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> LOG OUT
            </button>
        </form>
    </div>

</body>
</html>