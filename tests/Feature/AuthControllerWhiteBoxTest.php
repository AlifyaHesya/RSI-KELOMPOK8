<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\KodeAkses;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;


class AuthControllerWhiteBoxTest extends TestCase
{
    use RefreshDatabase;

    private const URL_LOGOUT = '/logout'; // sesuaikan

    private function buatKodeAksesPic(string $kode = 'PIC-VALID-123'): string
    {
        KodeAkses::create(['kode' => $kode, 'role' => Role::Pic, 'aktif' => true]);

        return $kode;
    }

    private function dataDasar(array $override = []): array
    {
        return array_merge([
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'username' => 'budi_s',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ], $override);
    }

    private function dataMahasiswa(array $override = []): array
    {
        return $this->dataDasar(array_merge([
            'nim' => '225150200111001',
            'program_studi' => 'Teknik Informatika',
        ], $override));
    }

    private function dataPic(array $override = []): array
    {
        return $this->dataDasar(array_merge([
            'biro' => 'Biro Kemahasiswaan',
        ], $override));
    }
    public function test_wb_tampil_login(): void
    {
        $this->get(route('login'))->assertOk()->assertViewIs('auth.login');
    }

    public function test_wb_pilih_peran(): void
    {
        $this->get(route('register'))
            ->assertOk()
            ->assertViewIs('auth.pilih-role')
            ->assertViewHas('roles', Role::cases());
    }

    /** Jalur 1: D1=true (email), D2=false (berhasil) */
    public function test_wb_masuk_dengan_email_berhasil(): void
    {
        $user = User::factory()->create([
            'email' => 'budi@example.com',
            'username' => 'budi_s',
            'password' => 'Password123',
        ]);

        $this->post(route('login'), ['login' => 'budi@example.com', 'password' => 'Password123'])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    /** Jalur 2: D1=false (username), D2=false (berhasil) */
    public function test_wb_masuk_dengan_username_berhasil(): void
    {
        $user = User::factory()->create([
            'email' => 'budi@example.com',
            'username' => 'budi_s',
            'password' => 'Password123',
        ]);

        $this->post(route('login'), ['login' => 'budi_s', 'password' => 'Password123'])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    /** Jalur 3: D2=true (kredensial salah) */
    public function test_wb_masuk_gagal_password_salah(): void
    {
        User::factory()->create([
            'email' => 'budi@example.com',
            'username' => 'budi_s',
            'password' => 'Password123',
        ]);

        $this->from(route('login'))
            ->post(route('login'), ['login' => 'budi@example.com', 'password' => 'salah'])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['login' => 'Username/email atau kata sandi salah']);

        $this->assertGuest();
    }

    /** Jalur 3 (varian username tidak terdaftar) */
    public function test_wb_masuk_gagal_username_tidak_terdaftar(): void
    {
        $this->post(route('login'), ['login' => 'tidakada', 'password' => 'Password123'])
            ->assertSessionHasErrors('login');

        $this->assertGuest();
    }

    /** Jalur validasi: field wajib kosong */
    public function test_wb_masuk_validasi_field_kosong(): void
    {
        $this->post(route('login'), [])
            ->assertSessionHasErrors([
                'login' => 'Username / email wajib diisi',
                'password' => 'Kata sandi wajib diisi',
            ]);
    }

    /** Jalur 1: D1=false (MHS) */
    public function test_wb_kode_akses_mahasiswa_tanpa_kode(): void
    {
        $this->post(route('register.kode'), ['role' => 'MHS'])
            ->assertRedirect(route('register.form', 'MHS'));

        $this->assertNull(session('kode_akses_valid'));
    }

    /** Jalur 2: D1=true, D2=true (kode salah) */
    public function test_wb_kode_akses_pic_kode_salah(): void
    {
        $this->post(route('register.kode'), ['role' => 'PIC', 'kode_akses' => 'SALAH'])
            ->assertSessionHasErrors(['kode_akses' => 'Kode akses tidak valid']);

        $this->assertNull(session('kode_akses_valid'));
    }

    /** Jalur 3: D1=true, D2=false (kode benar) */
    public function test_wb_kode_akses_pic_kode_benar(): void
    {
        $kode = $this->buatKodeAksesPic();

        $this->post(route('register.kode'), ['role' => 'PIC', 'kode_akses' => $kode])
            ->assertRedirect(route('register.form', 'PIC'));

        $this->assertSame('PIC', session('kode_akses_valid'));
    }

    /** Cabang validasi: PIC tanpa kode_akses (required_unless) */
    public function test_wb_kode_akses_pic_kode_kosong(): void
    {
        $this->post(route('register.kode'), ['role' => 'PIC'])
            ->assertSessionHasErrors(['kode_akses' => 'Kode akses wajib diisi untuk PIC']);
    }

    /** Cabang validasi: role di luar MHS/PIC */
    public function test_wb_kode_akses_role_tidak_valid(): void
    {
        $this->post(route('register.kode'), ['role' => 'ADM'])
            ->assertSessionHasErrors('role');
    }

    /** Jalur 1: D1=true -> 404 */
    public function test_wb_form_registrasi_role_tidak_dikenal(): void
    {
        $this->get(route('register.form', 'XYZ'))->assertNotFound();
    }

    /** Jalur 2: D1=false, D2=false (MHS, kondisi kiri || true) */
    public function test_wb_form_registrasi_mahasiswa_tampil(): void
    {
        $this->get(route('register.form', 'MHS'))
            ->assertOk()
            ->assertViewIs('auth.register')
            ->assertViewHas('role', Role::Mahasiswa);
    }

    /** Jalur 3: D2=true (PIC tanpa sesi, kedua sisi || false) */
    public function test_wb_form_registrasi_pic_tanpa_sesi_ditolak(): void
    {
        $this->get(route('register.form', 'PIC'))
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors(['kode_akses' => 'Masukkan kode akses terlebih dahulu']);
    }

    /** Jalur 4: PIC, sisi kanan || true (sesi cocok) */
    public function test_wb_form_registrasi_pic_dengan_sesi_valid(): void
    {
        $this->withSession(['kode_akses_valid' => 'PIC'])
            ->get(route('register.form', 'PIC'))
            ->assertOk()
            ->assertViewHas('role', Role::Pic);
    }

    /** Cabang === false: sesi ada tapi nilainya tidak sama dengan role */
    public function test_wb_form_registrasi_pic_sesi_nilai_salah(): void
    {
        $this->withSession(['kode_akses_valid' => 'MHS'])
            ->get(route('register.form', 'PIC'))
            ->assertRedirect(route('register'));
    }

    /** Jalur 1: D1=true */
    public function test_wb_registrasi_role_tidak_dikenal(): void
    {
        $this->post(route('register.form', 'XYZ'), $this->dataDasar())->assertNotFound();
        $this->assertDatabaseCount('users', 0);
    }

    /** Jalur 2: D2=true (PIC tanpa sesi) */
    public function test_wb_registrasi_pic_tanpa_sesi_ditolak(): void
    {
        $this->post(route('register.form', 'PIC'), $this->dataPic())
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors('kode_akses');

        $this->assertDatabaseCount('users', 0);
    }

    /** Jalur 3: D3=true, D5=true (Mahasiswa sukses) */
    public function test_wb_registrasi_mahasiswa_berhasil(): void
    {
        $this->post(route('register.form', 'MHS'), $this->dataMahasiswa())
            ->assertRedirect(route('login'))
            ->assertSessionHas('status', 'Registrasi berhasil');

        $user = User::where('email', 'budi@example.com')->firstOrFail();

        $this->assertSame(Role::Mahasiswa, $user->role);
        $this->assertTrue(Hash::check('Password123', $user->password));
        $this->assertSame('225150200111001', $user->mahasiswa->nim);
        $this->assertSame('Teknik Informatika', $user->mahasiswa->program_studi);
    }

    /** Jalur 4: D3=false, D4=true, D5=false, D6=true (PIC sukses) */
    public function test_wb_registrasi_pic_berhasil_dan_sesi_dihapus(): void
    {
        $this->withSession(['kode_akses_valid' => 'PIC'])
            ->post(route('register.form', 'PIC'), $this->dataPic())
            ->assertRedirect(route('login'))
            ->assertSessionHas('status', 'Registrasi berhasil')
            ->assertSessionMissing('kode_akses_valid');

        $user = User::where('email', 'budi@example.com')->firstOrFail();

        $this->assertSame(Role::Pic, $user->role);
        $this->assertSame('Biro Kemahasiswaan', $user->pic->biro);
    }

    /** Aturan nim/program_studi hanya berlaku di cabang Mahasiswa (D3=true) */
    public function test_wb_registrasi_mahasiswa_tanpa_nim_dan_prodi(): void
    {
        $this->post(route('register.form', 'MHS'), $this->dataDasar())
            ->assertSessionHasErrors([
                'nim' => 'NIM wajib diisi',
                'program_studi' => 'Program studi wajib diisi',
            ]);

        $this->assertDatabaseCount('users', 0);
    }

    /** Aturan biro hanya berlaku di cabang PIC (D4=true) */
    public function test_wb_registrasi_pic_tanpa_biro(): void
    {
        $this->withSession(['kode_akses_valid' => 'PIC'])
            ->post(route('register.form', 'PIC'), $this->dataDasar())
            ->assertSessionHasErrors(['biro' => 'Biro/departemen wajib diisi']);

        $this->assertDatabaseCount('users', 0);
    }

    /** Mahasiswa tidak dikenai aturan biro (D4 tidak dievaluasi) */
    public function test_wb_registrasi_mahasiswa_tidak_butuh_biro(): void
    {
        $this->post(route('register.form', 'MHS'), $this->dataMahasiswa(['biro' => '']))
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('login'));
    }

    /** Validasi unik: email, username, NIM */
    public function test_wb_registrasi_data_duplikat(): void
    {
        $lama = User::factory()->create([
            'email' => 'budi@example.com',
            'username' => 'budi_s',
            'role' => Role::Mahasiswa,
        ]);
        $lama->mahasiswa()->create(['nim' => '225150200111001', 'program_studi' => 'TI']);

        $this->post(route('register.form', 'MHS'), $this->dataMahasiswa())
            ->assertSessionHasErrors([
                'email' => 'Email sudah digunakan',
                'username' => 'Username sudah digunakan',
                'nim' => 'NIM sudah terdaftar',
            ]);
    }

    /** Validasi password & format (boundary) */
    #[DataProvider('payloadTidakValid')]
    public function test_wb_registrasi_validasi_gagal(array $override, string $fieldError): void
    {
        $this->post(route('register.form', 'MHS'), $this->dataMahasiswa($override))
            ->assertSessionHasErrors($fieldError);

        $this->assertDatabaseCount('users', 0);
    }

    public static function payloadTidakValid(): array
    {
        return [
            'password < 8 karakter' => [['password' => 'Pass1', 'password_confirmation' => 'Pass1'], 'password'],
            'password tanpa angka' => [['password' => 'PasswordSaja', 'password_confirmation' => 'PasswordSaja'], 'password'],
            'password tanpa huruf' => [['password' => '12345678', 'password_confirmation' => '12345678'], 'password'],
            'konfirmasi tidak cocok' => [['password_confirmation' => 'Beda12345'], 'password_confirmation'],
            'email format salah' => [['email' => 'bukan-email'], 'email'],
            'username berisi spasi' => [['username' => 'budi s'], 'username'],
            'nama kosong' => [['name' => ''], 'name'],
        ];
    }

    public function test_wb_keluar(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(self::URL_LOGOUT)
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }
}
