@extends('layouts.app')
@section('title', 'Daftar')

@section('tombol')
    <a href="{{ route('login') }}" class="btn btn-outline-light rounded-pill btn-sm px-3">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Login
    </a>
@endsection

@section('content')
<div class="container py-5 text-center">
    <h1 class="display-5 fw-bold text-pk-yellow mb-2">Daftar sebagai apa?</h1>
    <p class="text-secondary mb-5">Pilih peran yang sesuai, lalu lengkapi data akunmu di langkah selanjutnya</p>

    <form method="POST" action="{{ route('register.kode') }}">
        @csrf
        <div class="row g-4 justify-content-center">
            @foreach ([
                ['MHS', 'Mahasiswa', 'bi-mortarboard-fill', ['Mahasiswa FILKOM UB', 'Cari, daftar, ikuti program kerja', 'Memiliki antusias tinggi']],
                ['PIC', 'PIC Event', 'bi-people', ['Mengelola Program Kerja', 'Mengajukan Program Kerja']],
            ] as [$nilai, $judul, $ikon, $poin])
                <div class="col-lg-3 col-md-4">
                    <input type="radio" class="btn-check" name="role" id="role-{{ $nilai }}" value="{{ $nilai }}"
                           @checked(old('role', 'PIC') === $nilai)>
                    <label for="role-{{ $nilai }}" class="role-card h-100 w-100">
                        <i class="bi {{ $ikon }} role-icon"></i>
                        <h2 class="h4 fw-bold mb-4">{{ $judul }}</h2>
                        <ul class="list-unstyled text-start small mb-0 w-100">
                            @foreach ($poin as $p)
                                <li class="mb-2"><i class="bi bi-check-circle-fill"></i><span>{{ $p }}</span></li>
                            @endforeach
                        </ul>
                    </label>
                </div>
            @endforeach
        </div>

        <div class="mx-auto mt-5" style="max-width: 520px">
            <input type="text" name="kode_akses" id="kode_akses" value="{{ old('kode_akses') }}"
                   class="form-control form-control-lg fs-6 py-3 rounded-3 @error('kode_akses') is-invalid @enderror">
            @error('kode_akses') <div class="invalid-feedback">{{ $message }}</div> @enderror

            <button class="btn btn-pk-yellow btn-lg fs-6 mt-3 px-5 py-2 fw-semibold text-white">
                Lanjutkan <i class="bi bi-arrow-right ms-1"></i>
            </button>
            <p class="small text-secondary mt-3">Sudah punya akun ProgramKita?
                <a href="{{ route('login') }}" class="fw-semibold text-pk text-decoration-none">Masuk</a>
            </p>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    const kode = document.getElementById('kode_akses');
    function aturKode() {
        const mhs = document.querySelector('input[name=role]:checked').value === 'MHS';
        kode.disabled = mhs;
        kode.placeholder = mhs ? 'Kode akses tidak diperlukan untuk Mahasiswa' : 'Masukkan Kode Akses';
    }
    document.querySelectorAll('input[name=role]').forEach(r => r.addEventListener('change', aturKode));
    aturKode();
</script>
@endpush
