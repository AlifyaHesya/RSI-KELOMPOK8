@use('App\Enums\Role')
@extends('layouts.app')
@section('title', $programKerja->nama_proker)

@section('tombol')
    <a href="{{ route('katalog.index') }}" class="btn btn-outline-light rounded-pill btn-sm px-3">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Katalog
    </a>
@endsection

@section('content')
<div class="container py-5" style="max-width: 860px">

    {{-- flash: pesan (alasan), konfirmasi pengingat, sukses --}}
    @if (session('pesan'))
        <div class="alert alert-warning">{{ session('pesan') }}</div>
    @endif
    @if (session('konfirmasi'))
        <div class="alert {{ session('konfirmasi')['berhasil'] ? 'alert-success' : 'alert-danger' }}">
            {{ session('konfirmasi')['pesan'] }}
        </div>
    @endif

    <div class="pk-card bg-white p-4 p-md-5">
        <span class="badge text-bg-light text-pk mb-2">{{ $programKerja->kategori->value }}</span>
        <h1 class="h2 fw-bold text-pk">{{ $programKerja->nama_proker }}</h1>
        <p class="text-secondary">{{ $programKerja->deskripsi }}</p>

        <div class="row g-3 my-3 small">
            <div class="col-md-6"><i class="bi bi-calendar-event me-2 text-pk"></i>
                {{ $programKerja->tanggal_pelaksanaan->translatedFormat('d F Y') }}, {{ substr($programKerja->waktu_pelaksanaan, 0, 5) }}</div>
            <div class="col-md-6"><i class="bi bi-geo-alt me-2 text-pk"></i>{{ $programKerja->lokasi }}</div>
            <div class="col-md-6"><i class="bi bi-diagram-3 me-2 text-pk"></i>{{ $programKerja->biro }}</div>
            <div class="col-md-6"><i class="bi bi-people me-2 text-pk"></i>Kuota {{ $programKerja->kuota }} peserta</div>
            <div class="col-md-6"><i class="bi bi-hourglass-split me-2 text-pk"></i>
                Pendaftaran ditutup {{ $programKerja->batas_pendaftaran->translatedFormat('d F Y, H:i') }}</div>
            <div class="col-md-6"><i class="bi bi-person-badge me-2 text-pk"></i>PIC: {{ $programKerja->pic->user->name }}</div>
        </div>

        <hr>

        @guest
            <a href="{{ route('login') }}" class="btn btn-pk-yellow fw-semibold">Masuk untuk mendaftar</a>
        @endguest

        @auth
            @if (auth()->user()->role === Role::Mahasiswa)
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('pendaftaran.periksa', $programKerja) }}" class="btn btn-pk-yellow fw-semibold">
                        <i class="bi bi-pencil-square me-1"></i> Daftar
                    </a>
                    <a href="{{ route('pengingat.form', $programKerja) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-bell me-1"></i> Aktifkan Pengingat
                    </a>
                </div>
            @endif
        @endauth
    </div>
</div>
@endsection