<?php

namespace Tests\Feature;

use App\Enums\ChapterStatus;
use App\Enums\ThesisStatus;
use App\Enums\UserRole;
use App\Models\ChapterTemplate;
use App\Models\StudyProgram;
use App\Models\SupervisionSlot;
use App\Models\Thesis;
use App\Models\ThesisChapter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DosenModuleTest extends TestCase
{
    use RefreshDatabase;

    protected StudyProgram $prodi;
    protected User $dosen;
    protected User $otherDosen;
    protected User $student;
    protected User $otherStudent;
    protected Thesis $thesis;
    protected Thesis $otherThesis;
    protected ThesisChapter $chapter;
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

        $this->dosen = User::factory()->create([
            'name' => 'Dr. Arka Mahendrata',
            'email' => 'dosen@progresa.ac.id',
            'nim_nip' => '19800101001',
            'role' => UserRole::DOSEN,
            'study_program_id' => $this->prodi->id,
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        $this->otherDosen = User::factory()->create([
            'name' => 'Dr. Dosen Lain',
            'nim_nip' => '19800101002',
            'role' => UserRole::DOSEN,
            'study_program_id' => $this->prodi->id,
            'is_active' => true,
        ]);

        $this->student = User::factory()->create([
            'name' => 'Naura Ayunda',
            'email' => 'mahasiswa@progresa.ac.id',
            'nim_nip' => '230101001',
            'role' => UserRole::MAHASISWA,
            'study_program_id' => $this->prodi->id,
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        $this->otherStudent = User::factory()->create([
            'name' => 'Mahasiswa Lain',
            'nim_nip' => '230101002',
            'role' => UserRole::MAHASISWA,
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
            'title' => 'Skripsi Bimbingan Dosen 1',
            'academic_year' => '2025/2026',
            'supervisor_1_id' => $this->dosen->id,
            'status' => ThesisStatus::IN_PROGRESS,
        ]);

        $this->chapter = ThesisChapter::create([
            'thesis_id' => $this->thesis->id,
            'chapter_template_id' => $tpl->id,
            'title' => 'BAB I: Pendahuluan',
            'order' => 1,
            'status' => ChapterStatus::IN_REVIEW,
        ]);

        $this->otherThesis = Thesis::create([
            'student_id' => $this->otherStudent->id,
            'study_program_id' => $this->prodi->id,
            'title' => 'Skripsi Dosen Lain',
            'academic_year' => '2025/2026',
            'supervisor_1_id' => $this->otherDosen->id,
            'status' => ThesisStatus::IN_PROGRESS,
        ]);

        $this->otherChapter = ThesisChapter::create([
            'thesis_id' => $this->otherThesis->id,
            'chapter_template_id' => $tpl->id,
            'title' => 'BAB I: Pendahuluan',
            'order' => 1,
            'status' => ChapterStatus::IN_REVIEW,
        ]);
    }

    public function test_login_redirect_and_dosen_module_pages_with_isolation(): void
    {
        // Login Dosen
        $this->post('/login', [
            'identifier' => 'dosen@progresa.ac.id',
            'password' => 'password123',
        ])->assertRedirect(route('dashboard.dosen'));

        $this->get(route('dashboard.dosen'))
            ->assertStatus(200)
            ->assertSee($this->thesis->title)
            ->assertSee($this->chapter->title);

        $this->get(route('theses.index'))->assertStatus(200)->assertSee($this->thesis->title)->assertDontSee($this->otherThesis->title);
        $this->get(route('theses.show', $this->thesis))->assertStatus(200);
        $this->get(route('chapters.show', $this->chapter))->assertStatus(200);
        $this->get(route('supervision.index'))->assertStatus(200);
        $this->get(route('notifications.index'))->assertStatus(200);
        $this->get(route('chat.index'))->assertStatus(200);
        $this->get(route('profile.dosen'))->assertStatus(200);

        // Isolation checks
        $this->get(route('dashboard.mahasiswa'))->assertStatus(403);
        $this->get(route('theses.show', $this->otherThesis))->assertStatus(403);
        $this->get(route('chapters.show', $this->otherChapter))->assertStatus(403);
    }

    public function test_dosen_can_review_chapter_manage_slots_and_update_profile(): void
    {
        $this->actingAs($this->dosen);

        // Create and delete unbooked slot
        $this->post(route('supervision.slots.store'), [
            'date' => now()->addDays(4)->toDateString(),
            'start_time' => '13:00',
            'end_time' => '14:00',
            'quota' => 2,
            'location_type' => 'online',
            'room_or_link' => 'https://meet.google.com/progresa',
        ])->assertRedirect();

        $slot = SupervisionSlot::where('supervisor_id', $this->dosen->id)->firstOrFail();
        $this->delete(route('supervision.slots.destroy', $slot))->assertRedirect();
        $this->assertDatabaseMissing('supervision_slots', ['id' => $slot->id]);

        // Approve chapter
        $this->post(route('approvals.stage', $this->chapter), [
            'stage' => 'pembimbing_1',
            'status' => 'approved',
            'feedback' => 'Sudah lengkap dan rapi.',
        ])->assertRedirect();

        $this->assertEquals(ChapterStatus::APPROVED, $this->chapter->fresh()->status);

        // Update profile
        $this->put(route('profile.update'), [
            'name' => 'Dr. Arka Mahendrata Updated',
            'email' => 'dosen@progresa.ac.id',
            'nim_nip' => '19800101001',
        ])->assertRedirect();

        $this->assertEquals('Dr. Arka Mahendrata Updated', $this->dosen->fresh()->name);
    }
}
