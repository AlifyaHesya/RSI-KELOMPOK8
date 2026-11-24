@extends('layouts.app')
@section('title', 'Notifikasi')
@section('content')
<div class="container py-5" style="max-width: 960px">
    <header class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div><span class="pk-eyebrow">Pusat informasi</span><h1 class="h2 text-pk fw-bold mb-1">Notifikasi</h1><p class="text-secondary mb-0">Pembaruan terkait program kerja dan pendaftaran Anda.</p></div>
        @if($notifikasi->where('sudah_dibaca', false)->isNotEmpty())
            <form method="POST" action="{{ route('notifikasi.dibaca-semua') }}">@csrf @method('PATCH')<button class="btn btn-outline-primary"><i class="bi bi-check2-all me-2"></i>Tandai semua dibaca</button></form>
        @endif
    </header>
    <section class="pk-card bg-white overflow-hidden">
        @forelse($notifikasi as $item)
            <article class="pk-notification {{ $item->sudah_dibaca ? '' : 'is-unread' }}">
                <span class="pk-notification-icon"><i class="bi {{ $item->sudah_dibaca ? 'bi-bell' : 'bi-bell-fill' }}"></i></span>
                <div class="flex-grow-1"><div class="d-flex flex-wrap align-items-center gap-2"><strong>{{ $item->judul ?? 'Pembaruan ProgramKita' }}</strong>@unless($item->sudah_dibaca)<span class="pk-unread-tag">Baru</span>@endunless</div><p>{{ $item->pesan ?? $item->isi ?? 'Ada pembaruan untuk akun Anda.' }}</p><small><i class="bi bi-clock me-1"></i>{{ $item->created_at?->translatedFormat('d M Y, H:i') }}</small></div>
                @unless($item->sudah_dibaca)<form method="POST" action="{{ route('notifikasi.dibaca', $item) }}">@csrf @method('PATCH')<button class="btn btn-sm btn-outline-secondary">Tandai dibaca</button></form>@endunless
            </article>
        @empty
            <div class="text-center py-5 px-3"><span class="pk-notification-empty"><i class="bi bi-bell-slash"></i></span><h2 class="h5 mt-3">Belum ada notifikasi</h2><p class="text-secondary mb-0">Kami akan menampilkan pembaruan akun Anda di sini.</p></div>
        @endforelse
    </section>
    <div class="mt-4 d-flex justify-content-center">{{ $notifikasi->links() }}</div>
</div>
@endsection
@push('styles')
<style>
.pk-notification{display:flex;align-items:flex-start;gap:1rem;padding:1.2rem 1.35rem;border-bottom:1px solid #e9efef}.pk-notification:last-child{border-bottom:0}.pk-notification.is-unread{background:#f4f9f9}.pk-notification-icon,.pk-notification-empty{width:2.7rem;height:2.7rem;display:grid;place-items:center;flex:none;border-radius:50%;background:#e5f1f1;color:var(--pk-teal);font-size:1.15rem}.pk-notification p{margin:.35rem 0;color:#526168}.pk-notification small{color:var(--pk-muted)}.pk-unread-tag{padding:.16rem .55rem;border-radius:999px;background:#fff4ce;color:#8a6500;font-size:.7rem;font-weight:700}.pk-notification-empty{width:3.5rem;height:3.5rem;margin:auto;font-size:1.4rem}@media(max-width:575.98px){.pk-notification{flex-wrap:wrap;padding:1rem}.pk-notification form{margin-left:3.7rem}}
</style>
@endpush