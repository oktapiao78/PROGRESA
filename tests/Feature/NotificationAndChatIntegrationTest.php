<?php

namespace Tests\Feature;

use App\Enums\ChapterStatus;
use App\Enums\ThesisStatus;
use App\Enums\UserRole;
use App\Models\ChapterTemplate;
use App\Models\ChatMessage;
use App\Models\Notification;
use App\Models\RevisionItem;
use App\Models\StudyProgram;
use App\Models\SupervisionRequest;
use App\Models\SupervisionSlot;
use App\Models\Thesis;
use App\Models\ThesisChapter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NotificationAndChatIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected StudyProgram $prodi;
    protected User $dosen;
    protected User $unrelatedDosen;
    protected User $student;
    protected Thesis $thesis;
    protected ThesisChapter $chapter;

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

        $this->unrelatedDosen = User::factory()->create([
            'name' => 'Dr. Dosen Lain',
            'role' => UserRole::DOSEN,
            'nim_nip' => '19800101099',
            'study_program_id' => $this->prodi->id,
            'is_active' => true,
        ]);

        $this->student = User::factory()->create([
            'name' => 'Naura Ayunda',
            'role' => UserRole::MAHASISWA,
            'nim_nip' => '230101001',
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
            'title' => 'Sistem Informasi PROGRESA',
            'academic_year' => '2025/2026',
            'supervisor_1_id' => $this->dosen->id,
            'status' => ThesisStatus::IN_PROGRESS,
        ]);

        $this->chapter = ThesisChapter::create([
            'thesis_id' => $this->thesis->id,
            'chapter_template_id' => $tpl->id,
            'title' => 'BAB I: Pendahuluan',
            'order' => 1,
            'status' => ChapterStatus::NOT_STARTED,
        ]);
    }

    public function test_all_dosen_and_mahasiswa_notification_triggers_and_read_status(): void
    {
        Storage::fake('local');

        $slot = SupervisionSlot::create([
            'supervisor_id' => $this->dosen->id,
            'date' => now()->addDays(2)->toDateString(),
            'start_time' => '09:00',
            'end_time' => '10:00',
            'quota' => 3,
            'booked_count' => 0,
            'location_type' => 'offline',
            'room_or_link' => 'R. F7.3',
            'is_active' => true,
        ]);

        // --- MAHASISWA TRIGGERS DOSEN NOTIFICATIONS ---
        $this->actingAs($this->student);

        // 1. Pengajuan bimbingan baru
        $this->post(route('supervision.book', $slot), ['topic' => 'Konsultasi Awal'])->assertRedirect();
        $req1 = SupervisionRequest::latest('id')->firstOrFail();

        // 2. Mengumpulkan bab baru (v1)
        $this->post(route('chapters.upload', $this->chapter), [
            'draft_file' => UploadedFile::fake()->create('bab1_v1.pdf', 100, 'application/pdf'),
            'notes' => 'Draft pertama',
        ])->assertRedirect();

        // --- DOSEN TRIGGERS MAHASISWA NOTIFICATIONS ---
        $this->actingAs($this->dosen);

        // 3. Pengajuan disetujui
        $this->post(route('supervision.respond', $req1), [
            'status' => 'accepted',
            'supervisor_notes' => 'Disetujui.',
        ])->assertRedirect();

        // 4. Jadwal berubah (rescheduled)
        $this->post(route('supervision.respond', $req1), [
            'status' => 'rescheduled',
            'supervisor_notes' => 'Diundur 30 menit.',
        ])->assertRedirect();

        // Pengajuan kedua untuk ditolak
        $this->actingAs($this->student);
        $this->post(route('supervision.book', $slot), ['topic' => 'Konsultasi Kedua'])->assertRedirect();
        $req2 = SupervisionRequest::latest('id')->firstOrFail();

        // 5. Pengajuan ditolak
        $this->actingAs($this->dosen);
        $this->post(route('supervision.respond', $req2), [
            'status' => 'rejected',
            'supervisor_notes' => 'Silakan gabung di sesi pertama saja.',
        ])->assertRedirect();

        // 6. Catatan revisi
        $this->post(route('revisions.store', $this->chapter), [
            'note' => 'Tambahkan referensi terbaru.',
        ])->assertRedirect();
        $revItem = RevisionItem::latest('id')->firstOrFail();

        // 7. Mahasiswa mengirim tanggapan revisi & upload v2 -> notifikasi revisi ke dosen
        $this->actingAs($this->student);
        $this->post(route('revisions.respond', $revItem), [
            'student_response' => 'Referensi sudah ditambahkan.',
        ])->assertRedirect();
        $this->post(route('chapters.upload', $this->chapter), [
            'draft_file' => UploadedFile::fake()->create('bab1_v2.pdf', 120, 'application/pdf'),
            'notes' => 'Perbaikan revisi',
        ])->assertRedirect();

        // 8. Dosen menyetujui revisi & memberikan hasil review
        $this->actingAs($this->dosen);
        $this->post(route('revisions.toggle', $revItem))->assertRedirect();
        $this->post(route('approvals.stage', $this->chapter), [
            'stage' => 'pembimbing_1',
            'status' => 'approved',
            'feedback' => 'BAB I disetujui.',
        ])->assertRedirect();

        // Verifikasi notifikasi Dosen
        $dosenNotifTitles = Notification::where('user_id', $this->dosen->id)->pluck('title')->implode(' | ');
        $this->assertStringContainsString('Pengajuan Bimbingan Baru', $dosenNotifTitles);
        $this->assertStringContainsString('Unggahan Bab Baru', $dosenNotifTitles);
        $this->assertStringContainsString('Tanggapan Revisi', $dosenNotifTitles);
        $this->assertStringContainsString('Unggahan Revisi', $dosenNotifTitles);

        // Verifikasi notifikasi Mahasiswa
        $studentNotifTitles = Notification::where('user_id', $this->student->id)->pluck('title')->implode(' | ');
        $this->assertStringContainsString('Disetujui / Terjadwal', $studentNotifTitles);
        $this->assertStringContainsString('Dijadwalkan Ulang', $studentNotifTitles);
        $this->assertStringContainsString('Ditolak', $studentNotifTitles);
        $this->assertStringContainsString('Catatan Revisi Baru', $studentNotifTitles);
        $this->assertStringContainsString('Revisi Disetujui', $studentNotifTitles);
        $this->assertStringContainsString('Hasil Review', $studentNotifTitles);

        // Verifikasi baca satu notifikasi & tandai semua dibaca
        $this->actingAs($this->student);
        $this->get(route('notifications.index'))->assertStatus(200)->assertSee('Belum Dibaca');

        $firstNotif = Notification::where('user_id', $this->student->id)->firstOrFail();
        $this->post(route('notifications.read', $firstNotif))->assertRedirect($firstNotif->link);
        $this->assertTrue($firstNotif->fresh()->is_read);

        $this->post(route('notifications.read_all'))->assertRedirect();
        $this->assertEquals(0, Notification::where('user_id', $this->student->id)->where('is_read', false)->count());
    }

    public function test_bidirectional_chat_read_status_and_relationship_authorization(): void
    {
        // Mahasiswa mengirim pesan ke dosen pembimbing
        $this->actingAs($this->student);
        $this->post(route('chat.store'), [
            'receiver_id' => $this->dosen->id,
            'message' => 'Selamat pagi Pak, mohon arahan BAB I.',
        ])->assertRedirect();

        $msg1 = ChatMessage::firstOrFail();
        $this->assertFalse($msg1->is_read);
        $this->assertTrue(Notification::where('user_id', $this->dosen->id)->where('type', 'chat')->exists());

        // Sebelum dibaca dosen, mahasiswa melihat status Terkirim
        $this->get(route('chat.index', ['user_id' => $this->dosen->id]))
            ->assertStatus(200)
            ->assertSee('Terkirim');

        // Dosen membuka chat -> pesan otomatis ditandai sudah dibaca
        $this->actingAs($this->dosen);
        $this->get(route('chat.index', ['user_id' => $this->student->id]))
            ->assertStatus(200)
            ->assertSee('Selamat pagi Pak, mohon arahan BAB I.');

        $this->assertTrue($msg1->fresh()->is_read);

        // Mahasiswa melihat status berubah menjadi Dibaca
        $this->actingAs($this->student);
        $this->get(route('chat.index', ['user_id' => $this->dosen->id]))
            ->assertStatus(200)
            ->assertSee('Dibaca');

        // Dosen membalas pesan ke mahasiswa
        $this->actingAs($this->dosen);
        $this->post(route('chat.store'), [
            'receiver_id' => $this->student->id,
            'message' => 'Baik, silakan lanjutkan revisinya.',
        ])->assertRedirect();

        $this->assertTrue(Notification::where('user_id', $this->student->id)->where('type', 'chat')->exists());

        // Mahasiswa tidak dapat chat dengan dosen yang bukan pembimbingnya
        $this->actingAs($this->student);
        $this->post(route('chat.store'), [
            'receiver_id' => $this->unrelatedDosen->id,
            'message' => 'Pesan ilegal',
        ])->assertStatus(403);
    }
}

