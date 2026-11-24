@extends('layouts.app')

@section('title', 'Pendaftaran Saya')

@section('content')
<div class="container py-4">
    <h1 class="h3 mb-1">Pendaftaran Saya</h1>
    <p class="text-muted mb-4">Pantau program kerja yang sudah Anda daftarkan.</p>
    <div class="row g-3">
        @forelse($pendaftaran as $item)
            <div class="col-md-6 col-xl-4"><article class="card h-100 border-0 shadow-sm"><div class="card-body">
                <span class="badge {{ $item->status_pendaftaran ? 'text-bg-success' : 'text-bg-secondary' }} mb-2">{{ $item->status_pendaftaran ? 'Terdaftar' : 'Tidak aktif' }}</span>
                <h2 class="h5">{{ $item->programKerja->nama_proker }}</h2>
                <div class="text-muted small">{{ $item->programKerja->biro }} · {{ $item->programKerja->tanggal_pelaksanaan?->translatedFormat('d M Y') }}</div>
                <div class="small mt-2">Mendaftar {{ $item->tanggal_pendaftaran?->translatedFormat('d M Y, H:i') }}</div>
                <a class="btn btn-sm btn-outline-primary mt-3" href="{{ route('katalog.show', $item->programKerja) }}">Lihat program</a>
            </div></article></div>
        @empty
            <div class="col-12"><div class="card border-0 shadow-sm"><div class="card-body text-center py-5"><p class="text-muted mb-3">Anda belum mendaftar program kerja.</p><a class="btn btn-primary" href="{{ route('katalog.index') }}">Jelajahi program</a></div></div></div>
        @endforelse
    </div>
    <div class="mt-3">{{ $pendaftaran->links() }}</div>
</div>
@endsection
