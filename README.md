# PROGRESA — Sistem Manajemen Bimbingan Skripsi & Tugas Akhir

Progresa adalah platform web berbasis Laravel untuk pengelolaan dan pemantauan tugas akhir/skripsi mahasiswa secara terintegrasi dengan persetujuan bertingkat, pelacakan riwayat versi draf, perbandingan naskah dokumen secara visual (*document diff*), penjadwalan bimbingan, serta deteksi risiko stagnansi mahasiswa.

---

## Fitur Utama

- **Multi-Identifier Authentication**: Pengguna dapat masuk menggunakan NIM (Mahasiswa), NIP (Dosen), atau Email (Semua Role).
- **Multi-Role Access Control**:
  - **Mahasiswa**: Pengajuan draf skripsi per bab, histori versi file, tanggapan revisi, dan pengajuan jadwal bimbingan.
  - **Dosen Pembimbing (P1 & P2)**: Review bab, checklist revisi interaktif, persetujuan bab bertingkat, dan manajemen slot bimbingan.
  - **Administrator**: Pengelolaan master data pengguna, program studi, dan audit log sistem.
- **Visual Document Diff**: Komparasi visual kata/baris antara dua versi draf naskah skripsi secara real-time.
- **Deteksi Stagnansi & Eskalasi Otomatis**: Identifikasi mahasiswa tanpa aktivitas bimbingan > 14 atau > 28 hari.
embangkan untuk kebutuhan akademik Sistem Informasi / Teknik Informatika. Hak cipta dilindungi.
