@extends('layouts.app')
@section('title', 'Registrasi Akun')

@section('tombol')
    <a href="{{ route('login') }}" class="btn btn-outline-light rounded-pill btn-sm px-3">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Login
    </a>
@endsection

@section('content')
<div class="container py-5">
    <div class="row align-items-center g-5">
        <!-- Kolom Kiri -->
        <div class="col-lg-5 d-none d-lg-block">
            <div class="pk-badge mb-3">
                <i class="bi bi-briefcase"></i> Bertumbuh bersama ProgramKita
            </div>
            <h1 class="display-5 fw-bold text-pk mb-3">Satu akun,<br>banyak kesempatan.</h1>
            <p class="text-secondary mb-4">Bergabung dengan ProgramKita untuk menemukan, mengikuti, dan mengelola program kerja BEM dalam satu tempat.</p>
            
            <div class="position-relative rounded-4 overflow-hidden shadow-sm">
                <div role="img" aria-label="ProgramKita" class="w-100" style="height: 300px; background: linear-gradient(135deg, #19565c, #48a0a5);"></div>
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

        <!-- Kolom Kanan (Form Registrasi) -->
        <div class="col-lg-7">
            <div class="pk-card bg-white p-4 p-md-5">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="pk-badge">
                        <i class="bi bi-mortarboard-fill"></i> Mendaftar sebagai {{ $role->value === 'PIC' ? 'PIC Event' : 'Mahasiswa' }}
                    </div>
                    <a href="{{ route('register') }}" class="small fw-semibold text-pk text-decoration-none">Ubah role</a>
                </div>

                <h2 class="h3 fw-bold text-pk mb-1">Lengkapi data akunmu</h2>
                <p class="small text-secondary mb-4">Isi data berikut untuk membuat akun ProgramKita.</p>

                @if ($errors->any())
                    <div class="alert alert-danger d-flex align-items-center py-2 px-3 small rounded-3 mb-4" role="alert">
                        <i class="bi bi-exclamation-circle me-2 fs-6"></i>
                        <div>Data tidak lengkap atau tidak valid</div>
                    </div>
                @endif

                <form method="POST" action="{{ route('register.store', $role->value) }}" onsubmit="return confirm('Konfirmasi registrasi akun ini?')">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Nama lengkap</label>
                            <input type="text" name="name" value="{{ old('name') }}" 
                                   class="form-control @error('name') is-invalid @enderror" placeholder="Budi Santoso" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        @if ($role->value === 'MHS')
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">NIM</label>
                            <input type="text" name="nim" value="{{ old('nim') }}" 
                                   class="form-control @error('nim') is-invalid @enderror" placeholder="235150400111001" required>
                            @error('nim') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Program studi</label>
                            <select name="program_studi" class="form-select @error('program_studi') is-invalid @enderror" required>
                                <option value="" disabled selected>Pilih Program Studi</option>
                                <option value="Teknik Informatika" @selected(old('program_studi') == 'Teknik Informatika')>Teknik Informatika</option>
                                <option value="Sistem Informasi" @selected(old('program_studi') == 'Sistem Informasi')>Sistem Informasi</option>
                                <option value="Teknologi Informasi" @selected(old('program_studi') == 'Teknologi Informasi')>Teknologi Informasi</option>
                                <option value="Teknik Komputer" @selected(old('program_studi') == 'Teknik Komputer')>Teknik Komputer</option>
                                <option value="Pendidikan Teknologi Informasi" @selected(old('program_studi') == 'Pendidikan Teknologi Informasi')>Pendidikan Teknologi Informasi</option>
                            </select>
                            @error('program_studi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        @elseif ($role->value === 'PIC')
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Biro/departemen</label>
                            <input type="text" name="biro" value="{{ old('biro') }}"
                                   class="form-control @error('biro') is-invalid @enderror" maxlength="100" placeholder="Contoh: BEM, Advokesma" required>
                            @error('biro') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        @endif

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" 
                                   class="form-control @error('email') is-invalid @enderror" placeholder="budi@student.ub.ac.id" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Username</label>
                            <input type="text" name="username" value="{{ old('username') }}" 
                                class="form-control @error('username') is-invalid @enderror" placeholder="budisantoso" required>
                            @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Kata sandi</label>
                            <div class="input-group @error('password') is-invalid @enderror">
                                <input type="password" name="password" id="regPassword" 
                                    class="form-control border-end-0 @error('password') is-invalid @enderror" placeholder="••••••••••••" required>
                                <button class="btn btn-outline-secondary border-start-0 bg-white text-pk" type="button" onclick="ubahVisibilitasKataSandi('regPassword', this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Konfirmasi kata sandi</label>
                            <div class="input-group @error('password_confirmation') is-invalid @enderror">
                                <input type="password" name="password_confirmation" id="regPasswordConfirm" 
                                    class="form-control border-end-0 @error('password_confirmation') is-invalid @enderror" placeholder="••••••••" required>
                                <button class="btn btn-outline-secondary border-start-0 bg-white text-pk" type="button" onclick="ubahVisibilitasKataSandi('regPasswordConfirm', this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            @error('password_confirmation') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="rounded-3 p-2 px-3 mt-4 mb-4 small d-flex align-items-center" 
                         style="background-color: #EBF3F4; color: #216E75;">
                        <i class="bi bi-lock me-2"></i>
                        <span>Gunakan minimal 8 karakter, kombinasi huruf dan angka.</span>
                    </div>

                    <button type="submit" class="btn btn-pk w-100 py-2 fw-semibold">
                        Registrasi <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                </form>

                <p class="text-center small text-secondary mt-4 mb-0">
                    Sudah punya akun ProgramKita? 
                    <a href="{{ route('login') }}" class="fw-semibold text-pk text-decoration-none">Masuk</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function ubahVisibilitasKataSandi(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    }
</script>

@if ($errors->any())
    <div class="alert alert-danger py-2 px-3 small rounded-3 mb-4" role="alert">
        <div class="fw-semibold mb-1">
            <i class="bi bi-exclamation-circle me-1"></i> Data tidak lengkap atau tidak valid:
        </div>
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@endpush
