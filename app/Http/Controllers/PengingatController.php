<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\Pengingat;
use App\Models\ProgramKerja;
use DateTimeInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * <<controller>> PengingatController, UC-07 Mengaktifkan Notifikasi Pengingat.
 *
 * Bagian A mengikuti class diagram + pseudo-code PC-07-01..04.
 * Bagian B adalah handler HTTP tipis untuk route Laravel.
 */
class PengingatController extends Controller
{
    // ===== A. Sesuai class diagram =====

    private Mahasiswa $mahasiswa;

    private Pengingat $pengingat;

    private ProgramKerja $programKerja;

    /** statusPengingat (PC-07-03): pesan hasil aktifkanPengingat() untuk konfirmasi / error. */
    private string $pesan = '';

    /** false bila aktifkanPengingat() berhenti karena waktu tidak valid (error tampil di form). */
    private bool $waktuValid = true;

    /**
     * PC-07-01, langkah 2–3: ambil tanggalDeadline (batas pendaftaran) program kerja.
     * Mengembalikan null bila informasi batas pendaftaran tidak tersedia.
     */
    public function getInformasiPengingat(int $idProker): ?Carbon
    {
        $this->programKerja = ProgramKerja::findOrFail($idProker);

        return $this->programKerja->ambilBatasPendaftaran();                                   // 3
    }

    /**
     * PC-07-02: waktu valid jika lebih dari sekarang dan sebelum tanggalDeadline.
     * $this->programKerja harus sudah terisi (lewat getInformasiPengingat / aktifkanPengingat).
     */
    public function validasiWaktuPengingat(DateTimeInterface $waktuPengingat): bool
    {
        $tanggalDeadline = isset($this->programKerja)
            ? $this->programKerja->ambilBatasPendaftaran()
            : null;

        if ($tanggalDeadline === null) {
            return false;
        }

        $waktu = Carbon::instance($waktuPengingat);

        return $waktu->gt(now()) && $waktu->lt($tanggalDeadline);
    }

    /**
     * PC-07-03, langkah 6: validasi -> simpan pengaturan (7) -> jadwalkan (8).
     * Mengembalikan statusPenjadwalan; teks statusnya ada di $this->pesan.
     *
     * Urutan parameter mengikuti sequence diagram: (idProker, idMahasiswa, waktuPengingat).
     */
    public function aktifkanPengingat(int $idProker, int $idMahasiswa, DateTimeInterface $waktuPengingat): bool
    {
        $this->programKerja = ProgramKerja::findOrFail($idProker);

        $this->waktuValid = $this->validasiWaktuPengingat($waktuPengingat);

        if (! $this->waktuValid) {
            $this->pesan = 'Waktu pengingat tidak valid';

            return false;
        }

        // pengingat yang sama (mahasiswa + proker) dipakai ulang, bukan dibuat ganda
        $this->pengingat = Pengingat::firstOrNew([
            'id_proker' => $idProker,
            'id_mahasiswa' => $idMahasiswa,
        ]);

        $statusSimpan = $this->pengingat->simpanPengaturanPengingat($idProker, $idMahasiswa, $waktuPengingat); // 7

        if (! $statusSimpan) {
            $this->pesan = 'Pengingat gagal disimpan';

            return false;
        }

        $statusPenjadwalan = $this->jadwalkanPengingat($this->pengingat->id_pengingat);                         // 8

        $this->pesan = $statusPenjadwalan ? 'Pengingat berhasil diaktifkan' : 'Pengingat gagal dijadwalkan';

        return $statusPenjadwalan;
    }

    /** PC-07-04: meneruskan ke Pengingat::jadwalkanPengingat(). */
    public function jadwalkanPengingat(int $idPengingat): bool
    {
        return Pengingat::jadwalkanPengingat($idPengingat);
    }

    // ===== B. Handler HTTP (adapter Laravel, bukan bagian class diagram) =====

    /** Langkah 1–4: tombol "Aktifkan Pengingat" di HalamanDetailProgramKerja. */
    public function formPengingat(ProgramKerja $programKerja): View|RedirectResponse
    {
        abort_unless($programKerja->dipublikasi, 404);

        $tanggalDeadline = $this->getInformasiPengingat($programKerja->id_proker);              // 2–3

        if ($tanggalDeadline === null) {
            return redirect()->route('katalog.show', $programKerja)
                ->with('pesan', 'Informasi batas pendaftaran tidak tersedia');
        }

        // alur alternatif UC-07 A: pendaftaran sudah ditutup
        if ($tanggalDeadline->isPast()) {
            return redirect()->route('katalog.show', $programKerja)
                ->with('pesan', 'Pengingat tidak dapat diaktifkan karena pendaftaran sudah ditutup');
        }

        // 4: FormPengingat::tampilkanPilihanWaktu(tanggalDeadline)
        return view('pengingat.form', [
            'programKerja' => $this->programKerja,
            'tanggalDeadline' => $tanggalDeadline,
        ]);
    }

    /** Langkah 5–6: FormPengingat::pilihWaktuPengingat() lalu aktifkanPengingat(). */
    public function prosesPengingat(Request $request, ProgramKerja $programKerja): RedirectResponse
    {
        abort_unless($programKerja->dipublikasi, 404);

        // hanya format; aturan waktu (> sekarang, < deadline) ada di validasiWaktuPengingat()
        $data = $request->validate(['waktu_pengingat' => ['required', 'date']]);

        $this->mahasiswa = auth()->user()->mahasiswa;

        $statusPenjadwalan = $this->aktifkanPengingat(
            $programKerja->id_proker,
            $this->mahasiswa->id_mahasiswa,
            Carbon::parse($data['waktu_pengingat'])
        );

        if (! $this->waktuValid) {
            // PC-07-03: pesan error tampil di FormPengingat
            return back()->withErrors(['waktu_pengingat' => $this->pesan])->withInput();
        }

        // 9: HalamanDetailProgramKerja::tampilkanKonfirmasi(statusPenjadwalan)
        return redirect()->route('katalog.show', $programKerja)->with('konfirmasi', [
            'berhasil' => $statusPenjadwalan,
            'pesan' => $this->pesan,
        ]);
    }
}