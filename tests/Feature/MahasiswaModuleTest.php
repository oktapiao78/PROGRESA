<?php

namespace Tests\Feature;

use App\Enums\ChapterStatus;
use App\Enums\SupervisionStatus;
use App\Enums\ThesisStatus;
use App\Enums\UserRole;
use App\Models\ChapterTemplate;
use App\Models\ChatMessage;
use App\Models\DocumentVersion;
use App\Models\Notification;
use App\Models\RevisionItem;
use App\Models\StudyProgram;
use App\Models\SupervisionSlot;
use App\Models\Thesis;
use App\Models\ThesisChapter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MahasiswaModuleTest extends TestCase
{
    use RefreshDatabase;

    protected StudyProgram $prodi;
    protected User $dosen1;
    protected User $otherDosen;
    protected User $student;
    protected User $otherStudent;
    protected Thesis $thesis;
    protected Thesis $otherThesis;
    protected ThesisChapter $chapter1;
    protected ThesisChapter $otherChapter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->prodi = StudyProgram::create([
            'code' => 'TI',
            'name' => 'Teknik Informatika',
            'faculty' => 'FILKOM',
            'degree' => 'S1',
            'is_active' => true,
        ]);

        $this->dosen1 = User::factory()->create([
            'role' => UserRole::DOSEN,
            'nim_nip' => '19800101001',
            'study_program_id' => $this->prodi->id,
            'is_active' => true,
        ]);

        $this->otherDosen = User::factory()->create([
            'role' => UserRole::DOSEN,
            'nim_nip' => '19800101002',
            'study_program_id' => $this->prodi->id,
            'is_active' => true,
        ]);

        $this->student = User::factory()->create([
            'role' => UserRole::MAHASISWA,
            'nim_nip' => '230101001',
            'study_program_id' => $this->prodi->id,
            'is_active' => true,
        ]);

        $this->otherStudent = User::factory()->create([
            'role' => UserRole::MAHASISWA,
            'nim_nip' => '230101002',
            'study_program_id' => $this->prodi->id,
            'is_active' => true,
        ]);

        $tpl = ChapterTemplate::create([
            'code' => 'BAB_1',
            'order' => 1,
            'title' => 'BAB I: Pendahuluan',
            'description' => 'Pendahuluan',
        ]);

        $this->thesis = Thesis::create([
            'student_id' => $this->student->id,
            'study_program_id' => $this->prodi->id,
            'title' => 'Sistem Informasi Monitoring Skripsi PROGRESA',
            'abstract' => 'Abstrak penelitian.',
            'academic_year' => '2025/2026',
            'supervisor_1_id' => $this->dosen1->id,
            'status' => ThesisStatus::IN_PROGRESS,
            'progress_percentage' => 0,
        ]);

        $this->chapter1 = ThesisChapter::create([
            'thesis_id' => $this->thesis->id,
            'chapter_template_id' => $tpl->id,
            'title' => 'BAB I: Pendahuluan',
            'order' => 1,
            'status' => ChapterStatus::NOT_STARTED,
        ]);

        $this->otherThesis = Thesis::create([
            'student_id' => $this->otherStudent->id,
            'study_program_id' => $this->prodi->id,
            'title' => 'Skripsi Mahasiswa Lain',
            'academic_year' => '2025/2026',
            'supervisor_1_id' => $this->otherDosen->id,
            'status' => ThesisStatus::IN_PROGRESS,
        ]);

        $this->otherChapter = ThesisChapter::create([
            'thesis_id' => $this->otherThesis->id,
            'chapter_template_id' => $tpl->id,
            'title' => 'BAB I: Pendahuluan',
            'order' => 1,
            'status' => ChapterStatus::NOT_STARTED,
        ]);
    }

    public function test_mahasiswa_can_access_all_student_pages_and_is_blocked_from_dosen_and_other_student_data(): void
    {
        $this->actingAs($this->student);

        $this->get(route('dashboard.mahasiswa'))->assertStatus(200)->assertSee($this->thesis->title);
        $this->get(route('theses.index'))->assertStatus(200)->assertSee($this->thesis->title);
        $this->get(route('theses.show', $this->thesis))->assertStatus(200);
        $this->get(route('chapters.show', $this->chapter1))->assertStatus(200);
        $this->get(route('supervision.index'))->assertStatus(200);
        $this->get(route('notifications.index'))->assertStatus(200);
        $this->get(route('chat.index'))->assertStatus(200);
        $this->get(route('profile.mahasiswa'))->assertStatus(200);

        // Role & ownership isolation
        $this->get(route('dashboard.dosen'))->assertStatus(403);
        $this->get(route('theses.show', $this->otherThesis))->assertStatus(403);
        $this->get(route('chapters.show', $this->otherChapter))->assertStatus(403);
    }

    public function test_mahasiswa_upload_and_revision_flow_preserves_old_versions_and_notifies_supervisor(): void
    {
        Storage::fake('local');
        $this->actingAs($this->student);

        // 1. Upload v1
        $file1 = UploadedFile::fake()->create('bab1_v1.pdf', 200, 'application/pdf');
        $this->post(route('chapters.upload', $this->chapter1), [
            'draft_file' => $file1,
            'notes' => 'Pengajuan awal BAB I',
        ])->assertRedirect(route('chapters.show', $this->chapter1));

        $this->chapter1->refresh();
        $this->assertEquals(ChapterStatus::IN_REVIEW, $this->chapter1->status);
        $this->assertEquals(1, $this->chapter1->versions()->count());
        $v1 = $this->chapter1->currentVersion;
        Storage::disk('local')->assertExists($v1->file_path);

        // Supervisor receives notification
        $this->assertTrue(Notification::where('user_id', $this->dosen1->id)->exists());

        // 2. Supervisor requests revision
        $this->actingAs($this->dosen1);
        $this->post(route('revisions.store', $this->chapter1), [
            'note' => 'Perbaiki rumusan masalah agar lebih spesifik.',
        ])->assertRedirect();

        $this->chapter1->refresh();
        $this->assertEquals(ChapterStatus::REVISION_REQUIRED, $this->chapter1->status);
        $revItem = RevisionItem::where('thesis_chapter_id', $this->chapter1->id)->firstOrFail();

        // 3. Student responds and uploads v2
        $this->actingAs($this->student);
        $this->post(route('revisions.respond', $revItem), [
            'student_response' => 'Sudah diperbaiki pada sub-bab 1.2.',
        ])->assertRedirect();
        $this->assertEquals('Sudah diperbaiki pada sub-bab 1.2.', $revItem->fresh()->student_response);

        $file2 = UploadedFile::fake()->create('bab1_v2.pdf', 250, 'application/pdf');
        $this->post(route('chapters.upload', $this->chapter1), [
            'draft_file' => $file2,
            'notes' => 'Perbaikan revisi rumusan masalah',
        ])->assertRedirect(route('chapters.show', $this->chapter1));

        $this->chapter1->refresh();
        $this->assertEquals(ChapterStatus::IN_REVIEW, $this->chapter1->status);
        $this->assertEquals(2, $this->chapter1->versions()->count());
        $v2 = $this->chapter1->currentVersion;
        $this->assertEquals(2, $v2->version_number);

        // Old v1 file & record must still exist
        Storage::disk('local')->assertExists($v1->file_path);
        Storage::disk('local')->assertExists($v2->file_path);
        $this->assertDatabaseHas('document_versions', ['id' => $v1->id, 'version_number' => 1]);
    }

    public function test_mahasiswa_can_book_only_own_supervisor_slot_and_chat_with_supervisor(): void
    {
        $ownSlot = SupervisionSlot::create([
            'supervisor_id' => $this->dosen1->id,
            'date' => now()->addDays(2)->toDateString(),
            'start_time' => '09:00',
            'end_time' => '10:00',
            'quota' => 2,
            'booked_count' => 0,
            'location_type' => 'offline',
            'room_or_link' => 'Ruang Dosen F7.3',
            'is_active' => true,
        ]);

        $otherSlot = SupervisionSlot::create([
            'supervisor_id' => $this->otherDosen->id,
            'date' => now()->addDays(3)->toDateString(),
            'start_time' => '10:00',
            'end_time' => '11:00',
            'quota' => 2,
            'booked_count' => 0,
            'location_type' => 'online',
            'room_or_link' => 'https://meet.google.com/abc',
            'is_active' => true,
        ]);

        $this->actingAs($this->student);

        // Book own supervisor slot succeeds
        $this->post(route('supervision.book', $ownSlot), [
            'topic' => 'Konsultasi BAB I',
            'student_notes' => 'Mohon arahan.',
        ])->assertRedirect();

        $this->assertDatabaseHas('supervision_requests', [
            'student_id' => $this->student->id,
            'supervisor_id' => $this->dosen1->id,
            'supervision_slot_id' => $ownSlot->id,
            'status' => SupervisionStatus::REQUESTED->value,
        ]);

        // Book non-supervisor slot fails validation
        $this->post(route('supervision.book', $otherSlot), [
            'topic' => 'Coba dosen lain',
        ])->assertSessionHasErrors('slot_id');

        // Chat with own supervisor succeeds
        $this->post(route('chat.store'), [
            'receiver_id' => $this->dosen1->id,
            'message' => 'Selamat pagi Pak, izin konfirmasi jadwal bimbingan.',
        ])->assertRedirect();

        $this->assertDatabaseHas('chat_messages', [
            'sender_id' => $this->student->id,
            'receiver_id' => $this->dosen1->id,
        ]);

        // Chat with non-supervisor is forbidden
        $this->post(route('chat.store'), [
            'receiver_id' => $this->otherDosen->id,
            'message' => 'Halo',
        ])->assertStatus(403);
    }
}

