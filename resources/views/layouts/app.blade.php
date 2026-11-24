<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - SGE FILKOM</title>

    <!-- Bootstrap 5 & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --pk-teal: #216E75;
            --pk-teal-dark: #19565c;
            --pk-yellow: #F5B400;
            --pk-yellow-hover: #dfa400;
            --pk-badge-bg: #F3EFE0;
        }

        body {
            background-color: #F8FAFC;
        }

        .text-pk { color: var(--pk-teal) !important; }
        .bg-pk { background-color: var(--pk-teal) !important; }
        .text-pk-yellow { color: var(--pk-yellow) !important; }

        .btn-pk {
            background-color: var(--pk-teal);
            color: #fff;
            border: none;
            border-radius: 0.6rem;
        }
        .btn-pk:hover { background-color: var(--pk-teal-dark); color: #fff; }

        .btn-pk-yellow {
            background-color: var(--pk-yellow);
            color: #1f2937;
            border: none;
            border-radius: 0.6rem;
        }
        .btn-pk-yellow:hover { background-color: var(--pk-yellow-hover); color: #111827; }

        .pk-badge {
            background-color: var(--pk-badge-bg);
            color: var(--pk-teal);
            font-size: 0.8rem;
            font-weight: 600;
            padding: 0.4rem 0.85rem;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .pk-card {
            border-radius: 1.25rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(0,0,0,0.03);
        }

        /* Styling Kartu Pilihan Role (pilih-role.blade.php) */
        .role-card {
            background-color: #ffffff;
            border-radius: 1.25rem;
            padding: 2.5rem 1.75rem;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.04);
            border: 2px solid transparent;
            cursor: pointer;
            transition: all 0.25s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .role-card .role-icon {
            font-size: 4rem;
            color: var(--pk-teal);
            margin-bottom: 1rem;
            transition: color 0.25s ease;
        }
        .role-card h2 { color: var(--pk-yellow); }
        .role-card ul li { color: #4b5563; display: flex; align-items: flex-start; gap: 0.6rem; }
        .role-card ul li i { color: var(--pk-yellow); font-size: 1.1rem; line-height: 1.3; }

        /* Saat Radio Button Dipilih (Active State) */
        .btn-check:checked + .role-card {
            background-color: var(--pk-teal);
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(33, 110, 117, 0.2);
        }
        .btn-check:checked + .role-card .role-icon,
        .btn-check:checked + .role-card ul li {
            color: #ffffff;
        }

        /* Sistem visual bersama */
        :root {
            --pk-teal: #21737f;
            --pk-teal-dark: #195c66;
            --pk-yellow: #f2b705;
            --pk-bg: #f5f9f9;
            --pk-ink: #253238;
            --pk-muted: #708087;
            --pk-border: #dce8e8;
            --pk-radius: 1rem;
            --pk-shadow: 0 8px 24px rgba(29, 74, 83, .07);
            --bs-body-font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            --bs-body-color: var(--pk-ink);
            --bs-body-bg: var(--pk-bg);
            --bs-primary: var(--pk-teal);
            --bs-primary-rgb: 33, 115, 127;
            --bs-link-color: var(--pk-teal);
            --bs-link-hover-color: var(--pk-teal-dark);
        }
        body { color: var(--pk-ink); background: var(--pk-bg); line-height: 1.55; }
        h1, h2, h3, h4, h5, h6 { letter-spacing: -.025em; }
        .navbar { box-shadow: 0 2px 14px rgba(17, 56, 62, .12); }
        .navbar-brand { letter-spacing: -.02em; }
        .navbar .btn { min-height: 38px; border-radius: .7rem; font-weight: 600; }
        .btn, .form-control, .form-select, .nav-link, .pk-card, .card {
            transition: color .18s ease, background-color .18s ease, border-color .18s ease, box-shadow .18s ease, transform .18s ease;
        }
        .btn { border-radius: .7rem; font-weight: 600; }
        .btn:focus-visible, a:focus-visible, button:focus-visible, input:focus-visible, select:focus-visible, textarea:focus-visible {
            outline: 3px solid rgba(242, 183, 5, .65);
            outline-offset: 2px;
            box-shadow: none;
        }
        .btn-pk { background: var(--pk-teal); color: #fff; border: 1px solid var(--pk-teal); }
        .btn-pk:hover, .btn-pk:focus { background: var(--pk-teal-dark); border-color: var(--pk-teal-dark); color: #fff; transform: translateY(-1px); }
        .btn-pk-yellow { background: var(--pk-yellow); color: #263238; border: 1px solid var(--pk-yellow); }
        .btn-pk-yellow:hover { background: #dda600; border-color: #dda600; color: #263238; transform: translateY(-1px); }
        .pk-card, .card { border: 1px solid var(--pk-border); border-radius: var(--pk-radius); box-shadow: var(--pk-shadow); }
        .form-label { color: #394951; font-size: .9rem; font-weight: 650; margin-bottom: .45rem; }
        .form-control, .form-select { min-height: 46px; border: 1px solid #d4e2e3; border-radius: .75rem; background-color: #fff; color: var(--pk-ink); }
        textarea.form-control { min-height: 110px; }
        .form-control::placeholder { color: #9aa8ad; }
        .form-control:focus, .form-select:focus { border-color: var(--pk-teal); box-shadow: 0 0 0 .22rem rgba(33, 115, 127, .12); }
        .table { --bs-table-bg: transparent; --bs-table-hover-bg: #f1f7f7; }
        .table thead th { color: #66777d; font-size: .78rem; font-weight: 700; letter-spacing: .045em; text-transform: uppercase; border-bottom-color: var(--pk-border); white-space: nowrap; }
        .table td { border-color: #e8eeee; }
        .alert { border: 0; border-radius: .85rem; }
        .badge { font-weight: 650; }
        .pagination { gap: .35rem; }
        .pagination .page-link { border-radius: .55rem; border-color: var(--pk-border); }
        footer { color: var(--pk-muted); }
        footer a:hover { color: var(--pk-teal) !important; }
        @media (max-width: 575.98px) {
            .navbar > .container { gap: .75rem; }
            .navbar .container > .d-flex { justify-content: flex-start !important; }
            main > .container.py-5 { padding-top: 1.5rem !important; padding-bottom: 1.5rem !important; }
            .pk-card { border-radius: .9rem; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { scroll-behavior: auto !important; transition-duration: .01ms !important; animation-duration: .01ms !important; animation-iteration-count: 1 !important; }
        }

    </style>
    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">

    <!-- Header / Navbar -->
    <nav class="navbar py-3" style="background-color: #216E75;">
        @hasSection('navbar-custom')
            @yield('navbar-custom')
        @else
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 text-white text-decoration-none" href="{{ route('katalog.index') }}">
                <div class="bg-white rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                    <i class="bi bi-briefcase-fill text-pk-yellow fs-5"></i>
                </div>
                <div class="lh-1">
                    <div class="fw-bold fs-5">SGE FILKOM</div>
                </div>
            </a>
            <div class="d-flex align-items-center flex-wrap justify-content-end gap-2">
                @auth
                    @if (auth()->user()->role->value === 'PIC')
                        <a href="{{ route('pic.proker.index') }}" class="btn btn-outline-light btn-sm">Program Kerja</a>
                        <a href="{{ route('pic.pengajuan.index') }}" class="btn btn-outline-light btn-sm">Pengajuan</a>
                        <a href="{{ route('pic.rekap.index') }}" class="btn btn-outline-light btn-sm">Rekap</a>
                    @else
                        <a href="{{ route('katalog.index') }}" class="btn btn-outline-light btn-sm">Katalog</a>
                        <a href="{{ route('pendaftaran.index') }}" class="btn btn-outline-light btn-sm">Pendaftaran</a>
                    @endif
                    <a href="{{ route('notifikasi.index') }}" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-bell"></i>
                        @php($jumlahBelumDibaca = auth()->user()->notifikasi()->where('sudah_dibaca', false)->count())
                        @if ($jumlahBelumDibaca > 0)<span class="badge rounded-pill text-bg-warning">{{ $jumlahBelumDibaca }}</span>@endif
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button class="btn btn-outline-light btn-sm">Keluar</button>
                    </form>
                @else
                    @if (Route::is('katalog.*'))
                        <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm">Masuk</a>
                        <a href="{{ route('register') }}" class="btn btn-pk-yellow btn-sm"
                        style="--bs-btn-color: var(--pk-teal); --bs-btn-hover-color: var(--pk-teal); --bs-btn-active-color: var(--pk-teal);">Daftar</a>
                    @endif
                @endauth
                @yield('tombol')
            </div>
        </div>
        @endif
    </nav>

    <!-- Konten Utama -->
    <main class="flex-grow-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-top py-3 mt-auto">
        <div class="container d-flex flex-column flex-sm-row justify-content-between align-items-center small text-secondary">
            <div>© 2026 SGE FILKOM. Semua hak dilindungi.</div>
            <div class="d-flex gap-4 mt-2 mt-sm-0">
                <a href="#" class="text-secondary text-decoration-none">Kebijakan privasi</a>
                <a href="#" class="fw-semibold text-pk text-decoration-none">Pusat bantuan</a>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
