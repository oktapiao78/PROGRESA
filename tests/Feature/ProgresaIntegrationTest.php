<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProgresaIntegrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Skenario A: Mahasiswa
     * 1. Register dengan role Mahasiswa
     * 2. Tersimpan di database MySQL dengan password ter-hash
     * 3. Diarahkan ke login
     * 4. Login menggunakan email atau NIM
     * 5. Diarahkan ke Dashboard Mahasiswa
     * 6. Membuka profil dan melihat data yang benar
     * 7. Mengedit profil dan perubahan tersimpan di database
     */
    public function test_skenario_a_mahasiswa_register_login_dashboard_dan_edit_profile(): void
    {
        // 1. Register Mahasiswa
        $registerResponse = $this->post('/register', [
            'name' => 'Naura Ayunda',
            'nim_nip' => '233150601111055',
            'email' => 'naurayunda@student.ub.ac.id',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'role' => 'mahasiswa',
        ]);

        $registerResponse->assertRedirect('/login');
        $registerResponse->assertSessionHas('success');

        // Pastikan tersimpan di DB dengan role Mahasiswa dan password ter-hash
        $user = User::where('email', 'naurayunda@student.ub.ac.id')->first();
        $this->assertNotNull($user);
        $this->assertEquals('Naura Ayunda', $user->name);
        $this->assertEquals('233150601111055', $user->nim_nip);
        $this->assertEquals(UserRole::MAHASISWA, $user->role);
        $this->assertTrue(Hash::check('secret123', $user->password));

        // 2. Login menggunakan NIM
        $loginWithNim = $this->post('/login', [
            'login' => '233150601111055',
            'password' => 'secret123',
        ]);
        $loginWithNim->assertRedirect(route('dashboard.mahasiswa'));
        $this->assertAuthenticatedAs($user);

        // 3. Buka Dashboard Mahasiswa
        $dashboardResponse = $this->get('/dashboard/mahasiswa');
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Portal Tugas Akhir');

        // 4. Buka Profil Mahasiswa
        $profileResponse = $this->get('/profile/mahasiswa');
        $profileResponse->assertStatus(200);
        $profileResponse->assertSee('Naura Ayunda');
        $profileResponse->assertSee('233150601111055');
        $profileResponse->assertSee('naurayunda@student.ub.ac.id');

        // 5. Edit Profil Mahasiswa
        $updateResponse = $this->put('/profile', [
            'name' => 'Naura Ayunda Putri',
            'nim_nip' => '233150601111055',
            'email' => 'naurayunda@student.ub.ac.id',
            'phone' => '+62 8123456789',
            'gender' => 'Perempuan',
            'domicile' => 'Malang, Jawa Timur',
            'birth_date' => '12 Maret 2005',
            'university' => 'Universitas Brawijaya',
            'faculty' => 'Fakultas Ilmu Komputer',
            'major' => 'Pendidikan Teknologi Informasi',
            'year' => '2023',
            'semester' => '8',
            'gpa' => '3.99',
        ]);

        $updateResponse->assertSessionHas('success');

        // Periksa data tersimpan di database
        $user->refresh();
        $this->assertEquals('Naura Ayunda Putri', $user->name);
        $this->assertEquals('+62 8123456789', $user->phone);
        $this->assertEquals('Perempuan', $user->gender);
        $this->assertEquals('Malang, Jawa Timur', $user->domicile);
        $this->assertEquals('Pendidikan Teknologi Informasi', $user->major);
        $this->assertEquals('3.99', $user->gpa);
    }

    /**
     * Skenario B: Dosen
     * 1. Register dengan role Dosen
     * 2. Tersimpan di database dengan role Dosen
     * 3. Diarahkan ke login
     * 4. Login menggunakan NIP
     * 5. Diarahkan ke Dashboard Dosen
     * 6. Membuka profil Dosen dan melihat data
     * 7. Mengedit profil Dosen dan perubahan tersimpan
     */
    public function test_skenario_b_dosen_register_login_dashboard_dan_edit_profile(): void
    {
        // 1. Register Dosen
        $registerResponse = $this->post('/register', [
            'name' => 'Ir. Dr. Arka Mahendrata, S.T., S.kom.',
            'nim_nip' => '19870615202112030008',
            'email' => 'mahendrarka@ub.ac.id',
            'password' => 'dosenpass123',
            'password_confirmation' => 'dosenpass123',
            'role' => 'dosen',
        ]);

        $registerResponse->assertRedirect('/login');
        $registerResponse->assertSessionHas('success');

        $dosen = User::where('email', 'mahendrarka@ub.ac.id')->first();
        $this->assertNotNull($dosen);
        $this->assertEquals(UserRole::DOSEN, $dosen->role);
        $this->assertTrue(Hash::check('dosenpass123', $dosen->password));

        // 2. Login menggunakan Email
        $loginWithEmail = $this->post('/login', [
            'login' => 'mahendrarka@ub.ac.id',
            'password' => 'dosenpass123',
        ]);
        $loginWithEmail->assertRedirect(route('dashboard.dosen'));
        $this->assertAuthenticatedAs($dosen);

        // 3. Akses Dashboard Dosen
        $dashboardResponse = $this->get('/dashboard/dosen');
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Bimbingan Utama (P1)');

        // 4. Akses Profil Dosen
        $profileResponse = $this->get('/profile/dosen');
        $profileResponse->assertStatus(200);
        $profileResponse->assertSee('Ir. Dr. Arka Mahendrata, S.T., S.kom.');
        $profileResponse->assertSee('19870615202112030008');

        // 5. Edit Profil Dosen
        $updateResponse = $this->put('/profile', [
            'name' => 'Prof. Dr. Arka Mahendrata, S.T., M.Kom.',
            'nim_nip' => '19870615202112030008',
            'email' => 'mahendrarka@ub.ac.id',
            'phone' => '+62 8561234509876',
            'gender' => 'Laki-laki',
            'domicile' => 'Malang, Jawa Timur',
            'university' => 'Universitas Brawijaya',
            'faculty' => 'Fakultas Ilmu Komputer',
        ]);

        $updateResponse->assertSessionHas('success');

        $dosen->refresh();
        $this->assertEquals('Prof. Dr. Arka Mahendrata, S.T., M.Kom.', $dosen->name);
        $this->assertEquals('+62 8561234509876', $dosen->phone);
    }

    /**
     * Skenario C: Keamanan & Pembatasan Role
     * 1. Pengguna belum login dialihkan ke /login
     * 2. Mahasiswa tidak bisa mengakses Dashboard Dosen (403)
     * 3. Dosen tidak bisa mengakses Dashboard Mahasiswa (403)
     * 4. Password salah ditolak
     */
    public function test_skenario_c_keamanan_dan_hak_akses(): void
    {
        // 1. Belum login mengakses dashboard / profile
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/dashboard/mahasiswa')->assertRedirect('/login');
        $this->get('/dashboard/dosen')->assertRedirect('/login');
        $this->get('/profile/mahasiswa')->assertRedirect('/login');
        $this->get('/profile/dosen')->assertRedirect('/login');

        // Buat akun mahasiswa & dosen untuk testing otorisasi
        $mhs = User::create([
            'name' => 'Mahasiswa Test',
            'email' => 'mhs@test.com',
            'nim_nip' => '23000001',
            'role' => UserRole::MAHASISWA,
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        $dsn = User::create([
            'name' => 'Dosen Test',
            'email' => 'dsn@test.com',
            'nim_nip' => '198000001',
            'role' => UserRole::DOSEN,
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        // 2. Mahasiswa mencoba akses halaman Dosen
        $this->actingAs($mhs)->get('/dashboard/dosen')->assertStatus(403);
        $this->actingAs($mhs)->get('/profile/dosen')->assertStatus(403);

        // 3. Dosen mencoba akses halaman Mahasiswa
        $this->actingAs($dsn)->get('/dashboard/mahasiswa')->assertStatus(403);
        $this->actingAs($dsn)->get('/profile/mahasiswa')->assertStatus(403);

        // 4. Logout dosen
        $this->actingAs($dsn)->post('/logout')->assertRedirect('/login');
        $this->assertGuest();

        // 5. Password salah saat login
        $failedLogin = $this->from('/login')->post('/login', [
            'login' => 'mhs@test.com',
            'password' => 'passwordsalah',
        ]);
        $failedLogin->assertSessionHasErrors(['login']);
        $this->assertGuest();

        // 6. User mahasiswa login lalu logout
        $this->actingAs($mhs)->post('/logout')->assertRedirect('/login');
        $this->assertGuest();
        $this->assertGuest();
    }
}
