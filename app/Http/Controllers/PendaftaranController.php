<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\Pendaftaran;
use App\Models\ProgramKerja;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/** Pendaftaran mahasiswa (UC-06). */
class PendaftaranController extends Controller
{
    // Aturan pendaftaran

    private Mahasiswa $mahasiswa;
    private Pendaftaran $pendaftaran;
    private ProgramKerja $programKerja;

    private string $alasan = '';

    private function cekKelayakanPendaftaran(int $idProker, int $idMahasiswa): bool
    {
        $this->programKerja = ProgramKerja::findOrFail($idProker);

        $statusBuka = $this->programKerja->cekStatusPendaftaran($idProker);
        $statusPendaftaran = Pendaftaran::cekRiwayatPendaftaran($idProker, $idMahasiswa);
        $kuotaTersedia = $this->programKerja->cekKuotaTersedia($idProker);

        if (! $statusPendaftaran && $kuotaTersedia && $statusBuka) {
            return true;
        }

        $this->alasan = match (true) {
            ! $statusBuka => 'Pendaftaran sudah ditutup',
            ! $kuotaTersedia => 'Kuota pendaftaran sudah penuh',
            default => 'Anda sudah terdaftar pada program kerja ini',
        };

        return false;
    }

    public function simpanPendaftaran(array $dataPendaftaran): bool
    {
        $this->pendaftaran = new Pendaftaran;

        return $this->pendaftaran->simpanPendaftaran($dataPendaftaran);
    }


    public function index(): View
    {
        $mahasiswa = Auth::user()->mahasiswa;

        return view('mahasiswa.pendaftaran.index', [
            'pendaftaran' => $mahasiswa->pendaftaran()
                ->with('programKerja')
                ->latest('tanggal_pendaftaran')
                ->paginate(10),
        ]);
    }

    public function periksaPendaftaran(ProgramKerja $programKerja): View|RedirectResponse
    {
        $this->mahasiswa = auth()->user()->mahasiswa;

        if ($this->cekKelayakanPendaftaran($programKerja->id_proker, $this->mahasiswa->id_mahasiswa)) {
            return $this->tampilkanFormPendaftaran($programKerja);
        }

        return redirect()->route('katalog.show', $programKerja)->with('pesan', $this->alasan);
    }

    private function tampilkanFormPendaftaran(ProgramKerja $programKerja): View
    {
        return view('mahasiswa.formPendaftaran', [
            'programKerja' => $programKerja,
            'mahasiswa' => $this->mahasiswa,
        ]);
    }

    public function kirimDataPendaftaran(Request $request, ProgramKerja $programKerja): RedirectResponse
    {
        $this->mahasiswa = auth()->user()->mahasiswa;

        $dataPendaftaran = $request->only(['nama', 'nim', 'email', 'program_studi']) + [
            'id_mahasiswa' => $this->mahasiswa->id_mahasiswa,
            'id_proker' => $programKerja->id_proker,
        ];

        if (! $this->mahasiswa->validasiDataMahasiswa($dataPendaftaran)) {
            return back()->withErrors($this->mahasiswa->kesalahanValidasi)->withInput();
        }

        if (! $this->simpanPendaftaran($dataPendaftaran)) {
            return back()
                ->withErrors(['simpan' => 'Pendaftaran gagal disimpan. Pendaftaran mungkin sudah ditutup atau kuota penuh.'])
                ->withInput();
        }

        return redirect()->route('pendaftaran.konfirmasi', $this->pendaftaran);
    }

    public function tampilkanKonfirmasi(Pendaftaran $pendaftaran): View
    {
        abort_unless(
            (int) $pendaftaran->id_mahasiswa === (int) auth()->user()->mahasiswa->id_mahasiswa,
            403
        );

        return view('mahasiswa.konfirmasi-pendaftaran', [
            'statusSimpan' => (bool) $pendaftaran->status_pendaftaran,
            'pendaftaran' => $pendaftaran->load(['programKerja', 'mahasiswa.pengguna']),
        ]);
    }
}
