@extends('layouts.app')
@section('title', 'Pengajuan Program Kerja')
@section('content')
<div class="pic-shell">
    <aside class="pic-sidebar" aria-label="Navigasi utama">
        <a href="{{ route('pic.proker.index') }}"><i class="bi bi-house-door"></i> Dashboard</a>
        <a class="active" href="{{ route('pic.pengajuan.index') }}" aria-current="page"><i class="bi bi-file-earmark-text"></i> Pengajuan</a>
        <a href="{{ route('pic.rekap.index') }}"><i class="bi bi-bar-chart-line"></i> Laporan Program Kerja</a>
        <a href="{{ route('notifikasi.index') }}"><i class="bi bi-gear"></i> Pengaturan</a>
    </aside>
    <section class="pic-main pj-page">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div><h1 class="h2 text-pk fw-bold mb-1">Pengajuan Program Kerja</h1><p class="text-secondary mb-0">Kelola dan pantau program kerja Anda.</p></div>
            <a href="{{ route('pic.proker.create') }}" class="btn btn-pk"><i class="bi bi-plus-lg me-2"></i>Buat Program Kerja</a>
        </div>
        @if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        @if (session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
        <div class="pj-toolbar mb-4">
            <label class="pj-search"><i class="bi bi-search" aria-hidden="true"></i><span class="visually-hidden">Cari program</span><input id="pj-search" type="search" class="form-control" placeholder="Cari nama program yang diajukan..." autocomplete="off"></label>
            <select id="pj-status" class="form-select pj-status" aria-label="Filter status">
                <option value="">Semua Status</option>
                @foreach ($pengajuan->pluck('status_pengajuan')->filter()->map(fn ($status) => $status->value)->unique() as $status)
                    <option value="{{ mb_strtolower($status) }}">{{ $status }}</option>
                @endforeach
            </select>
        </div>
        <div id="pj-list">
            @forelse ($pengajuan as $item)
                @php($program = $item->programKerja)
                @php($status = $item->status_pengajuan->value)
                @php($badge = match ($status) { 'Disetujui' => 'st-disetujui', 'Ditolak' => 'st-ditolak', 'Menunggu Verifikasi' => 'st-menunggu', default => 'st-draft' })
                <article class="pj-card pj-item" data-name="{{ mb_strtolower($item->nama_proker) }}" data-status="{{ mb_strtolower($status) }}">
                    @if ($program?->poster_path)
                        <img class="pj-image" src="{{ asset('storage/'.$program->poster_path) }}" alt="Poster {{ $item->nama_proker }}">
                    @else
                        <div class="pj-image pj-placeholder" aria-hidden="true"><i class="bi bi-image"></i><span>Program Kita</span></div>
                    @endif
                    <div class="pj-content">
                        <div class="pj-heading"><div><h2>{{ $item->nama_proker }}</h2><span class="pj-meta">PIC: {{ $program?->pic?->pengguna?->name ?? auth()->user()->name }}</span></div><span class="st-badge {{ $badge }}"><i class="bi bi-circle-fill"></i>{{ $status }}</span></div>
                        <p class="pj-description">{{ $program?->deskripsi ?: 'Belum ada deskripsi program.' }}</p>
                        <div class="pj-footer"><span class="pj-date"><i class="bi bi-calendar3"></i>{{ $item->updated_at->translatedFormat('d F Y') }}</span><a class="btn btn-pk btn-sm" href="{{ route('pic.pengajuan.show', $item) }}">Lihat Detail <i class="bi bi-arrow-right ms-1"></i></a></div>
                    </div>
                </article>
            @empty
                <div class="pj-empty"><i class="bi bi-inbox"></i><h2>Belum ada pengajuan</h2><p>Program kerja yang diajukan akan muncul di halaman ini.</p><a href="{{ route('pic.proker.create') }}" class="btn btn-pk">Buat Program Kerja</a></div>
            @endforelse
            <div id="pj-no-results" class="pj-empty d-none"><i class="bi bi-search"></i><h2>Pengajuan tidak ditemukan</h2><p>Coba ubah kata kunci atau filter status.</p></div>
        </div>
    </section>
</div>
@endsection
@push('styles')
<style>
.pj-page{padding:1.75rem 2.5rem}.pj-toolbar{display:flex;align-items:center;justify-content:space-between;gap:1rem}.pj-search{position:relative;flex:1;max-width:540px}.pj-search>i{position:absolute;left:1rem;top:50%;transform:translateY(-50%);color:#8989a8}.pj-search .form-control{border:1px solid #d7d4e9;border-radius:999px;background:#fff;padding:.8rem 1rem .8rem 2.8rem}.pj-status{width:auto;min-width:165px;border:1px solid #d7d4e9;border-radius:999px;background:#fff;padding:.75rem 2.4rem .75rem 1rem}.pj-card{display:flex;min-height:210px;margin-bottom:.85rem;overflow:hidden;background:#fff;border:1px solid #d8d7e9;border-radius:1.2rem;box-shadow:0 4px 14px rgba(29,74,83,.04)}.pj-image{width:280px;flex:none;object-fit:cover;background:#e8f3f3}.pj-placeholder{display:flex;flex-direction:column;align-items:center;justify-content:center;gap:.5rem;color:var(--pk-teal);font-size:2rem}.pj-placeholder span{font-size:.9rem;font-weight:700}.pj-content{display:flex;flex:1;min-width:0;flex-direction:column;padding:1.35rem 1.5rem}.pj-heading,.pj-footer{display:flex;align-items:center;justify-content:space-between;gap:1rem}.pj-heading{align-items:flex-start}.pj-heading h2{margin:0 0 .2rem;color:var(--pk-teal);font-size:1.2rem;font-weight:700}.pj-meta{color:#8585a6;font-size:.85rem}.pj-description{margin:1rem 0;color:#5b5d80;font-size:.92rem;line-height:1.6}.pj-footer{margin-top:auto}.pj-date{display:flex;align-items:center;gap:.5rem;color:#8989a8;font-size:.85rem}.pj-card .btn{border-radius:.7rem;padding:.55rem 1rem;font-weight:600}.pj-empty{text-align:center;padding:3.5rem 1rem;background:white;border:1px solid #e3eeee;border-radius:1.2rem}.pj-empty>i{display:block;margin-bottom:.75rem;color:var(--pk-teal);font-size:2rem}.pj-empty h2{font-size:1.15rem}.pj-empty p{color:#78808e}@media(max-width:991.98px){.pj-page{padding:1.25rem}}@media(max-width:767.98px){.pj-toolbar{align-items:stretch;flex-direction:column}.pj-search{max-width:none}.pj-card{flex-direction:column}.pj-image{width:100%;height:180px}.pj-content{padding:1rem}.pj-heading{align-items:flex-start;flex-direction:column-reverse}.pj-footer{align-items:flex-start;flex-direction:column}}
</style>
@endpush
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const search = document.getElementById('pj-search');
    const status = document.getElementById('pj-status');
    const items = [...document.querySelectorAll('.pj-item')];
    const empty = document.getElementById('pj-no-results');
    const filter = () => {
        const query = search.value.trim().toLocaleLowerCase('id');
        let visible = 0;
        items.forEach(item => {
            const matches = item.dataset.name.includes(query) && (!status.value || item.dataset.status === status.value);
            item.classList.toggle('d-none', !matches);
            if (matches) visible++;
        });
        empty.classList.toggle('d-none', visible > 0 || items.length === 0);
    };
    search.addEventListener('input', filter);
    status.addEventListener('change', filter);
});
</script>
@endpush

