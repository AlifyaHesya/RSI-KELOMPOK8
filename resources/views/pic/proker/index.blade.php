@extends('layouts.app')
@section('title', 'Program Kerja Saya')
@section('content')
<div class="container py-5">
    <header class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div><span class="pk-eyebrow">Ruang PIC</span><h1 class="h2 text-pk fw-bold mb-1">Program Kerja Saya</h1><p class="text-secondary mb-0">Kelola kegiatan, pengajuan, dan pendaftaran dalam satu tempat.</p></div>
        <a class="btn btn-pk" href="{{ route('pic.proker.create') }}"><i class="bi bi-plus-lg me-2"></i>Buat Program Kerja</a>
    </header>
    @if (session('success'))<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>@endif
    @if (session('error'))<div class="alert alert-danger"><i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}</div>@endif
    <div class="table-responsive pk-card bg-white p-3 p-lg-4">
        <table class="table align-middle mb-0">
            <thead><tr><th>Program</th><th>Pelaksanaan</th><th>Status</th><th>Pendaftar</th><th><span class="visually-hidden">Aksi</span></th></tr></thead>
            <tbody>
            @forelse ($programKerja as $proker)
                <tr>
                    <td><strong>{{ $proker->nama_proker }}</strong><div class="small text-secondary">{{ $proker->kategori->value }} · {{ $proker->lokasi }}</div></td>
                    <td class="text-nowrap">{{ $proker->tanggal_pelaksanaan->translatedFormat('d M Y') }}<div class="small text-secondary">{{ substr($proker->waktu_pelaksanaan, 0, 5) }}</div></td>
                    <td><span class="pk-badge"><i class="bi bi-circle-fill small"></i>{{ $proker->pengajuan?->status_pengajuan?->value ?? 'Belum diajukan' }}</span>@if($proker->dipublikasi)<div class="small text-success mt-1"><i class="bi bi-broadcast me-1"></i>Tayang</div>@endif</td>
                    <td class="text-nowrap"><strong>{{ $proker->pendaftaran_count }}</strong><span class="text-secondary"> / {{ $proker->kuota }}</span></td>
                    <td class="text-end">
                        <div class="d-inline-flex flex-wrap justify-content-end gap-1">
                            @if($proker->pengajuan)<a class="btn btn-sm btn-outline-primary" href="{{ route('pic.pengajuan.show', $proker->pengajuan) }}">Pengajuan</a>@endif
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('pic.proker.edit', $proker) }}">Ubah</a>
                            <form method="POST" action="{{ route('pic.proker.destroy', $proker) }}" onsubmit="return confirm('Hapus program kerja ini? Seluruh pendaftaran, pengingat, dan laporan terkait juga akan dihapus.')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Hapus</button></form>
                            @if ($proker->pengajuan?->status_pengajuan?->value === 'Disetujui')
                                <form method="POST" action="{{ route('pic.proker.buka', $proker) }}">@csrf @method('PATCH')<button class="btn btn-sm {{ $proker->status_buka ? 'btn-outline-secondary' : 'btn-outline-success' }}">{{ $proker->status_buka ? 'Tutup daftar' : 'Buka daftar' }}</button></form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5"><div class="py-5 text-center"><div class="text-pk fs-2 mb-2"><i class="bi bi-inboxes"></i></div><strong>Belum ada program kerja</strong><p class="text-secondary mb-3">Mulai dengan membuat program kerja pertama Anda.</p><a class="btn btn-pk btn-sm" href="{{ route('pic.proker.create') }}">Buat Program Kerja</a></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
@push('styles')
<style>.pk-eyebrow{display:inline-block;color:var(--pk-teal);font-size:.78rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;margin-bottom:.4rem}table form{display:inline-flex}</style>
@endpush