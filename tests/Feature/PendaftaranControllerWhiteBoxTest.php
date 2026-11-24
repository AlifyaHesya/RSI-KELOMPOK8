<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Mahasiswa;
use App\Models\Pendaftaran;
use App\Models\ProgramKerja;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class PendaftaranControllerWhiteBoxTest extends TestCase
{
    use RefreshDatabase;

    private const ROUTE_INDEX = 'pendaftaran.index';     // GET  index()
    private const ROUTE_PERIKSA = 'pendaftaran.periksa'; // GET  periksaPendaftaran()
    private const ROUTE_KIRIM = 'pendaftaran.simpan';     // POST kirimDataPendaftaran()

    private const PESAN_TUTUP = 'Pendaftaran sudah ditutup';
    private const PESAN_PENUH = 'Kuota pendaftaran sudah penuh';
    private const PESAN_SUDAH = 'Anda sudah terdaftar pada program kerja ini';

    private function buatMahasiswa(string $nim = '225150200111001'): User
    {
        $user = User::factory()->create(['role' => Role::Mahasiswa]);
        $user->mahasiswa()->create(['nim' => $nim, 'program_studi' => 'Teknik Informatika']);

        return $user->load('mahasiswa');
    }

    /** Program kerja dengan pendaftaran dibuka dan kuota masih tersedia. */
    private function buatProker(array $override = []): ProgramKerja
    {
        $picUser = User::factory()->create(['role' => Role::Pic]);
        $pic = $picUser->pic()->create(['biro' => 'Biro Kemahasiswaan']);

        return ProgramKerja::create(array_merge([
            'id_pic' => $pic->id_pic,
            'nama_proker' => 'Program Uji',
            'deskripsi' => 'Deskripsi program uji',
            'kuota' => 5,
            'lokasi' => 'Kampus',
            'biro' => 'Biro Kemahasiswaan',
            'kategori' => 'Seminar',
            'tanggal_pelaksanaan' => now()->addDays(14)->toDateString(),
            'waktu_pelaksanaan' => '09:00:00',
            'tanggal_buka_pendaftaran' => now()->subDay(),
            'batas_pendaftaran' => now()->addDays(7),
            'status_buka' => true,
            'dipublikasi' => true,
        ], $override));
    }

    private function tutupProker(ProgramKerja $proker): void
    {
        $proker->update(['status_buka' => false]);
    }
    private function penuhiKuota(ProgramKerja $proker): void
    {
        $proker->update(['kuota' => 1]);
        $lain = $this->buatMahasiswa('999999999999999');
        $this->daftarkan($lain->mahasiswa, $proker);
    }

    private function daftarkan(Mahasiswa $mhs, ProgramKerja $proker, bool $status = true): Pendaftaran
    {
        return Pendaftaran::unguarded(fn () => Pendaftaran::create([
            'id_mahasiswa' => $mhs->id_mahasiswa,
            'id_proker' => $proker->id_proker,
            'status_pendaftaran' => $status,
            'tanggal_pendaftaran' => now(),
        ]));
    }

    private function dataForm(User $user, array $override = []): array
    {
        return array_merge([
            'nama' => $user->name,
            'nim' => $user->mahasiswa->nim,
            'email' => $user->email,
            'program_studi' => $user->mahasiswa->program_studi,
        ], $override);
    }

    public function test_wb_index_hanya_menampilkan_pendaftaran_milik_sendiri(): void
    {
        $user = $this->buatMahasiswa();
        $lain = $this->buatMahasiswa('999999999999999');
        $proker = $this->buatProker();

        $this->daftarkan($user->mahasiswa, $proker);
        $this->daftarkan($lain->mahasiswa, $proker);

        $response = $this->actingAs($user)->get(route(self::ROUTE_INDEX));

        $response->assertOk()->assertViewIs('mahasiswa.pendaftaran.index');

        $paginator = $response->viewData('pendaftaran');
        $this->assertInstanceOf(LengthAwarePaginator::class, $paginator);
        $this->assertCount(1, $paginator->items());
        $this->assertSame(10, $paginator->perPage());
    }


    /** Jalur a */
    public function test_wb_periksa_layak_menampilkan_form(): void
    {
        $user = $this->buatMahasiswa();
        $proker = $this->buatProker();

        $this->actingAs($user)
            ->get(route(self::ROUTE_PERIKSA, $proker))
            ->assertOk()
            ->assertViewIs('mahasiswa.formPendaftaran')
            ->assertViewHas('programKerja', fn ($p) => $p->is($proker))
            ->assertViewHas('mahasiswa', fn ($m) => $m->is($user->mahasiswa));
    }

    /** Jalur b: sudah terdaftar, pendaftaran buka, kuota masih ada */
    public function test_wb_periksa_sudah_terdaftar(): void
    {
        $user = $this->buatMahasiswa();
        $proker = $this->buatProker(['kuota' => 5]);
        $this->daftarkan($user->mahasiswa, $proker);

        $this->actingAs($user)
            ->get(route(self::ROUTE_PERIKSA, $proker))
            ->assertRedirect(route('katalog.show', $proker))
            ->assertSessionHas('pesan', self::PESAN_SUDAH);
    }

    /** Jalur c: kuota penuh (oleh mahasiswa lain), belum terdaftar, pendaftaran buka */
    public function test_wb_periksa_kuota_penuh(): void
    {
        $user = $this->buatMahasiswa();
        $proker = $this->buatProker();
        $this->penuhiKuota($proker);

        $this->actingAs($user)
            ->get(route(self::ROUTE_PERIKSA, $proker))
            ->assertRedirect(route('katalog.show', $proker))
            ->assertSessionHas('pesan', self::PESAN_PENUH);
    }

    /** Jalur d: pendaftaran ditutup, belum terdaftar, kuota ada */
    public function test_wb_periksa_pendaftaran_ditutup(): void
    {
        $user = $this->buatMahasiswa();
        $proker = $this->buatProker();
        $this->tutupProker($proker);

        $this->actingAs($user)
            ->get(route(self::ROUTE_PERIKSA, $proker))
            ->assertRedirect(route('katalog.show', $proker))
            ->assertSessionHas('pesan', self::PESAN_TUTUP);
    }

    /** Jalur e: ditutup DAN sudah terdaftar -> pesan "ditutup" menang */
    public function test_wb_periksa_ditutup_dan_sudah_terdaftar(): void
    {
        $user = $this->buatMahasiswa();
        $proker = $this->buatProker();
        $this->daftarkan($user->mahasiswa, $proker);
        $this->tutupProker($proker);

        $this->actingAs($user)
            ->get(route(self::ROUTE_PERIKSA, $proker))
            ->assertRedirect(route('katalog.show', $proker))
            ->assertSessionHas('pesan', self::PESAN_TUTUP);
    }

    public function test_wb_periksa_kuota_penuh_dan_sudah_terdaftar(): void
    {
        $user = $this->buatMahasiswa();
        $proker = $this->buatProker(['kuota' => 1]);
        $this->daftarkan($user->mahasiswa, $proker);

        $this->actingAs($user)
            ->get(route(self::ROUTE_PERIKSA, $proker))
            ->assertRedirect(route('katalog.show', $proker))
            ->assertSessionHas('pesan', self::PESAN_PENUH);
    }

    /** Jalur 1: D1=T (validasi gagal) */
    public function test_wb_kirim_validasi_gagal(): void
    {
        $user = $this->buatMahasiswa();
        $proker = $this->buatProker();

        $this->actingAs($user)
            ->from(route(self::ROUTE_PERIKSA, $proker))
            ->post(route(self::ROUTE_KIRIM, $proker), [
                'nama' => '', 'nim' => '', 'email' => '', 'program_studi' => '',
            ])
            ->assertRedirect(route(self::ROUTE_PERIKSA, $proker))
            ->assertSessionHasErrors();

        $this->assertSame(0, Pendaftaran::count());
    }

    /** Jalur 2: D1=F, D2=T (simpan gagal, mis. pendaftaran ditutup saat submit) */
    public function test_wb_kirim_simpan_gagal(): void
    {
        $user = $this->buatMahasiswa();
        $proker = $this->buatProker();
        $this->tutupProker($proker);

        $this->actingAs($user)
            ->from(route(self::ROUTE_PERIKSA, $proker))
            ->post(route(self::ROUTE_KIRIM, $proker), $this->dataForm($user))
            ->assertRedirect(route(self::ROUTE_PERIKSA, $proker))
            ->assertSessionHasErrors([
                'simpan' => 'Pendaftaran gagal disimpan. Pendaftaran mungkin sudah ditutup atau kuota penuh.',
            ]);

        $this->assertSame(0, Pendaftaran::count());
    }

    /** Jalur 3: D1=F, D2=F (sukses) */
    public function test_wb_kirim_berhasil_redirect_ke_konfirmasi(): void
    {
        $user = $this->buatMahasiswa();
        $proker = $this->buatProker();

        $response = $this->actingAs($user)
            ->post(route(self::ROUTE_KIRIM, $proker), $this->dataForm($user));

        $this->assertSame(1, Pendaftaran::count());

        $pendaftaran = Pendaftaran::firstOrFail();
        $this->assertSame($user->mahasiswa->id_mahasiswa, (int) $pendaftaran->id_mahasiswa);
        $this->assertSame($proker->id_proker, (int) $pendaftaran->id_proker);

        $response->assertRedirect(route('pendaftaran.konfirmasi', $pendaftaran));
    }


    /** Jalur 1: pemilik -> 200 (status tersimpan) */
    public function test_wb_konfirmasi_pemilik_status_tersimpan(): void
    {
        $user = $this->buatMahasiswa();
        $pendaftaran = $this->daftarkan($user->mahasiswa, $this->buatProker(), true);

        $this->actingAs($user)
            ->get(route('pendaftaran.konfirmasi', $pendaftaran))
            ->assertOk()
            ->assertViewIs('mahasiswa.konfirmasi-pendaftaran')
            ->assertViewHas('statusSimpan', true)
            ->assertViewHas('pendaftaran', fn ($p) => $p->relationLoaded('programKerja')
                && $p->relationLoaded('mahasiswa'));
    }

    /** Jalur 1 (varian): cast (bool) status_pendaftaran = false */
    public function test_wb_konfirmasi_pemilik_status_tidak_tersimpan(): void
    {
        $user = $this->buatMahasiswa();
        $pendaftaran = $this->daftarkan($user->mahasiswa, $this->buatProker(), false);

        $this->actingAs($user)
            ->get(route('pendaftaran.konfirmasi', $pendaftaran))
            ->assertOk()
            ->assertViewHas('statusSimpan', false);
    }

    /** Jalur 2: bukan pemilik -> 403 */
    public function test_wb_konfirmasi_bukan_pemilik_ditolak(): void
    {
        $pemilik = $this->buatMahasiswa();
        $lain = $this->buatMahasiswa('999999999999999');
        $pendaftaran = $this->daftarkan($pemilik->mahasiswa, $this->buatProker());

        $this->actingAs($lain)
            ->get(route('pendaftaran.konfirmasi', $pendaftaran))
            ->assertForbidden();
    }
}
