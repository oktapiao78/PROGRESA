<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;

class DashboardController extends Controller
{
    public function index()
    {
        $role = auth()->user()->role ?? UserRole::MAHASISWA;

        if ($role === UserRole::DOSEN) {
            return $this->dosenDashboard();
        }

        return $this->mahasiswaDashboard();
    }

    protected function mahasiswaDashboard()
    {
        $student = (object) ['name' => 'Naura Ayunda', 'nim_nip' => '233150601111055'];
        
        $thesis = (object) [
            'title' => 'Rancang Bangun Sistem Informasi Pemantauan Bimbingan Skripsi Menggunakan Arsitektur Modular',
            'objective' => 'Tujuan Penelitian',
            'status' => 'Bimbingan Berjalan',
            'risk_level' => 'Rendah',
            'supervisor1' => (object) ['name' => 'Ir. Dr. Arka Mahendrata., S.T., S.kom.'],
            'supervisor2' => (object) ['name' => 'Anindya Sekar Pranadya., S.pd., M. Cs.'],
            'chapters' => [
                (object) ['title' => 'BAB I: Pendahuluan', 'status' => 'Disetujui', 'version' => 1, 'revisions_open' => 0, 'revisions_total' => 0],
                (object) ['title' => 'BAB II: Tinjauan Pustaka', 'status' => 'Disetujui', 'version' => 1, 'revisions_open' => 0, 'revisions_total' => 0],
                (object) ['title' => 'BAB III: Metodologi Penelitian', 'status' => 'Perlu Revisi', 'version' => 1, 'revisions_open' => 0, 'revisions_total' => 0],
                (object) ['title' => 'BAB IV: Hasil Penelitian', 'status' => 'Belum Dimulai', 'version' => 1, 'revisions_open' => 0, 'revisions_total' => 0],
            ]
        ];

        return view('dashboard.mahasiswa', compact('student', 'thesis'));
    }

    protected function dosenDashboard()
    {
        $stats = (object) [
            'p1_count' => 2,
            'p2_count' => 3,
            'draft_count' => 0,
            'request_count' => 2,
        ];

        $mahasiswa_list = [
            (object) [
                'nama' => 'Naura Ayunda',
                'nim' => '233150601111055',
                'judul' => 'Rancang Bangun Sistem Informasi Pemantauan Bimbingan Skripsi Menggunakan Arsitektur Modular',
                'progress' => 30,
                'risk' => null,
                'pembimbing' => 'Pembimbing 1'
            ],
            (object) [
                'nama' => 'Ellio Chandranata',
                'nim' => '233150601111028',
                'judul' => 'Analisis Sentimen Opini Publik Terhadap Kebijakan Transportasi Cerdas Menggunakan IndoTrans',
                'progress' => 50,
                'risk' => 'Risiko Tinggi (Stagnan)',
                'pembimbing' => 'Pembimbing 1'
            ],
            (object) [
                'nama' => 'Sammy Galleo',
                'nim' => '233150601111029',
                'judul' => 'Penerapan Deep Learning Convolutional Neural Network untuk Klasifikasi Penyakit Daun Padi Berbasis Citra',
                'progress' => 20,
                'risk' => 'Kritis (Terancam DO/Mundur)',
                'pembimbing' => 'Pembimbing 2'
            ],
        ];

        return view('dashboard.dosen', compact('stats', 'mahasiswa_list'));
    }
}