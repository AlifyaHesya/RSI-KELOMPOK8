@extends('layouts.app')
@section('title', 'Masuk')

@section('tombol')
    <a href="{{ route('katalog.index') }}" class="btn btn-outline-light rounded-pill btn-sm px-3">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
    </a>
@endsection

@section('content')
<div class="container py-5">
    <div class="row align-items-center g-5">
        <div class="col-lg-6 d-none d-lg-block">
            <h1 class="display-6 fw-bold text-pk">Kesempatan baru,<br>langkah berikutnya.</h1>
            <p class="text-secondary">Temukan program yang sesuai dengan minatmu, daftar dengan mudah, dan pantau prosesnya dalam satu tempat.</p>
            <img src="{{ asset('img/hero.jpg') }}" alt="" class="img-fluid rounded-4 shadow-sm">
            <p class="small text-secondary mt-3">Ingin lihat-lihat dulu?
                <a href="{{ route('katalog.index') }}" class="fw-semibold text-pk text-decoration-none">Jelajahi katalog <i class="bi bi-arrow-right"></i></a>
            </p>
        </div>

        <div class="col-lg-5 offset-lg-1">
            <div class="pk-card bg-white p-4 p-md-5">
                <h2 class="h3 fw-bold text-pk">Selamat datang kembali</h2>
                <p class="small text-secondary">Masuk ke akun ProgramKita untuk melanjutkan pendaftaran dan melihat status programmu.</p>

                @if (session('status'))
                    <div class="alert alert-success py-2 small">{{ session('status') }}</div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="d-grid gap-3">
                    @csrf
                    <div>
                        <x-input name="login" label="Username / Email" placeholder="username atau nama@gmail.com"
                                 autocomplete="username" required />
                    </div>
                    <div>
                        <x-input name="password" label="Kata sandi" type="password" placeholder="Masukkan kata sandi"
                                 autocomplete="current-password" required />
                    </div>
                    <div class="d-flex justify-content-between small">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label" for="remember">Ingat saya</label>
                        </div>
                        <a href="#" class="fw-semibold text-pk text-decoration-none">Lupa kata sandi?</a>
                    </div>
                    <button class="btn btn-pk btn-lg">Masuk <i class="bi bi-arrow-right ms-1"></i></button>
                </form>

                <hr class="my-4">
                <p class="text-center small text-secondary mb-2">Belum punya akun ProgramKita?</p>
                <a href="{{ route('register') }}" class="btn btn-pk-yellow w-100 fw-semibold">Daftar akun baru</a>
            </div>
        </div>
    </div>
</div>
@endsection