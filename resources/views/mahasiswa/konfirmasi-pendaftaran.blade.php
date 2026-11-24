{{-- Konfirmasi hasil pendaftaran (UC-06). --}}
@extends('layouts.app')
@section('title', 'Pendaftaran Terkirim')

@section('navbar-custom')
<div class="container d-flex justify-content-between align-items-center">
    <a href="{{ route('katalog.index') }}" class="navbar-brand text-white fw-bold fs-4 mb-0">SGE FILKOM</a>
    <div class="d-flex gap-2"><a href="{{ route('notifikasi.index') }}" class="pk-nav-icon" aria-label="Notifikasi"><i class="bi bi-bell"></i></a><a href="{{ route('dashboard') }}" class="pk-nav-icon" aria-label="Akun"><i class="bi bi-person"></i></a></div>
</div>
@endsection

@push('styles')
<style>
    .confirmation-page { min-height:100%; background:#f5f8f9; color:#263b46; }
    .pk-nav-icon { width:40px;height:40px;display:grid;place-items:center;color:#207883;background:#fff;border-radius:50%;text-decoration:none;font-size:1.1rem; }
    .confirmation-card,.confirmation-side { border:1px solid #dce6e9; border-radius:18px; background:#fff; }
    .confirmation-side { padding:24px; }
    .confirmation-main { padding:26px; }
    .confirmation-icon { width:40px;height:40px;display:grid;place-items:center;flex:0 0 auto;color:#247984;background:#edf7f7;border-radius:10px; }
    .confirmation-step { min-height:82px; padding:14px; border:1px solid #dce5e8; border-radius:13px; color:#247984; }
    .confirmation-step.active { background:#247984; border-color:#247984; color:#fff; }
    .confirmation-check { width:58px;height:58px;display:grid;place-items:center;border-radius:50%;background:#eaf6f4;color:#247984;font-size:1.6rem;margin:auto; }
    .confirmation-info { border:1px solid #dce6e9; border-radius:13px; padding:18px; }
    @media(max-width:575.98px){.confirmation-main,.confirmation-side{padding:19px}}
</style>
@endpush

@section('content')
<div class="confirmation-page py-4 py-lg-5">
    <div class="container-xxl px-lg-4">
        <div class="small text-secondary mb-4"><a href="{{ route('pendaftaran.index') }}" class="text-decoration-none text-secondary">Pendaftaran saya</a><span class="mx-2">/</span><span class="text-pk">Konfirmasi</span></div>
        <div class="row g-4 align-items-start">
            <aside class="col-lg-4">
                <div class="confirmation-side mb-3">
                    <div class="d-flex align-items-center gap-3 mb-4"><span class="confirmation-icon"><i class="bi bi-mortarboard"></i></span><div><strong class="d-block text-pk">Pendaftaran mahasiswa</strong><small class="text-secondary">Ringkasan data pendaftaran Anda</small></div></div>
                    <div class="rounded-4 p-4 text-white mb-3" style="background:#247984"><div class="small text-uppercase mb-3"><i class="bi bi-bookmark-check me-2"></i>Program pilihan</div><h2 class="h5 fw-semibold">{{ $pendaftaran->programKerja->nama_proker }}</h2><p class="small mb-0 opacity-75">{{ $pendaftaran->programKerja->kategori->value }} · {{ $pendaftaran->programKerja->biro }}</p></div>
                    <h3 class="h6 fw-bold text-pk">Informasi pendaftaran</h3>
                    <div class="small text-secondary">Dikirim pada</div><div class="fw-semibold mb-3">{{ $pendaftaran->tanggal_pendaftaran->translatedFormat('d F Y, H:i') }} WIB</div>
                    <div class="small text-secondary">Pelaksanaan program</div><div class="fw-semibold">{{ $pendaftaran->programKerja->tanggal_pelaksanaan->translatedFormat('d F Y') }} · {{ substr($pendaftaran->programKerja->waktu_pelaksanaan,0,5) }} WIB</div>
                </div>
                <div class="confirmation-side"><h3 class="h6 fw-bold text-pk">Langkah selanjutnya</h3><div class="d-flex gap-3 align-items-start mt-3"><span class="badge rounded-pill text-bg-success">1</span><div><strong class="small">Pendaftaran tercatat</strong><div class="small text-secondary">Data Anda sudah masuk ke daftar peserta.</div></div></div><div class="d-flex gap-3 align-items-start mt-3"><span class="badge rounded-pill text-bg-light">2</span><div><strong class="small">Pantau informasi program</strong><div class="small text-secondary">Cek notifikasi dan detail program menjelang pelaksanaan.</div></div></div></div>
            </aside>

            <main class="col-lg-8">
                <div class="confirmation-card">
                    <div class="confirmation-main">
                        <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-4"><div><h1 class="h3 fw-bold text-pk mb-1">Pendaftaran Terkirim</h1><p class="text-secondary mb-0">Terima kasih, formulir pendaftaran Anda berhasil dikirim.</p></div><span class="badge rounded-pill text-bg-success"><i class="bi bi-check-circle me-1"></i>Berhasil dikirim</span></div>
                        <div class="row g-2 mb-4">
                            <div class="col-md-4"><div class="confirmation-step"><small class="fw-semibold"><i class="bi bi-check2-circle me-1"></i>Selesai</small><strong class="d-block mt-2">Pilih program</strong></div></div>
                            <div class="col-md-4"><div class="confirmation-step"><small class="fw-semibold"><i class="bi bi-check2-circle me-1"></i>Selesai</small><strong class="d-block mt-2">Isi data</strong></div></div>
                            <div class="col-md-4"><div class="confirmation-step active"><small class="fw-semibold text-warning"><i class="bi bi-check2 me-1"></i>Terkirim</small><strong class="d-block mt-2">Konfirmasi</strong></div></div>
                        </div>

                        <div class="text-center rounded-4 p-4 mb-4" style="background:#f5f9f9"><span class="confirmation-check"><i class="bi bi-check-lg"></i></span><h2 class="h4 fw-bold text-pk mt-3">Pendaftaran Anda berhasil!</h2><p class="text-secondary mb-0">Pendaftaran untuk <strong>{{ $pendaftaran->programKerja->nama_proker }}</strong> telah tercatat. Anda tidak perlu mengirim formulir lagi.</p></div>

                        <section class="confirmation-info mb-3">
                            <div class="d-flex justify-content-between align-items-center gap-2 mb-3"><h2 class="h6 fw-bold text-pk mb-0">Status pendaftaran</h2><span class="badge rounded-pill text-bg-success">Terdaftar</span></div>
                            <div class="row g-3 small"><div class="col-sm-4 text-secondary">Nama lengkap</div><div class="col-sm-8 fw-semibold">{{ $pendaftaran->mahasiswa->pengguna->name }}</div><div class="col-sm-4 text-secondary">Program studi</div><div class="col-sm-8 fw-semibold">{{ $pendaftaran->mahasiswa->program_studi }}</div><div class="col-sm-4 text-secondary">Email terdaftar</div><div class="col-sm-8 fw-semibold">{{ $pendaftaran->mahasiswa->pengguna->email }}</div></div>
                        </section>

                        <div class="alert alert-info small mb-4"><i class="bi bi-info-circle me-2"></i>Status berhasil dikirim berarti pendaftaran sudah tercatat. Pantau notifikasi website untuk informasi terbaru dari penyelenggara.</div>
                        <div class="d-flex justify-content-between gap-2 flex-wrap"><a href="{{ route('katalog.index') }}" class="btn btn-light border"><i class="bi bi-house me-2"></i>Kembali ke Beranda</a><a href="{{ route('pendaftaran.index') }}" class="btn btn-pk"><i class="bi bi-card-list me-2"></i>Lihat Pendaftaran</a></div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>
@endsection
