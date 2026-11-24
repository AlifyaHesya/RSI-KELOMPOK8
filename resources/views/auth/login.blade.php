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
        <!-- Kolom Kiri -->
        <div class="col-lg-6 d-none d-lg-block">
            <div class="pk-badge mb-3">
                <i class="bi bi-briefcase"></i> Bertumbuh bersama SGE FILKOM UB
            </div>
            <h1 class="display-5 fw-bold text-pk mb-3">Kesempatan baru,<br>langkah berikutnya.</h1>
            <p class="text-secondary mb-4">Temukan program yang sesuai dengan minatmu, daftar dengan mudah, dan pantau prosesnya dalam satu tempat.</p>
        
           <div class="position-relative rounded-4 overflow-hidden shadow-sm">
                <img src="{{ asset('css/img/login.jpg') }}" alt="ProgramKita" class="w-100"
                    style="height: 400px; object-fit: cover; background: #19565c;">
                <div class="position-absolute bottom-0 start-0 w-100 p-4 text-white"
                    style="background: linear-gradient(to top, rgba(0,0,0,0.75), transparent);">
                    <h5 class="fw-bold mb-1">Mulai dari minat, tumbuh jadi peluang.</h5>
                    <p class="small mb-0 opacity-75">Magang • Pelatihan • Pengembangan karir</p>
                </div>
            </div>

            <p class="small text-secondary mt-3">Ingin lihat-lihat dulu?
                <a href="{{ route('katalog.index') }}" class="fw-semibold text-pk text-decoration-none">Jelajahi katalog <i class="bi bi-arrow-right"></i></a>
            </p>
        </div>

        <!-- Kolom Kanan (Form Card) -->
        <div class="col-lg-5 offset-lg-1">
            <div class="pk-card bg-white p-4 p-md-5">
                <div class="d-inline-flex align-items-center justify-content-center rounded-3 mb-3" 
                     style="width: 42px; height: 42px; background-color: #EBF3F4; color: #216E75;">
                    <i class="bi bi-lock fs-5"></i>
                </div>

                <h2 class="h3 fw-bold text-pk">Selamat datang kembali</h2>
                <p class="small text-secondary mb-4">Masuk ke akun ProgramKita untuk melanjutkan pendaftaran dan melihat status programmu.</p>

                @if (session('status'))
                    <div class="alert alert-success py-2 small">{{ session('status') }}</div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="d-grid gap-3">
                    @csrf
                    <div>
                        <label class="form-label small fw-semibold text-dark">Username / Email</label>
                        <input type="text" name="login" value="{{ old('login') }}" 
                               class="form-control form-control-lg fs-6 @error('login') is-invalid @enderror" 
                               placeholder="username atau nama@gmail.com" autocomplete="username" required>
                        @error('login') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label class="form-label small fw-semibold text-dark">Kata sandi</label>
                        <div class="input-group">
                            <input type="password" name="password" id="loginPassword"
                                   class="form-control form-control-lg fs-6 border-end-0 @error('password') is-invalid @enderror" 
                                   placeholder="Masukkan kata sandi" autocomplete="current-password" required>
                            <button class="btn btn-outline-secondary border-start-0 bg-white text-pk small fw-semibold" 
                                    type="button" onclick="ubahVisibilitasKataSandi('loginPassword', this)">
                                <i class="bi bi-eye me-1"></i> Tampilkan
                            </button>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center small my-1">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label text-secondary" for="remember">Ingat saya</label>
                        </div>
                        <a href="{{ route('password.request') }}" class="fw-semibold text-pk text-decoration-none">Lupa kata sandi?</a>
                    </div>

                    <button class="btn btn-pk btn-lg fs-6 fw-semibold py-2">Masuk <i class="bi bi-arrow-right ms-1"></i></button>
                </form>

                <hr class="my-4 text-secondary opacity-25">
                <p class="text-center small text-secondary mb-2">Belum punya akun ProgramKita?</p>
                <a href="{{ route('register') }}" class="btn btn-pk-yellow w-100 fw-semibold py-2">Daftar akun baru</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function ubahVisibilitasKataSandi(inputId, btn) {
        const input = document.getElementById(inputId);
        if (input.type === 'password') {
            input.type = 'text';
            btn.innerHTML = '<i class="bi bi-eye-slash me-1"></i> Sembunyikan';
        } else {
            input.type = 'password';
            btn.innerHTML = '<i class="bi bi-eye me-1"></i> Tampilkan';
        }
    }
</script>
@endpush
