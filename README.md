# PROGRESA — Sistem Manajemen Bimbingan Skripsi & Tugas Akhir

Progresa adalah platform web berbasis Laravel untuk pengelolaan dan pemantauan tugas akhir/skripsi mahasiswa secara terintegrasi dengan persetujuan bertingkat, pelacakan riwayat versi draf, perbandingan naskah dokumen secara visual (*document diff*), penjadwalan bimbingan, serta deteksi risiko stagnansi mahasiswa.

---

## Fitur Utama

- **Multi-Identifier Authentication**: Pengguna dapat masuk menggunakan NIM (Mahasiswa), NIP (Dosen, Koordinator TA, Kaprodi), atau Email (Semua Role).
- **Multi-Role Access Control**:
  - **Mahasiswa**: Pengajuan draf skripsi per bab, histori versi file, tanggapan revisi, dan pengajuan jadwal bimbingan.
  - **Dosen Pembimbing (P1 & P2)**: Review bab, checklist revisi interaktif, persetujuan bab bertingkat, dan manajemen slot bimbingan.
  - **Koordinator TA**: Monitoring progres seluruh mahasiswa prodi, eskalasi keterlambatan/stagnansi bimbingan, dan analisis risiko.
  - **Ketua Program Studi (Kaprodi)**: Dashboard eksekutif distribusi beban bimbingan dosen, statistik kelulusan tepat waktu, dan audit aktivitas.
  - **Administrator**: Pengelolaan master data pengguna, program studi, dan audit log sistem.
- **Visual Document Diff**: Komparasi visual kata/baris antara dua versi draf naskah skripsi secara real-time.
- **Deteksi Stagnansi & Eskalasi Otomatis**: Identifikasi mahasiswa tanpa aktivitas bimbingan > 14 atau > 28 hari.

---

## Persyaratan Sistem

- PHP `>= 8.1` (dengan ekstensi `pdo_sqlite` atau `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`)
- Composer
- Node.js & NPM
- SQLite (default) atau MySQL

---

## Panduan Instalasi & Setup Database

1. **Clone repository dan masuk ke direktori proyek**:
   ```bash
   cd "PROJECT AKHIR"
   ```

2. **Salin file environment dan generate app key**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Pastikan konfigurasi database di `.env` sudah sesuai** (default menggunakan SQLite):
   ```env
   DB_CONNECTION=sqlite
   ```
   *Catatan: Pastikan file `database/database.sqlite` tersedia.*

4. **Jalankan migrasi database dan seeder data awal**:
   ```bash
   php artisan migrate --seed
   ```
   *Untuk reset total environment development lokal:*
   ```bash
   php artisan migrate:fresh --seed
   ```

5. **Install dependensi frontend dan build assets**:
   ```bash
   npm install
   npm run build
   ```

6. **Jalankan server aplikasi**:
   ```bash
   php artisan serve
   ```
   Akses aplikasi melalui browser di `http://127.0.0.1:8000`.

---

## Kredensial Akun Demo (Demo Credentials)

Semua akun demo di bawah ini telah di-seed ke database dengan password default: `password123`.

| Role | Nama Pengguna | Identifier Login (NIM / NIP / Email) | Password | Deskripsi Akses |
| :--- | :--- | :--- | :--- | :--- |
| **Mahasiswa** | Dimas Aditya Nugraha | NIM: `230101001`<br>Email: `mahasiswa1@progresa.ac.id` | `password123` | Skripsi aktif (Bab 1 & 2 Approved, Bab 3 Revisi) |
| **Mahasiswa** | Annisa Larasati | NIM: `230101002`<br>Email: `mahasiswa2@progresa.ac.id` | `password123` | Skripsi stagnan (> 28 hari tanpa progres) |
| **Mahasiswa** | Rian Hidayat | NIM: `230101003`<br>Email: `mahasiswa3@progresa.ac.id` | `password123` | Siap Sidang (*Defense Ready*, 100%) |
| **Dosen Pembimbing 1** | Prof. Dr. Hendra Wijaya, S.T., M.Kom. | NIP: `198003152006041003`<br>Email: `pembimbing1@progresa.ac.id` | `password123` | Pembimbing Utama & Jadwal Bimbingan |
| **Dosen Pembimbing 2** | Siti Rahmawati, M.Cs. | NIP: `198507202010122004`<br>Email: `pembimbing2@progresa.ac.id` | `password123` | Pembimbing Pendamping |
| **Koordinator TA** | Ahmad Fauzi, S.Kom., M.Kom. | NIP: `198205122008121002`<br>Email: `koordinator@progresa.ac.id` | `password123` | Monitoring & Approval Eskalasi |
| **Ketua Program Studi** | Dr. Eng. Irfan Pratama, M.T. | NIP: `197801012003121001`<br>Email: `kaprodi@progresa.ac.id` | `password123` | Laporan Eksekutif, Beban Dosen & Risiko |
| **Administrator** | Administrator Progresa | ID/NIP: `ADM001`<br>Email: `admin@progresa.ac.id` | `password123` | Pengelolaan Pengguna & Audit Log |

> **Tips Login Cepat**: Pada halaman `/login`, Anda juga dapat menggunakan tombol **"Akun Demo Cepat (1-Klik Isi)"** untuk langsung mengisi kredensial secara otomatis.

---

## Pengujian Otomatis (Automated Testing)

Jalankan rangkaian test otomatis dengan PHPUnit:
```bash
php artisan test
```

Untuk menjalankan test autentikasi secara khusus:
```bash
php artisan test --filter=AuthenticationFlowTest
```

---

## Lisensi
Proyek ini dikembangkan untuk kebutuhan akademik Sistem Informasi / Teknik Informatika. Hak cipta dilindungi.
