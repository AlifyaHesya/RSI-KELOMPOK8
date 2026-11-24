<?php

namespace App\Http\Controllers;

use App\Enums\StatusPengajuan;
use App\Models\Pengajuan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/** UC-10 Mengajukan Program Kerja, UC-11 Melihat Status Pengajuan (PIC). */
class PengajuanController extends Controller
{
    public function index()
    {
        $pengajuan = auth()->user()->pic
            ->pengajuan()
            ->with('programKerja')
            ->latest('updated_at')
            ->get();

        return view('pic.pengajuan.index', compact('pengajuan'));
    }

    public function show(Pengajuan $pengajuan)
    {
        $this->pastikanKepemilikan($pengajuan);

        return view('pic.pengajuan.show', [
            'pengajuan' => $pengajuan->load('programKerja'),
            'dokumenKurang' => $pengajuan->dokumenKurang(),
        ]);
    }

    public function unggahBerkas(Request $request, Pengajuan $pengajuan): RedirectResponse
    {
        $this->pastikanKepemilikan($pengajuan);
        abort_unless($pengajuan->bisaDiubahPic(), 403);

        $adaBerkasBaru = collect(array_keys(Pengajuan::BERKAS_WAJIB))
            ->contains(fn (string $kolom) => $request->hasFile($kolom));
        $tautanBerubah = $request->filled('link_bukti')
            && trim((string) $request->input('link_bukti')) !== trim((string) $pengajuan->link_bukti);

        if (! $adaBerkasBaru && ! $tautanBerubah) {
            return back()
                ->withInput()
                ->with('error', 'Pilih berkas untuk diunggah atau masukkan tautan bukti yang baru.');
        }

        $data = $request->validate([
            'berkas_proposal' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
            'berkas_rab' => ['nullable', 'file', 'mimes:pdf,xls,xlsx', 'max:5120'],
            'berkas_surat_pengantar' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
            'link_bukti' => ['nullable', 'url', 'max:255'],
        ]);

        foreach (array_keys(Pengajuan::BERKAS_WAJIB) as $kolom) {
            if ($request->hasFile($kolom)) {
                if ($pengajuan->{$kolom}) {
                    Storage::delete($pengajuan->{$kolom});
                }
                $pengajuan->{$kolom} = $request->file($kolom)->store("pengajuan/{$pengajuan->id_pengajuan}");
            }
        }

        if ($request->filled('link_bukti')) {
            $pengajuan->link_bukti = $data['link_bukti'];
        }

        $pengajuan->berkas_lengkap = $pengajuan->cekBerkasLengkap();
        $pengajuan->save();

        return back()->with('success', 'Berkas berhasil diunggah');
    }

    public function ajukanPengajuan(Pengajuan $pengajuan): RedirectResponse
    {
        $this->pastikanKepemilikan($pengajuan);
        abort_unless($pengajuan->bisaDiubahPic(), 403);

        if (! $pengajuan->cekBerkasLengkap()) {
            return back()
                ->with('error', 'Pengajuan ditolak sistem, berkas belum lengkap')
                ->with('dokumen_kurang', $pengajuan->dokumenKurang());
        }

        $this->setujuiDanPublikasikan($pengajuan);

        return redirect()->route('pic.pengajuan.index')->with('success', 'Pengajuan langsung disetujui dan program kerja dipublikasikan');
    }

    public function setujuiPengajuan(Pengajuan $pengajuan): RedirectResponse
    {
        $this->pastikanKepemilikan($pengajuan);
        abort_unless($pengajuan->bisaDiubahPic(), 403);

        if (! $pengajuan->cekBerkasLengkap()) {
            return back()->with('error', 'Pengajuan gagal diproses, berkas belum lengkap');
        }

        $this->setujuiDanPublikasikan($pengajuan);

        return redirect()->route('pic.pengajuan.index')->with('success', 'Program kerja disetujui dan dipublikasikan');
    }

    private function pastikanKepemilikan(Pengajuan $pengajuan): void
    {
        abort_unless((int) $pengajuan->id_pic === (int) auth()->user()->pic->id_pic, 403);
    }

    private function setujuiDanPublikasikan(Pengajuan $pengajuan): void
    {
        DB::transaction(function () use ($pengajuan) {
            $pengajuan->ubahStatus(StatusPengajuan::Disetujui);
            $pengajuan->programKerja()->update(['dipublikasi' => true, 'status_buka' => true]);
        });
    }
}
