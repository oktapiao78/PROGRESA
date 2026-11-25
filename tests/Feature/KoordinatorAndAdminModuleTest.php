<?php

namespace Tests\Feature;

use App\Enums\ChapterStatus;
use App\Enums\RiskLevel;
use App\Enums\ThesisStatus;
use App\Enums\UserRole;
use App\Models\ChapterTemplate;
use App\Models\StagnationLog;
use App\Models\StudyProgram;
use App\Models\Thesis;
use App\Models\ThesisChapter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KoordinatorAndAdminModuleTest extends TestCase
{
    use RefreshDatabase;

    protected StudyProgram $prodi;
    protected User $admin;
    protected User $koordinator;
    protected User $kaprodi;
    protected User $dosen;
    protected User $student;
    protected Thesis $thesis;

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

        $this->admin = User::factory()->create([
            'role' => UserRole::ADMIN,
            'study_program_id' => $this->prodi->id,
        ]);

        $this->koordinator = User::factory()->create([
            'role' => UserRole::KOORDINATOR,
            'study_program_id' => $this->prodi->id,
        ]);

        $this->kaprodi = User::factory()->create([
            'role' => UserRole::KAPRODI,
            'study_program_id' => $this->prodi->id,
        ]);

        $this->dosen = User::factory()->create([
            'role' => UserRole::DOSEN,
            'study_program_id' => $this->prodi->id,
        ]);

        $this->student = User::factory()->create([
            'role' => UserRole::MAHASISWA,
            'study_program_id' => $this->prodi->id,
        ]);

        $this->thesis = Thesis::create([
            'title' => 'Sistem Informasi Akademik',
            'abstract' => 'Deskripsi penelitian',
            'student_id' => $this->student->id,
            'supervisor_1_id' => $this->dosen->id,
            'study_program_id' => $this->prodi->id,
            'status' => ThesisStatus::IN_PROGRESS,
            'progress_percentage' => 40,
        ]);

        StagnationLog::create([
            'thesis_id' => $this->thesis->id,
            'days_inactive' => 18,
            'last_student_activity_at' => now()->subDays(18),
            'risk_level' => RiskLevel::HIGH,
            'notes' => 'Perlu ditinjau',
            'is_escalated' => false,
        ]);

        ChapterTemplate::create([
            'code' => 'BAB_1',
            'order' => 1,
            'title' => 'BAB I: Pendahuluan',
            'is_required' => true,
        ]);
    }

    public function test_koordinator_can_access_dashboard_and_monitoring()
    {
        $response = $this->actingAs($this->koordinator)->get(route('dashboard'));
        $response->assertRedirect(route('dashboard.koordinator'));

        $response = $this->actingAs($this->koordinator)->get(route('dashboard.koordinator'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->koordinator)->get(route('monitoring.stagnant'));
        $response->assertStatus(200);

        // Test escalate and resolve
        $response = $this->actingAs($this->koordinator)->post(route('approvals.escalate', $this->thesis));
        $response->assertRedirect();

        $response = $this->actingAs($this->koordinator)->post(route('approvals.resolve_escalation', $this->thesis));
        $response->assertRedirect();
    }

    public function test_kaprodi_can_access_dashboard_and_monitoring()
    {
        $response = $this->actingAs($this->kaprodi)->get(route('dashboard'));
        $response->assertRedirect(route('dashboard.kaprodi'));

        $response = $this->actingAs($this->kaprodi)->get(route('dashboard.kaprodi'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->kaprodi)->get(route('monitoring.delay_risk'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->kaprodi)->get(route('monitoring.supervisors'));
        $response->assertStatus(200);
    }

    public function test_admin_can_manage_users_and_chapter_templates()
    {
        $response = $this->actingAs($this->admin)->get(route('dashboard'));
        $response->assertRedirect(route('admin.users.index'));

        $response = $this->actingAs($this->admin)->get(route('admin.users.index'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin)->get(route('admin.chapters.index'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin)->get(route('admin.audit_logs'));
        $response->assertStatus(200);

        // Non-admin (mahasiswa) is blocked
        $response = $this->actingAs($this->student)->get(route('admin.users.index'));
        $response->assertStatus(403);
    }
}
