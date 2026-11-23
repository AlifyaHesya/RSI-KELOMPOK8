<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\Pendaftaran;
use App\Models\ProgramKerja;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * <<controller>> PendaftaranController, UC-06 Mendaftar Program Kerja.
 *
 * Bagian A mengikuti class diagram (atribut + 3 method public dengan signature yang sama).
 * Bagian B adalah handler HTTP tipis untuk route Laravel; tugasnya hanya memanggil
 * method di Bagian A sesuai urutan sequence diagram lalu memilih view (boundary).
 */
class PendaftaranController extends Controller
{
    // ===== A. Sesuai class diagram =====

    private Mahasiswa $mahasiswa;
    private Pendaftaran $pendaftaran;
    private ProgramKerja $programKerja;

    /** Alasan penolakan (langkah 13). Diisi periksaPendaftaran() saat hasilnya false. */
    private string $alasan = '';

    /**
     * Langkah 2–5: periksa status buka, riwayat pendaftaran, dan kuota.
     * true = mahasiswa boleh mendaftar; false = lihat $this->alasan.
     */
    public function periksaPendaftaran(int $idProker, int $idMahasiswa): bool
    {
        $this->programKerja = ProgramKerja::findOrFail($idProker);

        $statusBuka = $this->programKerja->cekStatusPendaftaran();                          // 3
        $statusPendaftaran = Pendaftaran::cekRiwayatPendaftaran($idProker, $idMahasiswa);   // 4
        $kuotaTersedia = $this->programKerja->cekKuotaTersedia();                           // 5

        // alt [statusPendaftaran=false, kuotaTersedia=true, statusBuka=true]
        if (! $statusPendaftaran && $kuotaTersedia && $statusBuka) {
            return true;
        }

        // else: urutan pemeriksaan mengikuti pseudo-code periksaPendaftaran()
        $this->alasan = match (true) {
            ! $statusBuka => 'Pendaftaran sudah ditutup',
            ! $kuotaTersedia => 'Kuota pendaftaran sudah penuh',
            default => 'Anda sudah terdaftar pada program kerja ini',
        };

        return false;
    }

    /** Langkah 10: simpan pendaftaran. Mengembalikan statusSimpan. */
    public function simpanPendaftaran(array $dataPendaftaran): bool
    {
        $this->pendaftaran = new Pendaftaran;

        return $this->pendaftaran->simpanPendaftaran($dataPendaftaran);
    }

    /** Langkah 9: meneruskan ke Mahasiswa::validasiDataMahasiswa(). Mengembalikan hasilValidasi. */
    public function validasiDataPendaftaran(array $dataPendaftaran): bool
    {
        $this->mahasiswa ??= Mahasiswa::findOrFail($dataPendaftaran['id_mahasiswa']);

        return $this->mahasiswa->validasiDataMahasiswa($dataPendaftaran);
    }

    // ===== B. Handler HTTP (adapter Laravel, bukan bagian class diagram) =====

    /** Langkah 1–2: tombol "Daftar" di HalamanDetailProgramKerja (pilihDaftar). */
    public function formDaftar(ProgramKerja $programKerja): View|RedirectResponse
    {
        $this->mahasiswa = auth()->user()->mahasiswa;

        if ($this->periksaPendaftaran($programKerja->id_proker, $this->mahasiswa->id_mahasiswa)) {
            // 6: FormPendaftaran::tampilkanFormPendaftaran(idProker)
            return view('pendaftaran.form', [
                'programKerja' => $this->programKerja,
                'mahasiswa' => $this->mahasiswa,
            ]);
        }

        // 13: HalamanDetailProgramKerja::tampilkanPesan(alasan)
        return redirect()->route('katalog.show', $programKerja)->with('pesan', $this->alasan);
    }

    /** Langkah 8: FormPendaftaran::kirimDataPendaftaran(dataPendaftaran). */
    public function prosesDaftar(Request $request, ProgramKerja $programKerja): RedirectResponse
    {
        $this->mahasiswa = auth()->user()->mahasiswa;

        $dataPendaftaran = $request->only(['nama', 'nim', 'email', 'program_studi']) + [
            'id_mahasiswa' => $this->mahasiswa->id_mahasiswa,
            'id_proker' => $programKerja->id_proker,
        ];

        if (! $this->validasiDataPendaftaran($dataPendaftaran)) {                            // 9
            // 12: FormPendaftaran::tampilkanPesanError(hasilValidasi)
            return back()->withErrors($this->mahasiswa->kesalahanValidasi)->withInput();
        }

        if (! $this->simpanPendaftaran($dataPendaftaran)) {                                  // 10
            // statusSimpan=false: form menampilkan pesan error (pseudo-code PC-06-03)
            return back()
                ->withErrors(['simpan' => 'Pendaftaran gagal disimpan. Pendaftaran mungkin sudah ditutup atau kuota penuh.'])
                ->withInput();
        }

        // 11: HalamanKonfirmasiPendaftaran::tampilkanKonfirmasi(statusSimpan=true)
        return redirect()->route('pendaftaran.konfirmasi', $this->pendaftaran);
    }

    /** Halaman konfirmasi (HalamanKonfirmasiPendaftaran) setelah pendaftaran tersimpan. */
    public function konfirmasi(Pendaftaran $pendaftaran): View
    {
        abort_unless(
            (int) $pendaftaran->id_mahasiswa === (int) auth()->user()->mahasiswa->id_mahasiswa,
            403
        );

        return view('pendaftaran.konfirmasi', [
            'statusSimpan' => true,
            'pendaftaran' => $pendaftaran->load('programKerja'),
        ]);
    }
}
