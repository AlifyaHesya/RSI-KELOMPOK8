<?php

namespace App\Http\Controllers;

use App\Enums\KategoriProker;
use App\Models\Pendaftaran;
use App\Models\ProgramKerja;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** UC-03 Katalog, UC-04 Cari & Filter, UC-05 Detail, UC-08 Berdasarkan Tanggal. */
class KatalogController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'kategori' => ['nullable', Rule::in(array_map(fn ($item) => $item->value, KategoriProker::cases()))],
            'tanggal' => ['nullable', 'date'],
            'biro' => ['nullable', 'string', 'max:100'],
        ]);

        $query = ProgramKerja::dipublikasi()
            ->with('pic.pengguna')
            ->withCount(['pendaftaran' => fn ($query) => $query->where('status_pendaftaran', true)]);

        if (! empty($filters['q'])) {
            $query->where('nama_proker', 'like', '%'.$filters['q'].'%');
        }
        if (! empty($filters['kategori'])) {
            $query->where('kategori', $filters['kategori']);
        }
        if (! empty($filters['tanggal'])) {
            $query->whereDate('tanggal_pelaksanaan', $filters['tanggal']);
        }
        if (! empty($filters['biro'])) {
            $query->where('biro', 'like', '%'.$filters['biro'].'%');
        }

        return view('mahasiswa.beranda', [
            'programKerja' => $query->latest()->paginate(6)->withQueryString(),
            'kategori' => KategoriProker::cases(),
            'ringkasan' => [
                'program' => ProgramKerja::dipublikasi()->count(),
                'biro' => ProgramKerja::dipublikasi()->distinct()->count('biro'),
                'peserta' => Pendaftaran::whereHas('programKerja', fn ($query) => $query->dipublikasi())->count(),
            ],
        ]);
    }

    public function show(ProgramKerja $programKerja)
    {
        abort_unless($programKerja->dipublikasi, 404);

        $programKerja->load('pic.pengguna')->loadCount(['pendaftaran' => fn ($query) => $query->where('status_pendaftaran', true)]);

        return view('katalog.detail', compact('programKerja'));
    }
}
