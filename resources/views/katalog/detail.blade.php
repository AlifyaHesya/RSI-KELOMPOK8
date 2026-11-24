@extends('layouts.app')
@section('title', $programKerja->nama_proker)

@section('navbar-custom')
<div class="container d-flex align-items-center justify-content-between gap-4">
    <a href="{{ route('katalog.index') }}" class="navbar-brand text-white fw-bold fs-4 mb-0">SGE FILKOM</a>
    <form method="GET" action="{{ route('katalog.index') }}" class="detail-search d-none d-md-flex flex-grow-1">
        <i class="bi bi-search text-secondary"></i>
        <input type="search" name="q" value="{{ request('q') }}" class="form-control border-0 bg-transparent shadow-none" placeholder="Cari program kerja..." aria-label="Cari program kerja">
    </form>
    <div class="d-flex align-items-center gap-2">
        @auth
            <a href="{{ route('notifikasi.index') }}" class="btn btn-outline-light rounded-pill"><i class="bi bi-bell"></i><span class="visually-hidden">Notifikasi</span></a>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-light rounded-pill"><i class="bi bi-person"></i><span class="visually-hidden">Akun</span></a>
        @else
            <a href="{{ route('login') }}" class="btn btn-outline-warning rounded-pill px-3"><i class="bi bi-lock me-1"></i>Masuk</a>
            <a href="{{ route('register') }}" class="btn btn-pk-yellow rounded-pill px-3">Mulai <i class="bi bi-arrow-right ms-1"></i></a>
        @endauth
    </div>
</div>
@endsection

@push('styles')
<style>
    .detail-page { background: #f6f8f8; color: #263b46; }
    .detail-search { max-width: 430px; height: 50px; padding: 0 18px; display: flex; align-items: center; gap: 10px; border: 1px solid #dae0ec; border-radius: 30px; background: #f2f1fa; }
    .detail-poster { min-height: 420px; max-height: 620px; object-fit: cover; }
    .detail-placeholder { min-height: 420px; background: radial-gradient(circle at 25% 25%, rgba(245,180,0,.72), transparent 27%), linear-gradient(145deg,#19565c,#48a0a5); }
    .detail-panel, .detail-section, .detail-side-card { border: 1px solid #dce5e7; border-radius: 16px; background: white; }
    .detail-panel { padding: 20px; }
    .detail-icon { width: 40px; height: 40px; display:grid; place-items:center; flex: 0 0 auto; color:#1d8190; background:#edf7f7; border-radius:10px; }
    .detail-meta { color:#75858d; font-size:.86rem; }
    .detail-section { padding: 26px; }
    .detail-side-card { padding: 22px; }
    @media (max-width: 767.98px) { .detail-poster,.detail-placeholder { min-height:260px; max-height:360px; } .detail-section { padding:20px; } }
</style>
@endpush

@section('content')
@php
    $statusDaftar = $programKerja->labelStatusPendaftaran();
    $pendaftaranDibuka = $programKerja->cekStatusPendaftaran() && $programKerja->cekKuotaTersedia();
@endphp
<div class="detail-page py-4 py-lg-5">
    <div class="container-xl">
        <a href="{{ route('katalog.index') }}" class="btn btn-light border rounded-3 mb-4"><i class="bi bi-arrow-left me-2 text-pk"></i>Kembali</a>
        @if (session('pesan'))<div class="alert alert-warning">{{ session('pesan') }}</div>@endif
        @if (session('konfirmasi'))<div class="alert {{ session('konfirmasi')['berhasil'] ? 'alert-success' : 'alert-danger' }}">{{ session('konfirmasi')['pesan'] }}</div>@endif

        <div class="row g-4 g-xl-5 align-items-start">
            <div class="col-lg-6">
                @if ($programKerja->poster_path)
                    <img src="{{ asset('storage/'.$programKerja->poster_path) }}" alt="Poster {{ $programKerja->nama_proker }}" class="detail-poster w-100 rounded-4 shadow-sm">
                @else
                    <div class="detail-placeholder w-100 rounded-4 shadow-sm d-flex flex-column justify-content-end p-4 text-white"><i class="bi bi-stars display-2 mb-auto"></i><span class="h3 fw-bold">{{ $programKerja->nama_proker }}</span><span class="opacity-75">Program untuk berkembang.</span></div>
                @endif
                <div class="d-flex justify-content-between align-items-center gap-3 mt-3 small detail-meta">
                    <span>{{ $programKerja->biro }} · {{ $programKerja->lokasi }}</span>
                    <span class="text-pk"><i class="bi bi-image me-1"></i>Poster program</span>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
                    <span class="badge rounded-pill px-3 py-2" style="background:#eaf4f4;color:#247984">{{ $programKerja->kategori->value }}</span>
                    <span class="small fw-semibold {{ $pendaftaranDibuka ? 'text-success' : 'text-secondary' }}"><i class="bi bi-circle-fill me-1" style="font-size:.5rem"></i>{{ $statusDaftar }}</span>
                </div>
                <h1 class="display-6 fw-semibold text-pk">{{ $programKerja->nama_proker }}</h1>
                <p class="text-secondary lh-lg">{{ $programKerja->deskripsi ?: 'Informasi lengkap program kerja ini dapat dilihat pada bagian deskripsi.' }}</p>

                <div class="d-flex align-items-center gap-3 border-bottom pb-3 mb-3">
                    <span class="detail-icon"><i class="bi bi-person-workspace"></i></span>
                    <div><small class="detail-meta d-block">Diselenggarakan oleh</small><strong>{{ $programKerja->pic->pengguna->name }}</strong></div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-sm-6 d-flex gap-2"><span class="detail-icon"><i class="bi bi-calendar3"></i></span><div><small class="detail-meta d-block">Tanggal pelaksanaan</small><strong>{{ $programKerja->tanggal_pelaksanaan->translatedFormat('d F Y') }}</strong></div></div>
                    <div class="col-sm-6 d-flex gap-2"><span class="detail-icon"><i class="bi bi-clock"></i></span><div><small class="detail-meta d-block">Waktu pelaksanaan</small><strong>{{ substr($programKerja->waktu_pelaksanaan, 0, 5) }} WIB</strong></div></div>
                    <div class="col-sm-6 d-flex gap-2"><span class="detail-icon"><i class="bi bi-geo-alt"></i></span><div><small class="detail-meta d-block">Lokasi</small><strong>{{ $programKerja->lokasi }}</strong></div></div>
                    <div class="col-sm-6 d-flex gap-2"><span class="detail-icon"><i class="bi bi-people"></i></span><div><small class="detail-meta d-block">Kuota peserta</small><strong>{{ $programKerja->pendaftaran_count }} / {{ $programKerja->kuota }} peserta</strong></div></div>
                </div>

                <div class="detail-panel mb-3">
                    <div class="d-flex justify-content-between align-items-start"><div><small class="detail-meta">Periode pendaftaran</small><div class="fw-semibold">{{ $programKerja->tanggal_buka_pendaftaran->translatedFormat('d F Y, H:i') }} – {{ $programKerja->batas_pendaftaran->translatedFormat('d F Y, H:i') }}</div></div><span class="h6 text-pk mb-0">WIB</span></div>
                    <div class="rounded-2 p-2 mt-3 small" style="background:#fff6dc"><i class="bi bi-hourglass-split me-2"></i>Batas akhir: {{ $programKerja->batas_pendaftaran->translatedFormat('d F Y, H:i') }} WIB</div>
                    @auth
                        @if (auth()->user()->getRawOriginal('role') === 'MHS')
                            @if ($pendaftaranDibuka)
                                <a href="{{ route('pendaftaran.periksa', $programKerja) }}" class="btn btn-pk w-100 mt-3 py-2">Daftar Sekarang <i class="bi bi-arrow-up-right ms-1"></i></a>
                            @else
                                <button class="btn btn-secondary w-100 mt-3" disabled>{{ $statusDaftar }}</button>
                            @endif
                            <a href="{{ route('pengingat.form', $programKerja) }}" class="btn btn-light border w-100 mt-2"><i class="bi bi-bell me-2"></i>Atur pengingat</a>
                        @else
                            <div class="alert alert-light border small mt-3 mb-0">Masuk dengan akun mahasiswa untuk mendaftar.</div>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn btn-pk w-100 mt-3 py-2">Masuk untuk mendaftar <i class="bi bi-arrow-up-right ms-1"></i></a>
                        <div class="text-center detail-meta mt-2">Belum punya akun? <a href="{{ route('register') }}" class="text-pk">Daftar di sini</a></div>
                    @endauth
                </div>
                @if (auth()->check() && auth()->user()->getRawOriginal('role') === 'MHS' && ! $pendaftaranDibuka)
                    <a href="{{ route('pengingat.form', $programKerja) }}" class="small text-pk text-decoration-none">Ingatkan saya sebelum pendaftaran ditutup</a>
                @endif
            </div>
        </div>

        <div class="row g-4 mt-3">
            <div class="col-lg-8"><section class="detail-section"><div class="d-flex gap-4 border-bottom mb-4"><span class="pb-2 border-bottom border-2 border-info text-pk fw-semibold">Deskripsi Program</span><span class="pb-2 detail-meta">Informasi kegiatan</span></div><h2 class="h4 fw-bold">Tentang program</h2><p class="text-secondary lh-lg mb-0">{{ $programKerja->deskripsi ?: 'Belum ada deskripsi tambahan untuk program ini.' }}</p></section></div>
            <aside class="col-lg-4"><section class="detail-side-card"><h2 class="h6 fw-bold mb-3">Catat tanggal pentingnya</h2><div class="small text-pk mb-1">{{ $programKerja->tanggal_buka_pendaftaran->translatedFormat('d F Y, H:i') }}</div><div class="fw-semibold mb-3">Pendaftaran dibuka</div><div class="small text-pk mb-1">{{ $programKerja->tanggal_pelaksanaan->translatedFormat('d F Y') }}</div><div class="fw-semibold">Pelaksanaan program</div><hr><div class="small detail-meta">Pendaftaran ditutup {{ $programKerja->batas_pendaftaran->translatedFormat('d F Y, H:i') }} WIB.</div></section></aside>
        </div>
    </div>
</div>
@endsection
