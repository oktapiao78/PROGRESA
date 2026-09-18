<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\StudyProgram;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $studentUser;
    protected User $supervisorUser;
    protected User $coordinatorUser;
    protected User $inactiveUser;

    protected function setUp(): void
    {
        parent::setUp();

        $studyProgram = StudyProgram::create([
            'name' => 'Teknik Informatika',
            'code' => 'TI',
        ]);

        $this->studentUser = User::create([
            'name' => 'Budi Mahasiswa',
            'email' => 'budi@progresa.ac.id',
            'nim_nip' => '230101001',
            'password' => Hash::make('password123'),
            'role' => UserRole::MAHASISWA,
            'study_program_id' => $studyProgram->id,
            'is_active' => true,
        ]);

        $this->supervisorUser = User::create([
            'name' => 'Prof. Hendra Wijaya',
            'email' => 'hendra@progresa.ac.id',
            'nim_nip' => '198003152006041003',
            'password' => Hash::make('password123'),
            'role' => UserRole::DOSEN,
            'study_program_id' => $studyProgram->id,
            'is_active' => true,
        ]);

        $this->coordinatorUser = User::create([
            'name' => 'Ahmad Fauzi',
            'email' => 'koordinator@progresa.ac.id',
            'nim_nip' => '198205122008121002',
            'password' => Hash::make('password123'),
            'role' => UserRole::KOORDINATOR,
            'study_program_id' => $studyProgram->id,
            'is_active' => true,
        ]);

        $this->inactiveUser = User::create([
            'name' => 'User Nonaktif',
            'email' => 'nonaktif@progresa.ac.id',
            'nim_nip' => '230101999',
            'password' => Hash::make('password123'),
            'role' => UserRole::MAHASISWA,
            'study_program_id' => $studyProgram->id,
            'is_active' => false,
        ]);
    }

    /**
     * Skenario A: Login menggunakan NIM + password benar → berhasil
     */
    public function test_user_can_login_with_nim_and_correct_password(): void
    {
        $response = $this->post('/login', [
            'login' => '230101001',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($this->studentUser);
    }

    /**
     * Skenario B: Login menggunakan NIP + password benar → berhasil
     */
    public function test_user_can_login_with_nip_and_correct_password(): void
    {
        $response = $this->post('/login', [
            'login' => '198003152006041003',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($this->supervisorUser);
    }

    /**
     * Skenario C: Login menggunakan email + password benar → berhasil
     */
    public function test_user_can_login_with_email_and_correct_password(): void
    {
        $response = $this->post('/login', [
            'login' => 'budi@progresa.ac.id',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($this->studentUser);
    }

    /**
     * Email login bersifat case-insensitive
     */
    public function test_user_can_login_with_uppercase_email(): void
    {
        $response = $this->post('/login', [
            'login' => 'BUDI@PROGRESA.AC.ID',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($this->studentUser);
    }

    /**
     * Login dengan whitespace pada identifier otomatis ditrim
     */
    public function test_user_can_login_with_whitespace_around_identifier(): void
    {
        $response = $this->post('/login', [
            'login' => '   230101001   ',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($this->studentUser);
    }

    /**
     * Login juga dapat menggunakan field 'identifier'
     */
    public function test_user_can_login_using_identifier_field(): void
    {
        $response = $this->post('/login', [
            'identifier' => '198205122008121002',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($this->coordinatorUser);
    }

    /**
     * Skenario D: Password salah → gagal dengan pesan umum
     */
    public function test_login_fails_with_incorrect_password_and_shows_generic_error(): void
    {
        $response = $this->from('/login')->post('/login', [
            'login' => '230101001',
            'password' => 'passwordsalah',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors([
            'login' => 'NIM / NIP / Email atau kata sandi tidak cocok.',
        ]);
        $this->assertGuest();
    }

    /**
     * Skenario E: Identifier tidak terdaftar → gagal dengan pesan umum (tanpa expose eksistensi)
     */
    public function test_login_fails_with_unregistered_identifier_and_shows_generic_error(): void
    {
        $response = $this->from('/login')->post('/login', [
            'login' => '999999999',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors([
            'login' => 'NIM / NIP / Email atau kata sandi tidak cocok.',
        ]);
        $this->assertGuest();
    }

    /**
     * Akun nonaktif tidak diizinkan masuk
     */
    public function test_login_fails_for_inactive_user_with_appropriate_message(): void
    {
        $response = $this->from('/login')->post('/login', [
            'login' => '230101999',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['login']);
        $this->assertGuest();
    }

    /**
     * Validasi input mewajibkan identifier dan password
     */
    public function test_login_validation_requires_identifier_and_password(): void
    {
        $response = $this->from('/login')->post('/login', [
            'login' => '',
            'password' => '',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['login', 'password']);
        $this->assertGuest();
    }

    /**
     * Skenario F: User belum login membuka / dan /dashboard → redirect /login
     */
    public function test_unauthenticated_user_accessing_root_is_redirected_to_login(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/login');
    }

    public function test_unauthenticated_user_accessing_dashboard_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_unauthenticated_user_accessing_protected_routes_is_redirected_to_login(): void
    {
        $protectedUrls = [
            '/dashboard',
            '/mahasiswa',
            '/dosen',
            '/koordinator',
            '/kaprodi',
            '/theses',
            '/supervision',
        ];

        foreach ($protectedUrls as $url) {
            $response = $this->get($url);
            $response->assertRedirect('/login');
        }
    }

    /**
     * Skenario G: User berhasil login → dapat membuka /dashboard dan tidak melihat form login
     */
    public function test_authenticated_user_sees_dashboard_and_no_login_form(): void
    {
        $response = $this->actingAs($this->studentUser)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Budi Mahasiswa');
        $response->assertDontSee('Masuk ke Sistem');
        $response->assertDontSee('Akun Demo Cepat');
    }

    public function test_authenticated_user_accessing_login_is_redirected_to_dashboard(): void
    {
        $response = $this->actingAs($this->studentUser)->get('/login');
        $response->assertRedirect('/dashboard');
    }

    public function test_authenticated_user_accessing_root_is_redirected_to_dashboard(): void
    {
        $response = $this->actingAs($this->studentUser)->get('/');
        $response->assertRedirect('/dashboard');
    }

    /**
     * Skenario H: Logout → session berakhir dan kembali ke /login
     */
    public function test_authenticated_user_can_logout_and_is_redirected_to_login(): void
    {
        $response = $this->actingAs($this->studentUser)->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    /**
     * Halaman login hanya menampilkan formulir autentikasi tanpa komponen dashboard
     */
    public function test_login_page_renders_only_authentication_without_dashboard_components(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Selamat Datang Kembali');
        $response->assertSee('Masuk ke Sistem');
        $response->assertDontSee('Total Progres Bab');
        $response->assertDontSee('Jadwal Bimbingan');
        $response->assertDontSee('Stagnansi & Eskalasi');
    }
}
