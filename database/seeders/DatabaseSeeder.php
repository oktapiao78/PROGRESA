<?php

namespace Database\Seeders;

use App\Enums\ApprovalStatus;
use App\Enums\ChapterStatus;
use App\Enums\ThesisStatus;
use App\Enums\UserRole;
use App\Models\ChapterApproval;
use App\Models\ChapterTemplate;
use App\Models\DocumentVersion;
use App\Models\Notification;
use App\Models\RevisionItem;
use App\Models\StudyProgram;
use App\Models\Thesis;
use App\Models\ThesisChapter;
use App\Models\ThesisSupervisorAssignment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $program = StudyProgram::updateOrCreate(['code' => 'TI'], [
                'name' => 'Teknik Informatika', 'faculty' => 'Fakultas Ilmu Komputer', 'degree' => 'S1', 'is_active' => true,
            ]);

            $templates = collect([
                ['code' => 'BAB_1', 'order' => 1, 'title' => 'BAB I: Pendahuluan'],
                ['code' => 'BAB_2', 'order' => 2, 'title' => 'BAB II: Tinjauan Pustaka'],
                ['code' => 'BAB_3', 'order' => 3, 'title' => 'BAB III: Metodologi Penelitian'],
                ['code' => 'BAB_4', 'order' => 4, 'title' => 'BAB IV: Hasil dan Pembahasan'],
                ['code' => 'BAB_5', 'order' => 5, 'title' => 'BAB V: Kesimpulan dan Saran'],
            ])->map(fn ($data) => ChapterTemplate::updateOrCreate(
                ['study_program_id' => $program->id, 'code' => $data['code']],
                $data + ['study_program_id' => $program->id, 'description' => null, 'is_required' => true]
            ));

            $password = Hash::make('password123');
            $makeUser = function (string $email, string $name, string $nim, UserRole $role) use ($program, $password) {
                return User::updateOrCreate(['email' => $email], [
                    'name' => $name, 'nim_nip' => $nim, 'role' => $role, 'study_program_id' => $program->id,
                    'password' => $password, 'is_active' => true,
                ]);
            };

            $supervisors = collect(range(1, 8))->map(fn ($i) => $makeUser(
                $i === 1 ? 'pembimbing1@progresa.ac.id' : "dosen{$i}@progresa.ac.id",
                $i === 1 ? 'Prof. Dr. Hendra Wijaya, M.Kom.' : "Dosen Fiktif {$i}, M.Kom.",
                '1980' . str_pad((string) $i, 2, '0', STR_PAD_LEFT) . '150001', UserRole::DOSEN
            ))->values();

            $students = collect(range(1, 24))->map(fn ($i) => $makeUser(
                $i === 1 ? 'aditya@progresa.ac.id' : "mahasiswa{$i}@progresa.ac.id",
                $i === 1 ? 'Aditya Pratama' : "Mahasiswa Fiktif {$i}",
                '230101' . str_pad((string) $i, 3, '0', STR_PAD_LEFT), UserRole::MAHASISWA
            ))->values();

            foreach ($students as $index => $student) {
                $p1 = $supervisors[$index % 8];
                $p2 = $supervisors[($index + 1) % 8];
                $thesis = Thesis::updateOrCreate(['student_id' => $student->id], [
                    'study_program_id' => $program->id,
                    'title' => 'Rancang Bangun Sistem Informasi Penelitian Mahasiswa ' . ($index + 1),
                    'abstract' => 'Penelitian dan pengembangan sistem berbasis Laravel untuk mendukung proses akademik.',
                    'academic_year' => '2025/2026', 'supervisor_1_id' => $p1->id, 'supervisor_2_id' => $p2->id,
                    'status' => ThesisStatus::IN_PROGRESS, 'progress_percentage' => $index % 5 === 0 ? 20 : 0,
                ]);

                foreach ($templates as $template) {
                    $chapter = ThesisChapter::updateOrCreate(
                        ['thesis_id' => $thesis->id, 'order' => $template->order],
                        ['chapter_template_id' => $template->id, 'title' => $template->title, 'status' => ChapterStatus::NOT_STARTED]
                    );

                    if ($template->order === 1 && $index < 8) {
                        $path = "theses/{$thesis->id}/bab_1/demo_v1.txt";
                        Storage::disk('local')->put($path, "BAB I - Draft {$student->name}\n\nDokumen simulasi PROGRESA.");
                        $version = DocumentVersion::updateOrCreate(
                            ['thesis_chapter_id' => $chapter->id, 'version_number' => 1],
                            ['file_path' => $path, 'original_filename' => 'BAB_I_Draft.txt', 'mime_type' => 'text/plain', 'file_size' => 60,
                             'notes' => 'Draft simulasi untuk pengujian end-to-end.', 'text_content' => 'Dokumen simulasi PROGRESA.', 'uploader_id' => $student->id]
                        );
                        $chapter->update(['current_version_id' => $version->id, 'status' => $index % 3 === 0 ? ChapterStatus::REVISION_REQUIRED : ChapterStatus::IN_REVIEW]);
                        RevisionItem::updateOrCreate(
                            ['thesis_chapter_id' => $chapter->id, 'supervisor_id' => $p1->id, 'note' => 'Tambahkan sumber referensi dan perjelas rumusan masalah.'],
                            ['document_version_id' => $version->id, 'is_resolved' => false]
                        );
                        ChapterApproval::updateOrCreate(
                            ['thesis_chapter_id' => $chapter->id, 'stage' => 'pembimbing_1'],
                            ['approver_id' => $p1->id, 'status' => ApprovalStatus::REVISION_REQUESTED, 'feedback' => 'Mohon perbaiki draft BAB I.', 'action_date' => now()]
                        );
                    }
                }

                foreach ([1 => $p1, 2 => $p2] as $role => $supervisor) {
                    ThesisSupervisorAssignment::updateOrCreate(
                        ['thesis_id' => $thesis->id, 'role' => $role, 'supervisor_id' => $supervisor->id, 'ended_at' => null],
                        ['assigned_at' => $thesis->created_at ?? now(), 'assigned_by' => null]
                    );
                }

                Notification::firstOrCreate(
                    ['user_id' => $student->id, 'title' => 'Data skripsi siap digunakan'],
                    ['message' => "Pembimbing {$p1->name} dan {$p2->name} telah ditetapkan.", 'type' => 'assignment', 'link' => route('dashboard.mahasiswa')]
                );
                Notification::firstOrCreate(
                    ['user_id' => $p1->id, 'title' => "Draft mahasiswa {$student->name}"],
                    ['message' => 'Terdapat draft BAB I yang perlu ditinjau.', 'type' => 'chapter_upload', 'link' => route('theses.show', $thesis)]
                );
            }
        });
    }
}
