<?php

namespace Tests\Feature;

use App\Enums\SupervisionStatus;
use App\Enums\ThesisStatus;
use App\Enums\UserRole;
use App\Models\Notification;
use App\Models\StudyProgram;
use App\Models\SupervisionRequest;
use App\Models\SupervisionSlot;
use App\Models\Thesis;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupervisionIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected StudyProgram $prodi;
    protected User $dosen;
    protected User $student1;
    protected User $student2;

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
            'role' => UserRole::DOSEN,
            'nim_nip' => '19800101001',
            'study_program_id' => $this->prodi->id,
            'is_active' => true,
        ]);

        $this->student1 = User::factory()->create([
            'name' => 'Naura Ayunda',
            'role' => UserRole::MAHASISWA,
            'nim_nip' => '230101001',
            'study_program_id' => $this->prodi->id,
            'is_active' => true,
        ]);

        $this->student2 = User::factory()->create([
            'name' => 'Ellio Chandranata',
            'role' => UserRole::MAHASISWA,
            'nim_nip' => '230101002',
            'study_program_id' => $this->prodi->id,
            'is_active' => true,
        ]);

        Thesis::create([
            'student_id' => $this->student1->id,
            'study_program_id' => $this->prodi->id,
            'title' => 'Skripsi Mahasiswa 1',
            'academic_year' => '2025/2026',
            'supervisor_1_id' => $this->dosen->id,
            'status' => ThesisStatus::IN_PROGRESS,
        ]);

        Thesis::create([
            'student_id' => $this->student2->id,
            'study_program_id' => $this->prodi->id,
            'title' => 'Skripsi Mahasiswa 2',
            'academic_year' => '2025/2026',
            'supervisor_1_id' => $this->dosen->id,
            'status' => ThesisStatus::IN_PROGRESS,
        ]);
    }

    public function test_full_supervision_lifecycle_between_dosen_and_mahasiswa(): void
    {
        $date = now()->addDays(3)->toDateString();

        // 1. Dosen membuat slot bimbingan (quota = 1)
        $this->actingAs($this->dosen);
        $this->post(route('supervision.slots.store'), [
            'date' => $date,
            'start_time' => '09:00',
            'end_time' => '10:00',
            'quota' => 1,
            'location_type' => 'offline',
            'room_or_link' => 'Ruang Dosen F7.3',
        ])->assertRedirect();

        $slot = SupervisionSlot::where('supervisor_id', $this->dosen->id)->firstOrFail();
        $this->get(route('supervision.index'))->assertStatus(200)->assertSee('Ruang Dosen F7.3');

        // 2. Mahasiswa 1 melihat slot & mengajukan bimbingan
        $this->actingAs($this->student1);
        $this->get(route('supervision.index'))->assertStatus(200)->assertSee('Dr. Arka Mahendrata');

        $this->post(route('supervision.book', $slot), [
            'topic' => 'Konsultasi BAB III',
            'student_notes' => 'Mohon review diagram arsitektur.',
        ])->assertRedirect();

        $slot->refresh();
        $this->assertEquals(1, $slot->booked_count);
        $req1 = SupervisionRequest::where('student_id', $this->student1->id)->firstOrFail();
        $this->assertEquals(SupervisionStatus::REQUESTED, $req1->status);
        $this->assertTrue(Notification::where('user_id', $this->dosen->id)->exists());

        // 3. Slot penuh tidak dapat dipesan mahasiswa lain secara bersamaan
        $this->actingAs($this->student2);
        $this->post(route('supervision.book', $slot), [
            'topic' => 'Ikut pesan slot penuh',
        ])->assertSessionHasErrors('slot_id');
        $this->assertEquals(1, $slot->fresh()->booked_count);

        // 4. Penolakan oleh dosen wajib memiliki alasan
        $this->actingAs($this->dosen);
        $this->post(route('supervision.respond', $req1), [
            'status' => 'rejected',
            'supervisor_notes' => '',
        ])->assertSessionHasErrors('supervisor_notes');

        // 5. Dosen menolak dengan alasan -> kuota kembali 0, mahasiswa melihat alasan penolakan
        $this->post(route('supervision.respond', $req1), [
            'status' => 'rejected',
            'supervisor_notes' => 'Saya ada rapat mendadak pada jam tersebut.',
        ])->assertRedirect();

        $this->assertEquals(SupervisionStatus::REJECTED, $req1->fresh()->status);
        $this->assertEquals(0, $slot->fresh()->booked_count);

        $this->actingAs($this->student1);
        $this->get(route('supervision.index'))
            ->assertStatus(200)
            ->assertSee('Ditolak')
            ->assertSee('Saya ada rapat mendadak pada jam tersebut.');

        // 6. Mahasiswa 1 mengajukan ulang di slot yang kini tersedia kembali
        $this->post(route('supervision.book', $slot), [
            'topic' => 'Konsultasi Ulang BAB III',
        ])->assertRedirect();

        $req2 = SupervisionRequest::where('student_id', $this->student1->id)
            ->where('status', SupervisionStatus::REQUESTED)
            ->firstOrFail();
        $this->assertEquals(1, $slot->fresh()->booked_count);

        // 7. Dosen menyetujui pengajuan -> jadwal dosen & mahasiswa sinkron
        $this->actingAs($this->dosen);
        $this->post(route('supervision.respond', $req2), [
            'status' => 'accepted',
            'supervisor_notes' => 'Silakan datang tepat waktu.',
        ])->assertRedirect();

        $this->assertEquals(SupervisionStatus::ACCEPTED, $req2->fresh()->status);
        $this->get(route('supervision.index'))
            ->assertStatus(200)
            ->assertSee('Konsultasi Ulang BAB III');

        $this->actingAs($this->student1);
        $this->get(route('supervision.index'))
            ->assertStatus(200)
            ->assertSee('Jadwal Bimbingan Mendatang (Disetujui)')
            ->assertSee('Konsultasi Ulang BAB III');
        $this->get(route('dashboard.mahasiswa'))
            ->assertStatus(200)
            ->assertSee('Konsultasi Ulang BAB III');

        // 8. Dosen menyelesaikan sesi bimbingan & mengisi berita acara
        $this->actingAs($this->dosen);
        $this->post(route('supervision.complete', $req2), [
            'meeting_summary' => 'Diagram arsitektur sudah sesuai.',
            'action_items' => 'Lanjut ke implementasi BAB IV.',
        ])->assertRedirect();

        $req2->refresh();
        $this->assertEquals(SupervisionStatus::COMPLETED, $req2->status);
        $this->assertNotNull($req2->completed_at);

        $this->get(route('supervision.index'))
            ->assertStatus(200)
            ->assertSee('Diagram arsitektur sudah sesuai.');

        $this->actingAs($this->student1);
        $this->get(route('supervision.index'))
            ->assertStatus(200)
            ->assertSee('Diagram arsitektur sudah sesuai.')
            ->assertSee('Lanjut ke implementasi BAB IV.');
    }
}

