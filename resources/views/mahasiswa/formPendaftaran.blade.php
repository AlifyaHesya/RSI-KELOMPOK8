{{-- Form pendaftaran mahasiswa (UC-06). --}}
@extends('layouts.mahasiswa')
@section('title', 'Pendaftaran ' . $programKerja->nama_proker)
@section('tanpa-pencarian', '1')

@section('content')
@php
    $prodi = ['Teknik Informatika', 'Teknik Komputer', 'Sistem Informasi', 'Teknologi Informasi', 'Pendidikan Teknologi Informasi'];
@endphp

<div class="container py-4">
    <div class="row g-4">

        <div class="col-lg-4">
            <div class="pk-card bg-white p-4 mb-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <span class="pk-info-icon" style="background: var(--pk-teal); color: #fff"><i class="bi bi-mortarboard"></i></span>
                    <div><strong class="text-pk d-block">Pendaftaran Mahasiswa</strong>
                        <small class="text-secondary">Lengkapi data diri untuk mendaftar</small></div>
                </div>
                <div class="pk-side-teal p-4">
                    <h2 class="h4 fw-bold">Informasi Pendaftaran</h2>
                    <p class="small mb-0 opacity-75">
                        Kamu mendaftar ke <strong>{{ $programKerja->nama_proker }}</strong>.
                        Batas pendaftaran {{ $programKerja->batas_pendaftaran->translatedFormat('d F Y, H.i') }} WIB.
                    </p>
                </div>
            </div>

            <div class="pk-card bg-white p-4 mb-4">
                <h3 class="h6 fw-bold text-pk">Syarat Pendaftaran</h3>
                <ul class="list-unstyled small mb-0">
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-warning me-2"></i>Sudah memiliki akun mahasiswa yang aktif.</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-warning me-2"></i>NIM sesuai dengan akun yang digunakan masuk.</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-warning me-2"></i>Nama, email, dan program studi terisi lengkap.</li>
                    <li><i class="bi bi-check-circle-fill text-warning me-2"></i>Satu mahasiswa hanya dapat mendaftar sekali.</li>
                </ul>
            </div>

            <div class="pk-card bg-white p-4 mb-4">
                <h3 class="h6 fw-bold text-pk">Tahapan Pendaftaran</h3>
                <div class="d-flex align-items-center gap-3 mb-2 small"><span class="pk-step">1</span>Periksa dan isi formulir.</div>
                <div class="d-flex align-items-center gap-3 mb-2 small"><span class="pk-step">2</span>Kirim pendaftaran.</div>
                <div class="d-flex align-items-center gap-3 small"><span class="pk-step">3</span>Terima konfirmasi pendaftaran.</div>
            </div>

            <a href="{{ route('katalog.show', $programKerja) }}" class="btn btn-outline-secondary rounded-3">
                <i class="bi bi-arrow-left me-2"></i> Kembali ke Detail Program
            </a>
        </div>

        <div class="col-lg-8">
            <div class="pk-card bg-white p-4 p-md-5">
                <h1 class="h3 fw-bold text-pk">Formulir Pendaftaran</h1>
                <p class="text-secondary">Isi data dengan benar untuk mempercepat proses verifikasi.</p>

                @if ($errors->any())
                    <div id="tampilkanPesanValidasi" class="alert alert-danger small">
                        <div class="fw-semibold mb-1"><i class="bi bi-exclamation-circle me-1"></i> Data tidak lengkap atau tidak valid</div>
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $pesan)
                                <li>{{ $pesan }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="kirimDataPendaftaran" method="POST" action="{{ route('pendaftaran.simpan', $programKerja) }}" class="row g-3" novalidate>
                    @csrf

                    <div class="col-12"><h2 class="h5 fw-bold text-pk mb-0">Data Pribadi</h2>
                        <small class="text-secondary">Gunakan nama lengkap sesuai identitas resmi.</small></div>

                    @foreach ([
                        ['nama', 'Nama Lengkap', 'text', 'Contoh: Budi Santoso', $mahasiswa->pengguna->name, 'col-md-6'],
                        ['nim', 'NIM', 'text', 'Contoh: 235150400111001', $mahasiswa->nim, 'col-md-6'],
                        ['email', 'Email', 'email', 'Contoh: nama@email.com', $mahasiswa->pengguna->email, 'col-md-12'],
                    ] as [$nm, $label, $tipe, $placeholder, $bawaan, $kolom])
                        <div class="{{ $kolom }}">
                            <label for="{{ $nm }}" class="form-label fw-semibold small">{{ $label }}</label>
                            <input type="{{ $tipe }}" id="{{ $nm }}" name="{{ $nm }}" value="{{ old($nm, $bawaan) }}"
                                   placeholder="{{ $placeholder }}" class="form-control @error($nm) is-invalid @enderror">
                            @error($nm)<div class="small text-danger mt-1">*{{ $message }}</div>@enderror
                        </div>
                    @endforeach

                    <div class="col-12 mt-4"><h2 class="h5 fw-bold text-pk mb-0">Data Akademik</h2>
                        <small class="text-secondary">Informasi ini digunakan untuk verifikasi administratif.</small></div>

                    <div class="col-md-12">
                        <label for="program_studi" class="form-label fw-semibold small">Program Studi</label>
                        <select id="program_studi" name="program_studi" class="form-select @error('program_studi') is-invalid @enderror">
                            <option value="" disabled>Pilih program studi</option>
                            @foreach ($prodi as $p)
                                <option value="{{ $p }}" @selected(old('program_studi', $mahasiswa->program_studi) === $p)>{{ $p }}</option>
                            @endforeach
                        </select>
                        @error('program_studi')<div class="small text-danger mt-1">*{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <div class="rounded-3 px-3 py-2 small" style="background: #fdf0c0">
                            <i class="bi bi-info-circle me-1"></i> Pastikan semua data sudah benar sebelum mengirim pendaftaran.
                        </div>
                    </div>

                    <div class="col-12 d-flex justify-content-end gap-2">
                        <a href="{{ route('katalog.show', $programKerja) }}" class="btn btn-light border">Batal</a>
                        <button class="btn btn-pk px-4">Kirim Pendaftaran <i class="bi bi-arrow-right ms-1"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
