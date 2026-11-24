@extends('layouts.app')
@section('title', 'Rekap Program Kerja')
@section('content')
<div class="container py-5">
    <header class="mb-4"><span class="pk-eyebrow">Ringkasan kegiatan</span><h1 class="h2 text-pk fw-bold mb-1">Rekap Program Kerja</h1><p class="text-secondary mb-0">Pilih program untuk melihat dan mengunduh rekap kegiatannya.</p></header>
    <div class="row g-3">
        @forelse ($programKerja as $proker)
            <div class="col-md-6 col-xl-4">
                <a class="pk-rekap-card" href="{{ route('pic.rekap.opsi', $proker) }}">
                    <div class="d-flex justify-content-between align-items-start gap-3"><span class="pk-rekap-icon"><i class="bi bi-clipboard-data"></i></span><i class="bi bi-arrow-up-right pk-rekap-arrow"></i></div>
                    <h2>{{ $proker->nama_proker }}</h2><p><i class="bi bi-calendar3 me-2"></i>{{ $proker->tanggal_pelaksanaan->translatedFormat('d F Y') }}</p>
                    <span class="pk-rekap-count"><strong>{{ $proker->pendaftaran_count }}</strong> peserta terdaftar</span>
                </a>
            </div>
        @empty
            <div class="col-12"><div class="pk-card bg-white text-center py-5 px-3"><i class="bi bi-clipboard-x fs-2 text-pk"></i><h2 class="h5 mt-3">Belum ada program untuk direkap</h2><p class="text-secondary mb-0">Rekap tersedia setelah program kerja dibuat.</p></div></div>
        @endforelse
    </div>
</div>
@endsection
@push('styles')
<style>
.pk-rekap-card{height:100%;display:block;padding:1.25rem 1.35rem;background:#fff;border:1px solid var(--pk-border);border-radius:1rem;box-shadow:var(--pk-shadow);text-decoration:none;color:inherit}.pk-rekap-card:hover{transform:translateY(-3px);border-color:#b8d1d3;box-shadow:0 14px 28px rgba(29,74,83,.11);color:inherit}.pk-rekap-icon{width:2.75rem;height:2.75rem;display:grid;place-items:center;border-radius:.85rem;background:#eaf4f4;color:var(--pk-teal);font-size:1.3rem}.pk-rekap-arrow{color:#849399}.pk-rekap-card h2{font-size:1.08rem;font-weight:700;margin:1.1rem 0 .45rem;color:var(--pk-teal)}.pk-rekap-card p{font-size:.9rem;color:var(--pk-muted);margin-bottom:1rem}.pk-rekap-count{display:inline-flex;padding:.35rem .7rem;border-radius:999px;background:#f2f7f7;color:#5f7177;font-size:.82rem}
</style>
@endpush