@use('App\Enums\Role')
@extends('layouts.app')
@section('title', 'Katalog Program Kerja')

@section('tombol')
    @auth
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-outline-light rounded-pill btn-sm px-3">
                <i class="bi bi-box-arrow-right me-1"></i> Keluar
            </button>
        </form>
    @else
        <a href="{{ route('login') }}" class="btn btn-outline-light rounded-pill btn-sm px-3">Masuk</a>
    @endauth
@endsection

@section('content')
<div class="container py-5">
    <h1 class="h2 fw-bold text-pk mb-4">Katalog Program Kerja</h1>

    <form method="GET" action="{{ route('katalog.index') }}" class="pk-card bg-white p-3 mb-4">
        <div class="row g-2">
            <div class="col-md-4">
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama program kerja">
            </div>
            <div class="col-md-3">
                <select name="kategori" class="form-select">
                    <option value="">Semua kategori</option>
                    @foreach ($kategori as $k)
                        <option value="{{ $k->value }}" @selected(request('kategori') === $k->value)>{{ $k->value }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="text" name="biro" value="{{ request('biro') }}" class="form-control" placeholder="Biro">
            </div>
            <div class="col-md-2">
                <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="form-control">
            </div>
            <div class="col-md-1 d-grid">
                <button class="btn btn-pk"><i class="bi bi-search"></i></button>
            </div>
        </div>
    </form>

    @forelse ($programKerja as $p)
        @if ($loop->first)<div class="row g-4">@endif
        <div class="col-md-6 col-lg-4">
            <div class="pk-card bg-white p-4 h-100 d-flex flex-column">
                <span class="badge text-bg-light text-pk align-self-start mb-2">{{ $p->kategori->value }}</span>
                <h2 class="h5 fw-bold">{{ $p->nama_proker }}</h2>
                <p class="small text-secondary mb-3">{{ \Illuminate\Support\Str::limit($p->deskripsi, 90) }}</p>
                <ul class="list-unstyled small text-secondary mt-auto mb-3">
                    <li><i class="bi bi-calendar-event me-2"></i>{{ $p->tanggal_pelaksanaan->translatedFormat('d F Y') }}, {{ substr($p->waktu_pelaksanaan, 0, 5) }}</li>
                    <li><i class="bi bi-geo-alt me-2"></i>{{ $p->lokasi }}</li>
                    <li><i class="bi bi-diagram-3 me-2"></i>{{ $p->biro }}</li>
                </ul>
                <a href="{{ route('katalog.show', $p) }}" class="btn btn-pk">Lihat detail</a>
            </div>
        </div>
        @if ($loop->last)</div>@endif
    @empty
        <div class="pk-card bg-white p-5 text-center text-secondary">
            <i class="bi bi-inbox display-5"></i>
            <p class="mt-3 mb-0">Belum ada program kerja yang dipublikasikan.</p>
        </div>
    @endforelse

    <div class="mt-4">{{ $programKerja->links('pagination::bootstrap-5') }}</div>
</div>
@endsection
