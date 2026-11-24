{{-- Pengaturan pengingat pendaftaran (UC-07). --}}
@extends('layouts.app')
@section('title', 'Pengaturan Pengingat')

@section('navbar-custom')
<div class="container d-flex justify-content-between align-items-center">
    <a href="{{ route('katalog.index') }}" class="navbar-brand text-white fw-bold fs-4 mb-0">SGE FILKOM</a>
    <div class="d-flex gap-2">
        <a href="{{ route('notifikasi.index') }}" class="pk-nav-icon" aria-label="Notifikasi"><i class="bi bi-bell"></i></a>
        <a href="{{ route('dashboard') }}" class="pk-nav-icon" aria-label="Akun"><i class="bi bi-person"></i></a>
    </div>
</div>
@endsection

@push('styles')
<style>
    .reminder-page { background: #f4f7f9; min-height: 100%; color: #263b46; }
    .pk-nav-icon { width: 40px; height: 40px; display: grid; place-items: center; color: #207883; background: #fff; border-radius: 50%; text-decoration: none; font-size: 1.1rem; }
    .reminder-card, .reminder-summary { border: 1px solid #dce6ea; border-radius: 18px; background: #fff; overflow: hidden; }
    .reminder-choice { display: flex; gap: 14px; align-items: center; padding: 16px; border: 1px solid #d9e3e7; border-radius: 12px; cursor: pointer; transition: .15s ease; }
    .reminder-choice:has(input:checked) { border-color: #e8a900; background: #fff7e3; box-shadow: 0 0 0 1px #e8a900 inset; }
    .reminder-choice input { width: 20px; height: 20px; accent-color: #247984; flex: 0 0 auto; }
    .reminder-choice small, .muted-copy { color: #71838c; }
    .reminder-header, .reminder-footer { padding: 22px 26px; }
    .reminder-body { padding: 24px 26px; border-top: 1px solid #e1e8eb; border-bottom: 1px solid #e1e8eb; }
    .summary-top { background: #247984; color: white; padding: 24px; }
    @media (max-width: 575.98px) { .reminder-header, .reminder-body, .reminder-footer { padding: 18px; } }
</style>
@endpush

@section('content')
@php
    $opsiPengingat = [
        ['label' => '7 hari sebelumnya', 'jarak' => '7 days', 'keterangan' => 'Mulai siapkan dokumen lebih awal.'],
        ['label' => '3 hari sebelumnya', 'jarak' => '3 days', 'keterangan' => 'Pastikan semua persyaratan sudah lengkap.'],
        ['label' => '1 hari sebelumnya', 'jarak' => '1 day', 'keterangan' => 'Periksa kembali pendaftaran Anda.'],
        ['label' => '3 jam sebelumnya', 'jarak' => '3 hours', 'keterangan' => 'Selesaikan langkah terakhir pendaftaran.'],
        ['label' => '1 jam sebelumnya', 'jarak' => '1 hour', 'keterangan' => 'Pengingat terakhir sebelum pendaftaran ditutup.'],
    ];
    $waktuTersimpan = old('waktu_pengingat', $waktuTerpilih);
@endphp

<div class="reminder-page py-4 py-lg-5">
    <div class="container-xxl px-lg-4">
        <div class="small muted-copy mb-4"><a href="{{ route('pendaftaran.index') }}" class="text-decoration-none text-secondary">Pendaftaran saya</a><span class="mx-2">/</span><span class="text-pk">Pengaturan pengingat</span></div>
        <a href="{{ route('katalog.show', $programKerja) }}" class="btn btn-light border rounded-3 mb-4"><i class="bi bi-arrow-left me-2 text-pk"></i>Kembali</a>

        @if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        @if ($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif

        <div class="row g-4 align-items-start">
            <div class="col-lg-9">
                <form id="pilihWaktuPengingat" method="POST" action="{{ route('pengingat.simpan', $programKerja) }}" class="reminder-card">
                    @csrf
                    <div class="reminder-header d-flex align-items-center gap-3">
                        <span class="rounded-3 d-grid text-pk" style="width:44px;height:44px;background:#edf7f7;place-items:center"><i class="bi bi-bell fs-4"></i></span>
                        <div><h1 class="h4 mb-1">Pengaturan pengingat</h1><div class="small muted-copy">Pengingat kecil, langkah besar untuk rencana Anda.</div></div>
                    </div>
                    <div class="reminder-body">
                        <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                            <div><h2 class="h6 mb-1">Kapan Anda ingin diingatkan?</h2><p class="small muted-copy mb-0">Pilih satu atau beberapa waktu sebelum pendaftaran ditutup.</p></div>
                            <span class="small text-pk fw-semibold text-nowrap"><span id="jumlahTerpilih">{{ count($waktuTersimpan) }}</span> waktu dipilih</span>
                        </div>
                        <div class="d-grid gap-2">
                            @foreach ($opsiPengingat as $opsi)
                                @php($waktuPilihan = $tanggalDeadline->copy()->sub($opsi['jarak']))
                                @php($nilaiPilihan = $waktuPilihan->format('Y-m-d\TH:i'))
                                @if ($waktuPilihan->isFuture())
                                    <label class="reminder-choice">
                                        <input type="checkbox" name="waktu_pengingat[]" value="{{ $nilaiPilihan }}" data-label="{{ $opsi['label'] }}" data-time="{{ $waktuPilihan->translatedFormat('d M Y · H:i') }}" @checked(in_array($nilaiPilihan, $waktuTersimpan, true))>
                                        <span><strong class="d-block">{{ $opsi['label'] }}</strong><small>{{ $opsi['keterangan'] }}</small></span>
                                    </label>
                                @endif
                            @endforeach
                        </div>
                        @error('waktu_pengingat')<div class="small text-danger mt-2">{{ $message }}</div>@enderror

                        <div class="mt-4">
                            <div class="small fw-semibold mb-2">Pengingat dikirim melalui</div>
                            <div class="rounded-3 p-3 d-flex align-items-center gap-3" style="background:#f6f9fa">
                                <i class="bi bi-bell text-pk fs-5"></i>
                                <div><strong class="d-block small">Notifikasi SGE FILKOM</strong><span class="small muted-copy">{{ auth()->user()->email }}</span></div>
                                <span class="small text-pk ms-auto">Aktif</span>
                            </div>
                        </div>
                    </div>
                    <div class="reminder-footer d-flex justify-content-between align-items-center gap-3 flex-wrap">
                        <span class="small muted-copy">Anda dapat mengubahnya kapan saja.</span>
                        <div class="d-flex gap-2"><a href="{{ route('katalog.show', $programKerja) }}" class="btn btn-light border px-4">Batal</a><button class="btn btn-pk px-4"><i class="bi bi-check2 me-2"></i>Simpan Pengingat</button></div>
                    </div>
                </form>
            </div>

            <aside class="col-lg-3">
                <div class="reminder-summary">
                    <div class="summary-top">
                        <div class="small text-uppercase mb-3"><i class="bi bi-mortarboard me-2"></i>Program pilihan Anda</div>
                        <h2 class="h5 fw-semibold">{{ $programKerja->nama_proker }}</h2>
                        <span class="badge bg-white text-pk">{{ $programKerja->labelStatusPendaftaran() }}</span>
                    </div>
                    <div class="p-4">
                        <div class="small muted-copy mb-2"><i class="bi bi-calendar3 me-2"></i>Pendaftaran ditutup</div>
                        <div class="h6 mb-1">{{ $tanggalDeadline->translatedFormat('d F Y') }}</div>
                        <div class="small muted-copy">Pukul {{ $tanggalDeadline->format('H.i') }} WIB</div>
                        <hr>
                        <div class="d-flex justify-content-between small mb-3"><span>Jadwal pengingat Anda</span><strong class="text-pk"><span id="jumlahRingkasan">{{ count($waktuTersimpan) }}</span> pengingat</strong></div>
                        <div id="ringkasanPengingat" class="d-grid gap-3">
                            @foreach ($opsiPengingat as $opsi)
                                @php($waktuPilihan = $tanggalDeadline->copy()->sub($opsi['jarak']))
                                @php($nilaiPilihan = $waktuPilihan->format('Y-m-d\TH:i'))
                                @if ($waktuPilihan->isFuture() && in_array($nilaiPilihan, $waktuTersimpan, true))
                                    <div class="d-flex gap-2 align-items-start"><i class="bi bi-check-circle-fill text-pk"></i><div class="small"><strong class="d-block">{{ $opsi['label'] }}</strong><span class="muted-copy">{{ $waktuPilihan->translatedFormat('d M Y · H:i') }} WIB</span></div></div>
                                @endif
                            @endforeach
                            @if (count($waktuTersimpan) === 0)<div class="small muted-copy">Belum ada jadwal dipilih.</div>@endif
                        </div>
                        <div class="small muted-copy mt-4"><i class="bi bi-globe me-2"></i>Waktu Indonesia Barat (WIB)</div>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const pilihanPengingat = document.querySelectorAll('input[name="waktu_pengingat[]"]');
    const jumlahTerpilih = document.getElementById('jumlahTerpilih');
    const jumlahRingkasan = document.getElementById('jumlahRingkasan');
    const ringkasanPengingat = document.getElementById('ringkasanPengingat');

    function perbaruiRingkasanPengingat() {
        const terpilih = [...pilihanPengingat].filter((item) => item.checked);
        jumlahTerpilih.textContent = terpilih.length;
        jumlahRingkasan.textContent = terpilih.length;
        ringkasanPengingat.replaceChildren();

        if (terpilih.length === 0) {
            const kosong = document.createElement('div');
            kosong.className = 'small muted-copy';
            kosong.textContent = 'Belum ada jadwal dipilih.';
            ringkasanPengingat.append(kosong);
            return;
        }

        terpilih.forEach((item) => {
            const baris = document.createElement('div');
            baris.className = 'd-flex gap-2 align-items-start';
            baris.innerHTML = '<i class="bi bi-check-circle-fill text-pk"></i><div class="small"><strong class="d-block"></strong><span class="muted-copy"></span></div>';
            baris.querySelector('strong').textContent = item.dataset.label;
            baris.querySelector('span').textContent = `${item.dataset.time} WIB`;
            ringkasanPengingat.append(baris);
        });
    }

    pilihanPengingat.forEach((item) => item.addEventListener('change', perbaruiRingkasanPengingat));
</script>
@endpush
