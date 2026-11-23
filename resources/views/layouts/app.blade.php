<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') | ProgramKita</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/programkita.css') }}" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">

<header class="pk-header text-white">
    <div class="container d-flex align-items-center justify-content-between py-3">
        <a href="{{ route('katalog.index') }}" class="d-flex align-items-center gap-3 text-white text-decoration-none">
            <span class="pk-logo"><i class="bi bi-briefcase-fill"></i></span>
            <span>
                <strong class="d-block fs-5 lh-1">ProgramKita</strong>
                <small class="opacity-75">Katalog program dan pendaftaran</small>
            </span>
        </a>
        @yield('tombol')
    </div>
</header>

<main class="flex-grow-1">
    @yield('content')
</main>

<footer class="border-top bg-white small text-secondary">
    <div class="container d-flex justify-content-between py-3">
        <span>© {{ date('Y') }} ProgramKita. Semua hak dilindungi.</span>
        <span><a href="#" class="text-secondary me-3">Kebijakan privasi</a><a href="#" class="text-pk fw-semibold">Pusat bantuan</a></span>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // tombol mata pada kolom kata sandi
    document.querySelectorAll('[data-toggle-password]').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = document.querySelector(btn.dataset.togglePassword);
            const tampil = input.type === 'password';
            input.type = tampil ? 'text' : 'password';
            btn.querySelector('i').className = tampil ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    });
</script>
@stack('scripts')
</body>
</html>