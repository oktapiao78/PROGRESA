<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Thesis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user && $user->isDosen()) {
            return redirect()->route('dashboard.dosen');
        }

        return redirect()->route('dashboard.mahasiswa');
    }

    public function mahasiswaDashboard()
    {
        $student = Auth::user();

        // Cari data skripsi mahasiswa di database
        $studentThesis = Thesis::with(['supervisor1', 'supervisor2', 'chapters'])
            ->where('student_id', $student->id)
            ->first();

        if ($studentThesis) {
            $thesis = (object) [
                'title' => $studentThesis->title,
                'objective' => $studentThesis->abstract ?? 'Tujuan Penelitian Skripsi',
                'status' => $studentThesis->status ? $studentThesis->status->label() : 'Bimbingan Berjalan',
                'risk_level' => $studentThesis->risk_level ? $studentThesis->risk_level->label() : 'Rendah',
                'supervisor1' => (object) ['name' => $studentThesis->supervisor1?->name ?? 'Belum Ditentukan'],
                'supervisor2' => (object) ['name' => $studentThesis->supervisor2?->name ?? 'Belum Ditentukan'],
                'chapters' => $studentThesis->chapters->map(function ($c) {
                    return (object) [
                        'title' => $c->title,
                        'status' => $c->status ? $c->status->label() : 'Belum Dimulai',
                        'version' => $c->current_version ?? 1,
                        'revisions_open' => 0,
                        'revisions_total' => 0,
                    ];
                })
            ];
        } else {
            $thesis = (object) [
                'title' => 'Rancang Bangun Sistem Informasi Pemantauan Bimbingan Skripsi Menggunakan Arsitektur Modular',
                'objective' => 'Tujuan Penelitian Skripsi',
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
        }

        return view('dashboard.mahasiswa', compact('student', 'thesis'));
    }

    public function dosenDashboard()
    {
        $user = Auth::user();

        $p1Count = Thesis::where('supervisor_1_id', $user->id)->count();
        $p2Count = Thesis::where('supervisor_2_id', $user->id)->count();

        $stats = (object) [
            'p1_count' => $p1Count > 0 ? $p1Count : 2,
            'p2_count' => $p2Count > 0 ? $p2Count : 3,
            'draft_count' => 0,
            'request_count' => 2,
        ];

        $theses = Thesis::with('student')
            ->where(function ($q) use ($user) {
                $q->where('supervisor_1_id', $user->id)
                  ->orWhere('supervisor_2_id', $user->id);
            })
            ->get();

        if ($theses->isNotEmpty()) {
            $mahasiswa_list = $theses->map(function ($t) use ($user) {
                return (object) [
                    'nama' => $t->student?->name ?? 'Mahasiswa',
                    'nim' => $t->student?->nim_nip ?? '-',
                    'judul' => $t->title,
                    'progress' => $t->progress_percentage ?? 0,
                    'risk' => $t->risk_level ? $t->risk_level->label() : null,
                    'pembimbing' => $t->supervisor_1_id === $user->id ? 'Pembimbing 1' : 'Pembimbing 2',
                ];
            });
        } else {
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
        }

        return view('dashboard.dosen', compact('stats', 'mahasiswa_list'));
    }
}