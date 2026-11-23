@use('App\Enums\Role')
@extends('layouts.app')
@section('title', 'Registrasi')

@section('tombol')
    <a href="{{ route('login') }}" class="btn btn-outline-light rounded-pill btn-sm px-3">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Login
    </a>
@endsection

@section('content')
@php
    $label = match ($role) { Role::Mahasiswa => 'Mahasiswa', Role::Pic => 'PIC Event', Role::Admin => 'Super Admin' };
@endphp
<div class="container py-5">
    <div class="row align-items-center g-5">
        <div class="col-lg-5 d-none d-lg-block">
            <h1 class="display-6 fw-bold text-pk">Satu akun,<br>banyak kesempatan.</h1>
            <p class="text-secondary">Bergabung dengan ProgramKita untuk menemukan, mengikuti, dan mengelola program kerja BEM dalam satu tempat.</p>
            <img src="{{ asset('img/hero.jpg') }}" alt="" class="img-fluid rounded-4 shadow-sm">
        </div>

        <div class="col-lg-7">
            <div class="pk-card bg-white p-4 p-md-5">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="badge rounded-pill text-bg-light text-pk px-3 py-2">Mendaftar sebagai {{ $label }}</span>
                    <a href="{{ route('register') }}" class="small fw-semibold text-pk">Ubah role</a>
                </div>
                <h2 class="h3 fw-bold text-pk">Lengkapi data akunmu</h2>
                <p class="small text-secondary">Isi data berikut untuk membuat akun ProgramKita.</p>

                @if ($errors->any())
                    <div class="alert alert-danger py-2 small d-flex align-items-center">
                        <i class="bi bi-exclamation-circle me-2"></i> Data tidak lengkap atau tidak valid
                    </div>
                @endif

                <form method="POST" action="{{ route('register.store', $role->value) }}" class="row g-3">
                    @csrf
                    <div class="col-md-6"><x-input name="name" label="Nama lengkap" required /></div>

                    @if ($role === Role::Mahasiswa)
                        <div class="col-md-6"><x-input name="nim" label="NIM" required /></div>
                        <div class="col-md-6">
                            <label for="program_studi" class="form-label fw-semibold small">Program studi</label>
                            <select id="program_studi" name="program_studi" required
                                    class="form-select @error('program_studi') is-invalid @enderror">
                                <option value="" disabled @selected(! old('program_studi'))>Pilih program studi</option>
                                @foreach (['Teknik Informatika', 'Teknik Komputer', 'Sistem Informasi', 'Teknologi Informasi', 'Pendidikan Teknologi Informasi'] as $prodi)
                                    <option value="{{ $prodi }}" @selected(old('program_studi') === $prodi)>{{ $prodi }}</option>
                                @endforeach
                            </select>
                            @error('program_studi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    @endif

                    <div class="col-md-6"><x-input name="email" label="Email" type="email" required /></div>
                    <div class="col-md-6"><x-input name="username" label="Username" autocomplete="username" required /></div>
                    <div class="col-md-6"><x-input name="password" label="Kata sandi" type="password" autocomplete="new-password" required /></div>
                    <div class="col-md-6"><x-input name="password_confirmation" label="Konfirmasi kata sandi" type="password" autocomplete="new-password" required /></div>

                    <div class="col-12">
                        <div class="alert alert-info py-2 small mb-0"><i class="bi bi-lock me-1"></i> Gunakan minimal 8 karakter, kombinasi huruf dan angka.</div>
                    </div>
                    <div class="col-12 d-grid">
                        <button class="btn btn-pk btn-lg">Registrasi <i class="bi bi-arrow-right ms-1"></i></button>
                    </div>
                </form>

                <p class="text-center small text-secondary mt-3 mb-0">Sudah punya akun ProgramKita?
                    <a href="{{ route('login') }}" class="fw-semibold text-pk">Masuk</a></p>
            </div>
        </div>
    </div>
</div>
@endsection