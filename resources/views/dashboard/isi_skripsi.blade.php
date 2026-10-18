<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BAB 1: BAB I: Pendahuluan - PROGRESA</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/isi_skripsi.css">

    <style>
        /* Paksa sembunyikan tab yang tidak aktif */
        .tab-content {
            display: none !important;
        }
        .tab-content.active {
            display: block !important;
        }
    </style>
</head>
<body>

    <!-- Header Atas -->
    <header class="top-header">
        <div class="header-title">
            <h1>BAB 1: BAB I: Pendahuluan</h1>
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
    <main class="container">
        
        <!-- Header Card Utama -->
        <div class="card main-header-card">
            <div class="header-top">
                <div class="breadcrumb">Skripsi Mahasiswa > BAB 1</div>
                <h2>BAB I : Pendahuluan</h2>
                <div class="status-row">
                    <span class="badge-status">Disetujui</span>
                    <span class="meta-text">Total 1 Versi Terdaftar • 0 Revisi Terbuka</span>
                </div>
            </div>
            <button class="btn-upload">
                <i class="fa-solid fa-upload"></i> Unggah Draft Baru
            </button>
            
            <!-- Tab Navigation -->
            <div class="tab-navigation">
                <button type="button" class="tab-btn active" onclick="switchTab(event, 'tab-dokumen')">Dokumen dan Riwayat Versi</button>
                <button type="button" class="tab-btn" onclick="switchTab(event, 'tab-revisi')">Daftar Butir Revisi</button>
                <button type="button" class="tab-btn" onclick="switchTab(event, 'tab-approval')">Persetujuan Bertingkat Approval</button>
            </div>
        </div>

        <!-- TAB 1: Dokumen dan Riwayat Versi -->
        <div id="tab-dokumen" class="tab-content active">
            <div class="card content-card">
                <div class="version-header">
                    <span class="badge-version">Versi 1</span>
                    <span class="version-status">• Versi Aktif</span>
                    <span class="version-meta">• Diunggah oleh Naura Ayunda (2 Days Ago)</span>
                </div>
                
                <div class="file-info-row">
                    <div class="file-name">
                        <strong>Draft_Bab_1_Naura_v1.Docx</strong>
                        <span class="file-size">(100.0KB)</span>
                    </div>
                    <button class="btn-download">
                        <i class="fa-solid fa-download"></i> Unduh File
                    </button>
                </div>

                <div class="student-note-box">
                    <strong>Catatan Mahasiswa :</strong> Pengajuan awal pendahuluan dan rumusan masalah
                </div>
            </div>
        </div>

        <!-- TAB 2: Daftar Butir Revisi -->
        <div id="tab-revisi" class="tab-content">
            <div class="card content-card">
                <h3>Daftar Butir Revisi & Checklist Perbaikan</h3>
                <div class="empty-state">
                    Belum ada butir catatan revisi untuk bab ini
                </div>
            </div>
        </div>

        <!-- TAB 3: Persetujuan Bertingkat Approval -->
        <div id="tab-approval" class="tab-content">
            <div class="card content-card">
                <h3>Alur Persetujuan Bertingkat (Hierarchical Approval)</h3>
                <p class="subtitle-text">Setiap bab membutuhkan verifikasi bertahap sebelum dinyatakan selesai dan sah untuk diajukan seminar/sidang.</p>

                <div class="approval-grid">
                    <div class="approval-card">
                        <div class="app-head">
                            <span class="app-stage stage-purple">Tahap 1: PEMBIMBING 1</span>
                            <span class="app-status status-approved">Disetujui</span>
                        </div>
                        <h4 class="app-name">Ir. Dr. Arka Mahendrata, S.T., S.kom.</h4>
                        <div class="app-note">"Latar belakang dan rumusan masalah sudah kuat dan disetujui."</div>
                    </div>

                    <div class="approval-card">
                        <div class="app-head">
                            <span class="app-stage stage-blue">Tahap 2: PEMBIMBING 2</span>
                            <span class="app-status status-approved">Disetujui</span>
                        </div>
                        <h4 class="app-name">Anindya Sekar Pranadya., S.pd., M. Cs.</h4>
                        <div class="app-note">"Disetujui untuk melanjutkan pada Bab 2."</div>
                    </div>

                    <div class="approval-card">
                        <div class="app-head">
                            <span class="app-stage stage-orange">Tahap 3: KOORDINATOR TA</span>
                            <span class="app-status status-pending">Menunggu Validasi</span>
                        </div>
                        <h4 class="app-name">Validasi & Verifikasi Akhir</h4>
                    </div>
                </div>
            </div>
        </div>

        <div style="height: 100px;"></div>
    </main>

    <!-- Navigasi Bawah -->
    <div class="bottom-bar">
        <nav class="pill-nav">
            <a href="/mahasiswa/bab" class="nav-item active"><i class="fa-solid fa-book-open"></i></a>
            <a href="/mahasiswa" class="nav-item"><i class="fa-solid fa-house"></i></a>
            <a href="#" class="nav-item"><i class="fa-regular fa-calendar-days"></i></a>
        </nav>
        
        <form action="/" method="GET" class="logout-form">
            <button type="submit" class="logout-btn">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> LOG OUT
            </button>
        </form>
    </div>

    <!-- Script Pindah Tab -->
    <script>
        function switchTab(event, tabId) {
            event.preventDefault();
            
            // Sembunyikan semua tab
            var contents = document.querySelectorAll('.tab-content');
            contents.forEach(function(content) {
                content.classList.remove('active');
            });

            // Matikan warna aktif tombol
            var buttons = document.querySelectorAll('.tab-btn');
            buttons.forEach(function(btn) {
                btn.classList.remove('active');
            });

            // Tampilkan tab yang diklik
            document.getElementById(tabId).classList.add('active');
            event.currentTarget.classList.add('active');
        }
    </script>
</body>
</html>