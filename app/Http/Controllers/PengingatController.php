<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\Pengingat;
use App\Models\ProgramKerja;
use DateTimeInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

/** Pengingat pendaftaran mahasiswa (UC-07). */
class PengingatController extends Controller
{
    // Aturan pengingat

    private Mahasiswa $mahasiswa;

    private Pengingat $pengingat;

    private ProgramKerja $programKerja;

    private string $pesan = '';

    private bool $waktuValid = true;

    public function getInformasiPengingat(int $idProker): ?Carbon
    {
        $this->programKerja = ProgramKerja::findOrFail($idProker);

        return $this->programKerja->ambilBatasPendaftaran($idProker);                                   // 3
    }

    public function validasiWaktuPengingat(DateTimeInterface $waktuPengingat): bool
    {
        $tanggalDeadline = isset($this->programKerja)
            ? $this->programKerja->ambilBatasPendaftaran($this->programKerja->id_proker)
            : null;

        if ($tanggalDeadline === null) {
            return false;
        }

        $waktu = Carbon::instance($waktuPengingat);

        return $waktu->gt(now()) && $waktu->lt($tanggalDeadline);
    }

    private function simpanDanJadwalkanPengingat(int $idProker, int $idMahasiswa, DateTimeInterface $waktuPengingat): bool
    {
        $this->programKerja = ProgramKerja::findOrFail($idProker);

        $this->waktuValid = $this->validasiWaktuPengingat($waktuPengingat);

        if (! $this->waktuValid) {
            $this->pesan = 'Waktu pengingat tidak valid';

            return false;
        }

        $this->pengingat = Pengingat::firstOrNew([
            'id_proker' => $idProker,
            'id_mahasiswa' => $idMahasiswa,
            'waktu_pengingat' => Carbon::instance($waktuPengingat)->toDateTimeString(),
        ]);

        $statusSimpan = $this->pengingat->simpanPengaturanPengingat($idProker, $idMahasiswa, $waktuPengingat); // 7

        if (! $statusSimpan) {
            $this->pesan = 'Pengingat gagal disimpan';

            return false;
        }

        $statusPenjadwalan = $this->pengingat->jadwalkanPengingat();                         // 8

        $this->pesan = $statusPenjadwalan ? 'Pengingat berhasil diaktifkan' : 'Pengingat gagal dijadwalkan';

        return $statusPenjadwalan;
    }


    public function tampilkanPilihanWaktu(ProgramKerja $programKerja): View|RedirectResponse
    {
        abort_unless($programKerja->dipublikasi, 404);

        $tanggalDeadline = $this->getInformasiPengingat($programKerja->id_proker);

        if ($tanggalDeadline === null) {
            return redirect()->route('katalog.show', $programKerja)
                ->with('pesan', 'Informasi batas pendaftaran tidak tersedia');
        }

        if ($tanggalDeadline->isPast()) {
            return redirect()->route('katalog.show', $programKerja)
                ->with('pesan', 'Pengingat tidak dapat diaktifkan karena pendaftaran sudah ditutup');
        }

        $this->mahasiswa = auth()->user()->mahasiswa;
        $waktuTerpilih = $this->mahasiswa->pengingat()
            ->where('id_proker', $programKerja->id_proker)
            ->pluck('waktu_pengingat')
            ->map(fn ($waktu) => Carbon::parse($waktu)->format('Y-m-d\\TH:i'))
            ->all();

        return view('mahasiswa.pengingat', [
            'programKerja' => $this->programKerja,
            'tanggalDeadline' => $tanggalDeadline,
            'waktuTerpilih' => $waktuTerpilih,
        ]);
    }

    public function aktifkanPengingat(Request $request, ProgramKerja $programKerja): RedirectResponse
    {
        abort_unless($programKerja->dipublikasi, 404);

        $data = $request->validate([
            'waktu_pengingat' => ['required', 'array', 'min:1'],
            'waktu_pengingat.*' => ['required', 'date'],
        ], [
            'waktu_pengingat.required' => 'Pilih setidaknya satu jadwal pengingat.',
            'waktu_pengingat.min' => 'Pilih setidaknya satu jadwal pengingat.',
        ]);

        $this->mahasiswa = auth()->user()->mahasiswa;
        $this->programKerja = $programKerja;
        $waktuPilihan = collect($data['waktu_pengingat'])
            ->map(fn ($waktu) => Carbon::parse($waktu)->startOfMinute())
            ->unique(fn (Carbon $waktu) => $waktu->toDateTimeString())
            ->values();

        foreach ($waktuPilihan as $urutan => $waktu) {
            if (! $this->validasiWaktuPengingat($waktu)) {
                throw ValidationException::withMessages([
                    "waktu_pengingat.{$urutan}" => 'Setiap pengingat harus sebelum batas pendaftaran dan masih di masa depan.',
                ]);
            }
        }

        DB::transaction(function () use ($programKerja, $waktuPilihan) {
            $waktuSimpan = $waktuPilihan->map(fn (Carbon $waktu) => $waktu->toDateTimeString())->all();

            $this->mahasiswa->pengingat()
                ->where('id_proker', $programKerja->id_proker)
                ->whereNotIn('waktu_pengingat', $waktuSimpan)
                ->delete();

            foreach ($waktuPilihan as $waktu) {
                if (! $this->simpanDanJadwalkanPengingat($programKerja->id_proker, $this->mahasiswa->id_mahasiswa, $waktu)) {
                    throw ValidationException::withMessages([
                        'waktu_pengingat' => 'Pengingat gagal disimpan. Silakan coba lagi.',
                    ]);
                }
            }
        });

        return redirect()->route('pengingat.form', $programKerja)->with('success', 'Jadwal pengingat berhasil disimpan.');
    }
}
