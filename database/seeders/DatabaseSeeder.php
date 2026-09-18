<?php

namespace Database\Seeders;

use App\Enums\ApprovalStatus;
use App\Enums\ChapterStatus;
use App\Enums\RiskLevel;
use App\Enums\SupervisionStatus;
use App\Enums\ThesisStatus;
use App\Enums\UserRole;
use App\Models\AuditLog;
use App\Models\ChapterApproval;
use App\Models\ChapterTemplate;
use App\Models\DocumentVersion;
use App\Models\RevisionItem;
use App\Models\StagnationLog;
use App\Models\StudyProgram;
use App\Models\SupervisionRequest;
use App\Models\SupervisionSlot;
use App\Models\Thesis;
use App\Models\ThesisChapter;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Study Programs
        $prodiTI = StudyProgram::create([
            'code' => 'TI',
            'name' => 'Teknik Informatika',
            'faculty' => 'Fakultas Ilmu Komputer',
            'degree' => 'S1',
            'is_active' => true,
        ]);

        $prodiSI = StudyProgram::create([
            'code' => 'SI',
            'name' => 'Sistem Informasi',
            'faculty' => 'Fakultas Ilmu Komputer',
            'degree' => 'S1',
            'is_active' => true,
        ]);

        // 2. Chapter Templates
        $templates = [
            ['code' => 'BAB_1', 'order' => 1, 'title' => 'BAB I: Pendahuluan', 'description' => 'Latar Belakang Masalah, Rumusan Masalah, Batasan Masalah, Tujuan Penelitian, dan Manfaat Penelitian.'],
            ['code' => 'BAB_2', 'order' => 2, 'title' => 'BAB II: Tinjauan Pustaka', 'description' => 'Kajian Teori Pendukung, Kajian Pustaka Terdahulu, Kerangka Berpikir, dan State of the Art.'],
            ['code' => 'BAB_3', 'order' => 3, 'title' => 'BAB III: Metodologi Penelitian', 'description' => 'Metode Pengembangan, Analisis Kebutuhan Sistem, Perancangan Arsitektur, dan Instrumen Pengujian.'],
            ['code' => 'BAB_4', 'order' => 4, 'title' => 'BAB IV: Hasil dan Pembahasan', 'description' => 'Implementasi Kode Sumber, Pengujian Black Box / White Box, Analisis Performa, dan Evaluasi Pengguna.'],
            ['code' => 'BAB_5', 'order' => 5, 'title' => 'BAB V: Kesimpulan dan Saran', 'description' => 'Kesimpulan Akhir Penelitian, Kontribusi Ilmiah, serta Saran untuk Pengembangan Lanjutan.'],
        ];

        foreach ($templates as $t) {
            ChapterTemplate::create($t);
        }

        // 3. Users
        $defaultPassword = Hash::make('password123');

        $admin = User::create([
            'name' => 'Administrator Progresa',
            'email' => 'admin@progresa.ac.id',
            'nim_nip' => 'ADM001',
            'role' => UserRole::ADMIN,
            'study_program_id' => $prodiTI->id,
            'password' => $defaultPassword,
            'is_active' => true,
        ]);

        $kaprodi = User::create([
            'name' => 'Dr. Eng. Irfan Pratama, M.T.',
            'email' => 'kaprodi@progresa.ac.id',
            'nim_nip' => '197801012003121001',
            'role' => UserRole::KAPRODI,
            'study_program_id' => $prodiTI->id,
            'password' => $defaultPassword,
            'is_active' => true,
        ]);

        $koordinator = User::create([
            'name' => 'Ahmad Fauzi, S.Kom., M.Kom.',
            'email' => 'koordinator@progresa.ac.id',
            'nim_nip' => '198205122008121002',
            'role' => UserRole::KOORDINATOR,
            'study_program_id' => $prodiTI->id,
            'password' => $defaultPassword,
            'is_active' => true,
        ]);

        $dosen1 = User::create([
            'name' => 'Prof. Dr. Hendra Wijaya, S.T., M.Kom.',
            'email' => 'pembimbing1@progresa.ac.id',
            'nim_nip' => '198003152006041003',
            'role' => UserRole::DOSEN,
            'study_program_id' => $prodiTI->id,
            'password' => $defaultPassword,
            'is_active' => true,
        ]);

        $dosen2 = User::create([
            'name' => 'Siti Rahmawati, M.Cs.',
            'email' => 'pembimbing2@progresa.ac.id',
            'nim_nip' => '198507202010122004',
            'role' => UserRole::DOSEN,
            'study_program_id' => $prodiTI->id,
            'password' => $defaultPassword,
            'is_active' => true,
        ]);

        $mhs1 = User::create([
            'name' => 'Dimas Aditya Nugraha',
            'email' => 'mahasiswa1@progresa.ac.id',
            'nim_nip' => '230101001',
            'role' => UserRole::MAHASISWA,
            'study_program_id' => $prodiTI->id,
            'password' => $defaultPassword,
            'is_active' => true,
        ]);

        $mhs2 = User::create([
            'name' => 'Annisa Larasati',
            'email' => 'mahasiswa2@progresa.ac.id',
            'nim_nip' => '230101002',
            'role' => UserRole::MAHASISWA,
            'study_program_id' => $prodiTI->id,
            'password' => $defaultPassword,
            'is_active' => true,
        ]);

        $mhs3 = User::create([
            'name' => 'Rian Hidayat',
            'email' => 'mahasiswa3@progresa.ac.id',
            'nim_nip' => '230101003',
            'role' => UserRole::MAHASISWA,
            'study_program_id' => $prodiTI->id,
            'password' => $defaultPassword,
            'is_active' => true,
        ]);

        // 4. Thesis 1 (Mahasiswa 1: Active, in progress, has revisions & versions)
        $thesis1 = Thesis::create([
            'student_id' => $mhs1->id,
            'study_program_id' => $prodiTI->id,
            'title' => 'Rancang Bangun Sistem Informasi Pemantauan Bimbingan Skripsi Menggunakan Arsitektur Modular Monolith Berbasis Laravel',
            'abstract' => 'Penelitian ini bertujuan membangun sistem monitoring tugas akhir dengan pelacakan riwayat versi draf dokumen, deteksi perubahan kata/baris secara visual, dan pemantauan risiko keterlambatan kelulusan bagi mahasiswa dan dosen pembimbing.',
            'academic_year' => '2025/2026',
            'supervisor_1_id' => $dosen1->id,
            'supervisor_2_id' => $dosen2->id,
            'status' => ThesisStatus::IN_PROGRESS,
            'start_date' => Carbon::now()->subMonths(3),
            'target_completion_date' => Carbon::now()->addMonths(3),
            'progress_percentage' => 40,
        ]);

        // Create 5 chapters for Thesis 1
        $chTemplates = ChapterTemplate::orderBy('order')->get();
        $thesis1Chapters = [];
        foreach ($chTemplates as $tpl) {
            $thesis1Chapters[$tpl->order] = ThesisChapter::create([
                'thesis_id' => $thesis1->id,
                'chapter_template_id' => $tpl->id,
                'title' => $tpl->title,
                'order' => $tpl->order,
                'status' => ChapterStatus::NOT_STARTED,
            ]);
        }

        // Bab 1 Thesis 1: Approved by P1 and P2
        $ch1 = $thesis1Chapters[1];
        $ch1->update(['status' => ChapterStatus::APPROVED]);
        $v1_1 = DocumentVersion::create([
            'thesis_chapter_id' => $ch1->id,
            'version_number' => 1,
            'file_path' => 'theses/1/bab_1/draft_v1.docx',
            'original_filename' => 'Draf_BAB_1_Dimas_v1.docx',
            'mime_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'file_size' => 102400,
            'notes' => 'Pengajuan awal pendahuluan dan rumusan masalah.',
            'text_content' => "BAB I PENDAHULUAN\n\n1.1 Latar Belakang Masalah\nProses bimbingan skripsi sering kali mengalami hambatan koordinasi dan hilangnya riwayat revisi draf naskah skripsi.\n\n1.2 Rumusan Masalah\nBagaimana merancang sistem manajemen skripsi dengan pelacakan versi berkas?",
            'uploader_id' => $mhs1->id,
            'created_at' => Carbon::now()->subDays(45),
        ]);
        $ch1->update(['current_version_id' => $v1_1->id]);

        ChapterApproval::create([
            'thesis_chapter_id' => $ch1->id,
            'stage' => 'pembimbing_1',
            'approver_id' => $dosen1->id,
            'status' => ApprovalStatus::APPROVED,
            'feedback' => 'Latar belakang dan rumusan masalah sudah kuat dan disetujui.',
            'action_date' => Carbon::now()->subDays(40),
        ]);
        ChapterApproval::create([
            'thesis_chapter_id' => $ch1->id,
            'stage' => 'pembimbing_2',
            'approver_id' => $dosen2->id,
            'status' => ApprovalStatus::APPROVED,
            'feedback' => 'Disetujui untuk lanjut ke Bab 2.',
            'action_date' => Carbon::now()->subDays(39),
        ]);

        // Bab 2 Thesis 1: Approved
        $ch2 = $thesis1Chapters[2];
        $ch2->update(['status' => ChapterStatus::APPROVED]);
        $v2_1 = DocumentVersion::create([
            'thesis_chapter_id' => $ch2->id,
            'version_number' => 1,
            'file_path' => 'theses/1/bab_2/draft_v1.docx',
            'original_filename' => 'Draf_BAB_2_Dimas_v1.docx',
            'mime_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'file_size' => 153600,
            'notes' => 'Kajian literatur modular monolith dan penelitian terkait.',
            'text_content' => "BAB II TINJAUAN PUSTAKA\n\n2.1 Modular Monolith Architecture\nModular Monolith membagi aplikasi monolitik ke dalam modul-modul independen tanpa overhead jaringan microservices.",
            'uploader_id' => $mhs1->id,
            'created_at' => Carbon::now()->subDays(30),
        ]);
        $ch2->update(['current_version_id' => $v2_1->id]);
        ChapterApproval::create([
            'thesis_chapter_id' => $ch2->id,
            'stage' => 'pembimbing_1',
            'approver_id' => $dosen1->id,
            'status' => ApprovalStatus::APPROVED,
            'feedback' => 'Tinjauan pustaka lengkap dan relevan.',
            'action_date' => Carbon::now()->subDays(25),
        ]);
        ChapterApproval::create([
            'thesis_chapter_id' => $ch2->id,
            'stage' => 'pembimbing_2',
            'approver_id' => $dosen2->id,
            'status' => ApprovalStatus::APPROVED,
            'feedback' => 'State of the art cukup mutakhir.',
            'action_date' => Carbon::now()->subDays(24),
        ]);

        // Bab 3 Thesis 1: In Revision with 2 Versions (v1 and v2) for visual diff demonstration!
        $ch3 = $thesis1Chapters[3];
        $ch3->update(['status' => ChapterStatus::REVISION_REQUIRED]);

        $v3_text_old = "BAB III METODOLOGI PENELITIAN\n\n3.1 Metode Pengembangan Sistem\nMetode yang digunakan adalah Waterfall konvensional dengan tahapan analisis, desain, pengkodean, dan pengujian.\n\n3.2 Kebutuhan Fungsional\n1. Pengguna dapat login.\n2. Mahasiswa dapat upload draf skripsi.\n\n3.3 Instrumen Pengujian\nPengujian dilakukan secara manual oleh peneliti.";

        $v3_text_new = "BAB III METODOLOGI PENELITIAN\n\n3.1 Metode Pengembangan Sistem\nMetode yang digunakan adalah Agile Scrum yang disesuaikan untuk pengembangan modular monolith dengan iterasi sprint 2 minggu.\n\n3.2 Kebutuhan Fungsional\n1. Pengguna dapat login dengan multi-identifier (NIM/NIP atau Email).\n2. Mahasiswa dapat upload draf skripsi per bab dengan versioning otomatis.\n3. Dosen dapat membandingkan dua versi dokumen secara visual dengan penandaan diff.\n4. Koordinator dapat memantau stagnansi dan eskalasi bimbingan.\n\n3.3 Instrumen Pengujian\nPengujian fungsional menggunakan Black-Box Testing dan Automated Unit Testing dengan PHPUnit.";

        // Ensure storage directory exists
        Storage::disk('local')->makeDirectory('theses/1/bab_3');
        Storage::disk('local')->put('theses/1/bab_3/draft_v1.txt', $v3_text_old);
        Storage::disk('local')->put('theses/1/bab_3/draft_v2.txt', $v3_text_new);

        $v3_1 = DocumentVersion::create([
            'thesis_chapter_id' => $ch3->id,
            'version_number' => 1,
            'file_path' => 'theses/1/bab_3/draft_v1.txt',
            'original_filename' => 'Draf_BAB_3_Dimas_v1.txt',
            'mime_type' => 'text/plain',
            'file_size' => strlen($v3_text_old),
            'notes' => 'Draf pertama metodologi dan kebutuhan sistem.',
            'text_content' => $v3_text_old,
            'uploader_id' => $mhs1->id,
            'created_at' => Carbon::now()->subDays(10),
        ]);

        $v3_2 = DocumentVersion::create([
            'thesis_chapter_id' => $ch3->id,
            'version_number' => 2,
            'file_path' => 'theses/1/bab_3/draft_v2.txt',
            'original_filename' => 'Draf_BAB_3_Dimas_v2_Revisi.txt',
            'mime_type' => 'text/plain',
            'file_size' => strlen($v3_text_new),
            'notes' => 'Revisi metode Agile Scrum dan penambahan kebutuhan fungsional diff & eskalasi sesuai catatan Prof. Hendra.',
            'text_content' => $v3_text_new,
            'uploader_id' => $mhs1->id,
            'created_at' => Carbon::now()->subDays(2),
        ]);
        $ch3->update(['current_version_id' => $v3_2->id]);

        // Revision items for Bab 3
        RevisionItem::create([
            'thesis_chapter_id' => $ch3->id,
            'document_version_id' => $v3_1->id,
            'supervisor_id' => $dosen1->id,
            'note' => 'Ganti metode Waterfall dengan pendekatan Agile/Iteratif dan jabarkan instrumen pengujian otomatis.',
            'is_resolved' => true,
            'student_response' => 'Sudah diperbaiki pada draf v2 sub-bab 3.1 dan 3.3 dengan metode Scrum dan PHPUnit.',
            'resolved_at' => Carbon::now()->subDays(1),
            'created_at' => Carbon::now()->subDays(9),
        ]);

        RevisionItem::create([
            'thesis_chapter_id' => $ch3->id,
            'document_version_id' => $v3_2->id,
            'supervisor_id' => $dosen1->id,
            'note' => 'Tambahkan diagram arsitektur modular monolith pada sub-bab perancangan sistem.',
            'is_resolved' => false,
            'created_at' => Carbon::now()->subDay(),
        ]);

        // Recalculate progress for thesis 1
        $thesis1->recalculateProgress();

        // 5. Thesis 2 (Mahasiswa 2: Stagnant Demonstration, > 28 days inactive, escalation flag)
        $thesis2 = Thesis::create([
            'student_id' => $mhs2->id,
            'study_program_id' => $prodiTI->id,
            'title' => 'Penerapan Deep Learning Convolutional Neural Network untuk Klasifikasi Penyakit Daun Padi Berbasis Citra',
            'abstract' => 'Penelitian ini mengembangkan model klasifikasi penyakit daun padi menggunakan arsitektur ResNet-50 untuk mendukung pertanian presisi.',
            'academic_year' => '2025/2026',
            'supervisor_1_id' => $dosen1->id,
            'supervisor_2_id' => $dosen2->id,
            'status' => ThesisStatus::IN_PROGRESS,
            'start_date' => Carbon::now()->subMonths(6),
            'target_completion_date' => Carbon::now()->subWeeks(2), // Past target date!
            'progress_percentage' => 20,
        ]);

        foreach ($chTemplates as $tpl) {
            ThesisChapter::create([
                'thesis_id' => $thesis2->id,
                'chapter_template_id' => $tpl->id,
                'title' => $tpl->title,
                'order' => $tpl->order,
                'status' => $tpl->order === 1 ? ChapterStatus::APPROVED : ChapterStatus::NOT_STARTED,
            ]);
        }

        StagnationLog::create([
            'thesis_id' => $thesis2->id,
            'days_inactive' => 28,
            'last_student_activity_at' => Carbon::now()->subDays(28),
            'last_supervisor_activity_at' => Carbon::now()->subDays(27),
            'risk_level' => RiskLevel::CRITICAL,
            'notes' => 'Tidak ada pengajuan draf selama 28 hari dan target kelulusan telah terlampaui. Perlu intervensi intensif dari Koordinator TA.',
            'is_escalated' => true,
            'escalated_at' => Carbon::now()->subDays(5),
        ]);

        // 6. Thesis 3 (Mahasiswa 3: Ready for Defense, 100% complete)
        $thesis3 = Thesis::create([
            'student_id' => $mhs3->id,
            'study_program_id' => $prodiTI->id,
            'title' => 'Analisis Sentimen Opini Publik Terhadap Kebijakan Transportasi Cerdas Menggunakan IndoBERT',
            'abstract' => 'Penelitian komparasi arsitektur BERT dan IndoBERT dalam mengklasifikasikan respons publik terhadap integrasi transportasi publik cerdas perkotaan.',
            'academic_year' => '2025/2026',
            'supervisor_1_id' => $dosen2->id,
            'supervisor_2_id' => $dosen1->id,
            'status' => ThesisStatus::DEFENSE_READY,
            'start_date' => Carbon::now()->subMonths(5),
            'target_completion_date' => Carbon::now()->addMonth(),
            'progress_percentage' => 100,
        ]);

        foreach ($chTemplates as $tpl) {
            ThesisChapter::create([
                'thesis_id' => $thesis3->id,
                'chapter_template_id' => $tpl->id,
                'title' => $tpl->title,
                'order' => $tpl->order,
                'status' => ChapterStatus::APPROVED,
            ]);
        }

        // 7. Supervision Slots
        $slot1 = SupervisionSlot::create([
            'supervisor_id' => $dosen1->id,
            'date' => Carbon::now()->addDays(2)->toDateString(),
            'start_time' => '09:00',
            'end_time' => '11:30',
            'quota' => 3,
            'booked_count' => 1,
            'location_type' => 'offline',
            'room_or_link' => 'Ruang Dosen 302, Gedung Fasilkom',
            'is_active' => true,
        ]);

        $slot2 = SupervisionSlot::create([
            'supervisor_id' => $dosen1->id,
            'date' => Carbon::now()->addDays(4)->toDateString(),
            'start_time' => '13:30',
            'end_time' => '15:30',
            'quota' => 2,
            'booked_count' => 0,
            'location_type' => 'online',
            'room_or_link' => 'https://meet.google.com/pgr-progresa-demo',
            'is_active' => true,
        ]);

        $slot3 = SupervisionSlot::create([
            'supervisor_id' => $dosen2->id,
            'date' => Carbon::now()->addDays(3)->toDateString(),
            'start_time' => '10:00',
            'end_time' => '12:00',
            'quota' => 2,
            'booked_count' => 0,
            'location_type' => 'offline',
            'room_or_link' => 'Lab Rekayasa Perangkat Lunak',
            'is_active' => true,
        ]);

        // Active booking for Mahasiswa 1
        SupervisionRequest::create([
            'supervision_slot_id' => $slot1->id,
            'thesis_id' => $thesis1->id,
            'student_id' => $mhs1->id,
            'supervisor_id' => $dosen1->id,
            'topic' => 'Konsultasi Perbaikan Metodologi BAB III & Diagram Arsitektur',
            'student_notes' => 'Ingin memastikan desain komponen modul telah sesuai dengan rekomendasi Prof.',
            'status' => SupervisionStatus::ACCEPTED,
            'scheduled_at' => Carbon::parse(Carbon::parse($slot1->date)->toDateString() . ' ' . $slot1->start_time),
        ]);

        // Past completed session for Mahasiswa 1
        SupervisionRequest::create([
            'supervision_slot_id' => null,
            'thesis_id' => $thesis1->id,
            'student_id' => $mhs1->id,
            'supervisor_id' => $dosen1->id,
            'topic' => 'Review Draf BAB II & Formulasi Masalah',
            'student_notes' => 'Membahas perbandingan paper terdahulu.',
            'status' => SupervisionStatus::COMPLETED,
            'meeting_summary' => 'Mahasiswa telah melengkapi 15 referensi jurnal bereputasi 5 tahun terakhir. Format penulisan tinjauan pustaka sudah disetujui.',
            'action_items' => 'Lanjutkan penulisan draf BAB III dengan penekanan pada spesifikasi kebutuhan non-fungsional.',
            'scheduled_at' => Carbon::now()->subDays(25),
            'completed_at' => Carbon::now()->subDays(25),
        ]);

        // Audit Logs
        AuditLog::create([
            'user_id' => $mhs1->id,
            'action' => 'DRAFT_UPLOADED',
            'auditable_type' => DocumentVersion::class,
            'auditable_id' => $v3_2->id,
            'details' => 'Mahasiswa mengunggah draf BAB III Versi 2.',
            'ip_address' => '127.0.0.1',
        ]);
        AuditLog::create([
            'user_id' => $dosen1->id,
            'action' => 'APPROVAL_pembimbing_1_approved',
            'auditable_type' => ThesisChapter::class,
            'auditable_id' => $ch1->id,
            'details' => 'Dosen Pembimbing 1 menyetujui BAB I Pendahuluan.',
            'ip_address' => '127.0.0.1',
        ]);
        AuditLog::create([
            'user_id' => $koordinator->id,
            'action' => 'ESCALATION_TRIGGERED',
            'auditable_type' => Thesis::class,
            'auditable_id' => $thesis2->id,
            'details' => 'Eskalasi otomatis: Skripsi Annisa Larasati tidak ada progres > 28 hari.',
            'ip_address' => '127.0.0.1',
        ]);
    }
}
