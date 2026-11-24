<?php

namespace App\Http\Controllers;

use App\Enums\KategoriProker;
use App\Enums\StatusPengajuan;
use App\Models\Pengajuan;
use App\Models\ProgramKerja;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\ValidationException;

/** UC-09 Mengelola Program Kerja (PIC). */
class ProgramKerjaController extends Controller
{
    public function index()
    {
        $programKerja = auth()->user()->pic
            ->programKerja()
            ->with('pengajuan')
            ->withCount(['pendaftaran' => fn ($query) => $query->where('status_pendaftaran', true)])
            ->latest()
            ->get();

        return view('pic.proker.index', compact('programKerja'));
    }

    public function create()
    {
        return view('pic.proker.form', [
            'programKerja' => new ProgramKerja,
            'kategori' => KategoriProker::cases(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->aturanValidasi(baru: true));
        $this->validasiJadwal($data);
        $poster = $data['poster'] ?? null;
        unset($data['poster']);
        $pic = auth()->user()->pic;

        $proker = DB::transaction(function () use ($data, $pic, $poster) {
            $proker = ProgramKerja::create($data + [
                'id_pic' => $pic->id_pic,
                'status_buka' => false,
                'dipublikasi' => false,
            ]);

            if ($poster) {
                $proker->update(['poster_path' => $poster->store('program-kerja/poster', 'public')]);
            }

            Pengajuan::create([
                'id_pic' => $pic->id_pic,
                'id_proker' => $proker->id_proker,
                'nama_proker' => $proker->nama_proker,
                'status_pengajuan' => StatusPengajuan::Draft,
            ]);

            return $proker;
        });

        return redirect()->route('pic.pengajuan.show', $proker->pengajuan)
            ->with('success', 'Program kerja berhasil disimpan sebagai draft');
    }

    public function edit(ProgramKerja $programKerja)
    {
        $this->pastikanKepemilikan($programKerja);

        return view('pic.proker.form', [
            'programKerja' => $programKerja,
            'kategori' => KategoriProker::cases(),
        ]);
    }

    public function update(Request $request, ProgramKerja $programKerja): RedirectResponse
    {
        $this->pastikanKepemilikan($programKerja);

        $data = $request->validate($this->aturanValidasi(baru: false));
        $this->validasiJadwal($data);
        $poster = $data['poster'] ?? null;
        unset($data['poster']);
        $posterLama = $programKerja->poster_path;

        DB::transaction(function () use ($programKerja, $data, $poster) {
            $programKerja->update($data);
            if ($poster) {
                $programKerja->update(['poster_path' => $poster->store('program-kerja/poster', 'public')]);
            }
            $programKerja->pengajuan->update(['nama_proker' => $data['nama_proker']]);
        });

        if ($poster && $posterLama) {
            Storage::disk('public')->delete($posterLama);
        }

        return redirect()->route('pic.proker.index')->with('success', 'Program kerja berhasil diperbarui');
    }

    public function destroy(ProgramKerja $programKerja): RedirectResponse
    {
        $this->pastikanKepemilikan($programKerja);

        $jalurPoster = $programKerja->poster_path;
        $jalurBerkasPengajuan = collect(Pengajuan::BERKAS_WAJIB)
            ->keys()
            ->map(fn (string $kolom) => $programKerja->pengajuan?->{$kolom})
            ->filter()
            ->all();

        $programKerja->delete();

        if ($jalurPoster) {
            Storage::disk('public')->delete($jalurPoster);
        }

        if ($jalurBerkasPengajuan !== []) {
            Storage::disk('local')->delete($jalurBerkasPengajuan);
        }

        return redirect()->route('pic.proker.index')->with('success', 'Program kerja dan data terkait berhasil dihapus');
    }

    public function ubahStatusPendaftaran(ProgramKerja $programKerja): RedirectResponse
    {
        $this->pastikanKepemilikan($programKerja);

        if ($programKerja->pengajuan->status_pengajuan !== StatusPengajuan::Disetujui) {
            return back()->with('error', 'Pendaftaran hanya dapat dibuka setelah pengajuan disetujui');
        }

        if (! $programKerja->status_buka && (! $programKerja->dipublikasi
            || now()->greaterThan($programKerja->batas_pendaftaran))) {
            return back()->with('error', 'Pendaftaran hanya dapat dibuka sebelum batas pendaftaran dan setelah program dipublikasikan');
        }

        $programKerja->update(['status_buka' => ! $programKerja->status_buka]);

        return back()->with('success', $programKerja->status_buka ? 'Pendaftaran dibuka' : 'Pendaftaran ditutup');
    }

    private function aturanValidasi(bool $baru): array
    {
        return [
            'nama_proker' => ['required', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string'],
            'kuota' => ['required', 'integer', 'min:1'],
            'lokasi' => ['required', 'string', 'max:100'],
            'biro' => ['required', 'string', 'max:100'],
            'kategori' => ['required', new Enum(KategoriProker::class)],
            'tanggal_pelaksanaan' => array_filter(['required', 'date', $baru ? 'after_or_equal:today' : null]),
            'waktu_pelaksanaan' => ['required', 'date_format:H:i'],
            'tanggal_buka_pendaftaran' => ['required', 'date'],
            'batas_pendaftaran' => ['required', 'date', 'after:tanggal_buka_pendaftaran'],
            'poster' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }

    private function validasiJadwal(array $data): void
    {
        $waktuPelaksanaan = Carbon::parse($data['tanggal_pelaksanaan'])
            ->setTimeFromTimeString($data['waktu_pelaksanaan']);

        if (Carbon::parse($data['batas_pendaftaran'])->greaterThanOrEqualTo($waktuPelaksanaan)) {
            throw ValidationException::withMessages([
                'batas_pendaftaran' => 'Batas pendaftaran harus sebelum waktu pelaksanaan program kerja.',
            ]);
        }
    }

    private function pastikanKepemilikan(ProgramKerja $programKerja): void
    {
        abort_unless((int) $programKerja->id_pic === (int) auth()->user()->pic->id_pic, 403);
    }
}
