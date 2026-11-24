{{-- Konfirmasi hasil pendaftaran (UC-06). --}}
@extends('layouts.mahasiswa')
@section('title', 'Konfirmasi Pendaftaran')
@section('tanpa-pencarian', '1')

@section('content')
<div class="container py-5" style="max-width: 640px">
    <div id="tampilkanKonfirmasi" class="pk-card bg-white p-5 text-center">
        @if ($statusSimpan)
            <i class="bi bi-check-circle-fill display-3 text-success"></i>
            <h1 class="h3 fw-bold text-pk mt-3">Pendaftaran Berhasil</h1>
            <p class="text-secondary">Kamu telah terdaftar pada program berikut.</p>
            @isset($pendaftaran)
                <div class="pk-soft-card p-3 text-start small mb-4">
                    <div class="fw-bold">{{ $pendaftaran->programKerja->nama_proker }}</div>
                    <div class="text-secondary">Didaftarkan pada {{ $pendaftaran->tanggal_pendaftaran->translatedFormat('d F Y, H.i') }} WIB</div>
                </div>
            @endisset
        @else
            <i class="bi bi-x-circle-fill display-3 text-danger"></i>
            <h1 class="h3 fw-bold mt-3">Pendaftaran Gagal</h1>
            <p class="text-secondary">Pendaftaran tidak dapat disimpan. Silakan coba lagi.</p>
        @endif

        <a href="{{ route('katalog.index') }}" class="btn btn-pk rounded-pill px-4">Kembali ke Beranda</a>
    </div>
</div>
@endsection
