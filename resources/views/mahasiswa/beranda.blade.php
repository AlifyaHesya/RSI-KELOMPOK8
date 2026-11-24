@extends('layouts.mahasiswa')
@section('title', 'Beranda')

@section('content')
@php
    $mencari = collect(['q', 'kategori', 'tanggal', 'biro'])->contains(fn ($k) => request()->filled($k));
@endphp

<!-- Hero Section -->
<section class="container py-5">
    <div class="row g-5 align-items-center">
        <div class="col-lg-5">
            <div class="position-relative">
                <div class="w-100 rounded-4 shadow-sm overflow-hidden position-relative d-flex flex-column justify-content-end p-4 text-white"
                     style="height: 420px; background: #19565c;">
                    <img src="{{ asset('css/img/beranda.jpg') }}" alt="Mahasiswa mengikuti kegiatan program"
                         class="position-absolute top-0 start-0 w-100 h-100" style="object-fit: cover;">
                    <div class="position-absolute top-0 start-0 w-100 h-100" aria-hidden="true"
                         style="background: linear-gradient(to top, rgba(0,0,0,.72), transparent 65%);"></div>
                    <span class="h3 fw-bold position-relative">Program untuk berkembang.</span>
                    <span class="opacity-75 position-relative">Temukan kesempatan berikutnya.</span>
                </div>
                <span class="badge rounded-pill position-absolute top-0 start-0 m-3 px-3 py-2 text-white shadow-sm" 
                      style="background-color: var(--pk-teal);">
                    <i class="bi bi-stars me-1 text-pk-yellow"></i> Katalog Program Kerja
                </span>
            </div>
        </div>

        <div class="col-lg-7">
            <h1 class="display-5 fw-bold text-pk mb-3" style="line-height: 1.2;">
                Temukan program kerja yang tepat untuk karirmu
            </h1>
            <p class="text-secondary fs-6 mb-4" style="line-height: 1.6;">
                Jelajahi katalog program kerja BEM FILKOM UB, pelatihan, dan pengembangan karir. Dibuat untuk membantu kamu menemukan kesempatan yang sesuai dengan tujuanmu.
            </p>

            <div class="row g-3 my-4">
                <div class="col-4">
                    <div class="pk-stat-box bg-white p-3 p-md-4 rounded-4">
                        <div class="fs-4 fw-bold text-pk">{{ $ringkasan['program'] ?? '120+' }}</div>
                        <small class="text-secondary">Program aktif</small>
                    </div>
                </div>
                <div class="col-4">
                    <div class="pk-stat-box bg-white p-3 p-md-4 rounded-4">
                        <div class="fs-4 fw-bold text-pk">{{ $ringkasan['biro'] ?? '45+' }}</div>
                        <small class="text-secondary">Biro / Dept</small>
                    </div>
                </div>
                <div class="col-4">
                    <div class="pk-stat-box bg-white p-3 p-md-4 rounded-4">
                        <div class="fs-4 fw-bold text-pk">{{ $ringkasan['peserta'] ?? 'FILKOM' }}</div>
                        <small class="text-secondary">Pendaftaran</small>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-3 mt-4">
                <a href="#katalog" class="btn btn-pk rounded-pill px-4 py-2 fw-semibold">
                    Jelajahi Semua <i class="bi bi-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Katalog program kerja -->
<section id="katalog" class="container pb-5 pt-3">
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div>
            <h2 class="h3 fw-bold text-pk mb-1">{{ $mencari ? 'Hasil Pencarian' : 'Program Terbaru' }}</h2>
            <p class="text-secondary small mb-0">Pilih program yang sesuai dengan minat dan tujuan karirmu.</p>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            {{-- UC-04 filter kategori/biro and UC-08 filter tanggal --}}
            <form method="GET" action="{{ route('katalog.index') }}#katalog" class="d-flex flex-wrap gap-2">
                <input type="hidden" name="q" value="{{ request('q') }}">
                <input type="text" name="biro" value="{{ request('biro') }}" placeholder="Biro" class="form-control form-control-sm rounded-pill px-3 w-auto border-secondary-subtle">
                <select name="kategori" class="form-select form-select-sm rounded-pill px-5 w-auto border-secondary-subtle">
                    <option value="">Semua kategori</option>
                    @foreach ($kategori as $k)
                        <option value="{{ $k->value }}" @selected(request('kategori') === $k->value)>{{ $k->value }}</option>
                    @endforeach
                </select>
                <input type="date" name="tanggal" value="{{ request('tanggal') }}" 
                       class="form-control form-control-sm rounded-pill px-3 w-auto border-secondary-subtle">
                <button class="btn btn-pk btn-sm rounded-pill px-3">Terapkan</button>
                @if ($mencari)
                    <a href="{{ route('katalog.index') }}#katalog" class="btn btn-sm btn-link text-secondary text-decoration-none">Reset</a>
                @endif
            </form>

            <a href="{{ route('katalog.index') }}#katalog" class="btn btn-pk-yellow btn-sm rounded-pill px-3 py-2 fw-semibold text-white">
                Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>

    @if ($programKerja->isEmpty())
        <div class="pk-card bg-white p-5 text-center text-secondary">
            <i class="bi bi-inbox display-5"></i>
            <p class="mt-3 mb-0">Belum ada program kerja yang dipublikasikan.</p>
        </div>
    @else
        <div class="row g-4">
            @foreach ($programKerja as $p)
                @php
                    $isYellowBadge = $loop->iteration % 2 !== 0;
                @endphp
                <div class="col-md-6 col-lg-4">
                    <div class="pk-card bg-white p-3 h-100 d-flex flex-column transition-card">
                        <div class="rounded-3 overflow-hidden mb-3 position-relative bg-light d-flex align-items-center justify-content-center" style="height: 190px;">
                            @if ($p->poster_path)
                                <img src="{{ asset('storage/' . $p->poster_path) }}" 
                                     alt="{{ $p->nama_proker }}" 
                                     class="w-100 h-100" style="object-fit: cover;">
                            @else
                                <div class="w-100 h-100 d-flex align-items-center justify-content-center text-white"
                                     role="img" aria-label="{{ $p->nama_proker }}"
                                     style="background: linear-gradient(135deg, #216E75, #48a0a5);"><i class="bi bi-stars display-3"></i></div>
                            @endif
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge rounded-pill px-3 py-1 fw-semibold" 
                                  style="background-color: {{ $isYellowBadge ? 'var(--pk-yellow)' : 'var(--pk-teal)' }}; color: #fff; font-size: 0.72rem;">
                                {{ $p->kategori->value }}
                            </span>
                            <small class="text-secondary" style="font-size: 0.78rem;">{{ $p->lokasi }}</small>
                        </div>

                        @php($statusDaftar = $p->labelStatusPendaftaran())
                        <span class="badge align-self-start mb-2 {{ $statusDaftar === 'Pendaftaran dibuka' ? 'text-bg-success' : ($statusDaftar === 'Kuota penuh' || $statusDaftar === 'Pendaftaran ditutup' ? 'text-bg-secondary' : 'text-bg-warning') }}">{{ $statusDaftar }}</span>

                        <h3 class="h5 fw-bold text-pk mb-2">{{ $p->nama_proker }}</h3>
                        <p class="small text-secondary mb-4" style="line-height: 1.5;">
                            {{ str($p->deskripsi)->limit(105) }}
                        </p>

                        <div class="mt-auto d-flex flex-wrap gap-2">
                            <a href="{{ route('katalog.show', $p) }}" class="btn btn-pk-outline rounded-pill btn-sm px-3 py-1 fw-semibold">
                                Lihat Detail <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                            @auth
                                @if (auth()->user()->role?->value === 'MHS')
                                    @if ($p->cekStatusPendaftaran() && $p->cekKuotaTersedia())
                                        <a href="{{ route('pendaftaran.periksa', $p) }}" class="btn btn-pk-yellow rounded-pill btn-sm px-3 py-1 fw-semibold">Daftar</a>
                                    @else
                                        <button type="button" class="btn btn-outline-secondary rounded-pill btn-sm px-3 py-1" disabled>{{ $p->labelStatusPendaftaran() }}</button>
                                    @endif
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="btn btn-pk-yellow rounded-pill btn-sm px-3 py-1 fw-semibold">Masuk untuk daftar</a>
                            @endauth
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">{{ $programKerja->links('pagination::bootstrap-5') }}</div>
    @endif
</section>
@endsection
