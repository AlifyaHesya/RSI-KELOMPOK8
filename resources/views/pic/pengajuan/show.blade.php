@extends('layouts.app')
@section('title', 'Pengajuan · ' . $pengajuan->nama_proker)
@section('content')
<div class="container py-5" style="max-width: 980px">
    <a class="pj-back-link" href="{{ route('pic.pengajuan.index') }}"><i class="bi bi-arrow-left me-2"></i>Semua pengajuan</a>
    <header class="pj-detail-head mt-3 mb-4">
        <div><span class="pk-eyebrow">Detail pengajuan</span><h1 class="h2 text-pk fw-bold mb-2">{{ $pengajuan->nama_proker }}</h1><p class="text-secondary mb-0">Pastikan semua dokumen wajib tersedia sebelum mengajukan program.</p></div>
        <span class="pk-badge"><i class="bi bi-circle-fill small"></i>{{ $pengajuan->status_pengajuan->value }}</span>
    </header>
    @if ($errors->any())<div class="alert alert-danger"><i class="bi bi-exclamation-circle me-2"></i><ul class="mb-0 mt-1">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    @if (session('success'))<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>@endif
    @if (session('error'))<div class="alert alert-danger"><i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}@if (session('dokumen_kurang'))<ul class="mb-0 mt-1">@foreach(session('dokumen_kurang') as $kurang)<li>{{ $kurang }}</li>@endforeach</ul>@endif</div>@endif
    <section class="pk-card bg-white p-4 p-md-5 mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-3"><div><h2 class="h5 fw-bold mb-1">Kelengkapan dokumen</h2><p class="text-secondary small mb-0">Format dan ukuran mengikuti ketentuan setiap dokumen.</p></div><span class="small fw-semibold {{ empty($dokumenKurang) ? 'text-success' : 'text-warning-emphasis' }}">{{ empty($dokumenKurang) ? 'Dokumen wajib lengkap' : count($dokumenKurang).' dokumen belum tersedia' }}</span></div>
        <div class="row g-3">
        @foreach (\App\Models\Pengajuan::BERKAS_WAJIB as $field => $label)
            <div class="col-md-4"><div class="pj-document {{ $pengajuan->{$field} ? 'is-ready' : '' }}"><span class="pj-document-icon"><i class="bi {{ $pengajuan->{$field} ? 'bi-file-earmark-check' : 'bi-file-earmark' }}"></i></span><span class="flex-grow-1"><strong>{{ $label }}</strong><small>{{ $pengajuan->{$field} ? 'Sudah diunggah' : 'Belum diunggah' }}</small></span><i class="bi {{ $pengajuan->{$field} ? 'bi-check-circle-fill text-success' : 'bi-circle text-secondary' }}"></i></div></div>
        @endforeach
        </div>
    </section>
    @if ($pengajuan->bisaDiubahPic())
        <section class="pk-card bg-white p-4 p-md-5">
            <h2 class="h5 fw-bold mb-1">Unggah atau perbarui berkas</h2><p class="text-secondary small mb-2">Anda dapat mengunggah beberapa dokumen sekaligus. Berkas sebelumnya akan diganti jika dokumen yang sama dipilih.</p>
            <div class="alert alert-info small mb-4"><i class="bi bi-info-circle me-2"></i>Ukuran maksimal setiap berkas adalah 5 MB. Jika total unggahan terlalu besar, kurangi jumlah berkas dalam sekali unggah dan coba lagi.</div>
            <form method="POST" enctype="multipart/form-data" action="{{ route('pic.pengajuan.berkas', $pengajuan) }}" class="row g-3">
                @csrf
                <div class="col-md-4"><label class="form-label" for="proposal">Proposal <span class="text-secondary fw-normal">PDF/DOC, maks. 5 MB</span></label><input id="proposal" type="file" name="berkas_proposal" accept=".pdf,.doc,.docx" class="form-control"></div>
                <div class="col-md-4"><label class="form-label" for="rab">RAB <span class="text-secondary fw-normal">PDF/XLS, maks. 5 MB</span></label><input id="rab" type="file" name="berkas_rab" accept=".pdf,.xls,.xlsx" class="form-control"></div>
                <div class="col-md-4"><label class="form-label" for="surat">Surat pengantar <span class="text-secondary fw-normal">PDF/DOC, maks. 5 MB</span></label><input id="surat" type="file" name="berkas_surat_pengantar" accept=".pdf,.doc,.docx" class="form-control"></div>
                <div class="col-12"><label class="form-label" for="link-bukti">Tautan bukti <span class="text-secondary fw-normal">Opsional</span></label><input id="link-bukti" type="url" name="link_bukti" value="{{ old('link_bukti', $pengajuan->link_bukti) }}" placeholder="https://..." class="form-control"><div class="form-text">Masukkan tautan Google Drive atau bukti pendukung lain.</div></div>
                <div class="col-12 d-flex flex-wrap justify-content-between align-items-center gap-3 pt-2">
                    <span class="small text-secondary"><i class="bi bi-info-circle me-1"></i>Pilih berkas atau masukkan tautan baru untuk menyimpan perubahan.</span>
                    <button class="btn btn-pk"><i class="bi bi-cloud-arrow-up me-2"></i>Unggah berkas</button>
                </div>
            </form>
            <hr class="my-4">
            <form method="POST" action="{{ route('pic.pengajuan.ajukan', $pengajuan) }}" onsubmit="return confirm('Ajukan dan langsung publikasikan program kerja ini?')">
                @csrf
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3"><div><strong>Siap mengajukan program?</strong><div class="small text-secondary">Pengajuan hanya dapat diproses jika semua dokumen wajib lengkap.</div></div><button class="btn btn-pk-yellow" {{ empty($dokumenKurang) ? '' : 'disabled' }}><i class="bi bi-send me-2"></i>Ajukan & publikasikan</button></div>
            </form>
        </section>
    @endif
</div>
@endsection
@push('styles')
<style>
.pj-back-link{color:var(--pk-teal);font-weight:650;text-decoration:none}.pj-back-link:hover{color:var(--pk-teal-dark);text-decoration:underline}.pj-detail-head{display:flex;justify-content:space-between;align-items:center;gap:1rem}.pj-document{height:100%;display:flex;align-items:center;gap:.8rem;padding:1rem;border:1px solid var(--pk-border);border-radius:.85rem;background:#fafcfc}.pj-document.is-ready{background:#f4faf6;border-color:#d3ead9}.pj-document-icon{width:2.5rem;height:2.5rem;display:grid;place-items:center;flex:none;border-radius:.7rem;background:#eaf4f4;color:var(--pk-teal);font-size:1.2rem}.pj-document strong,.pj-document small{display:block}.pj-document strong{font-size:.9rem}.pj-document small{margin-top:.15rem;color:var(--pk-muted);font-size:.78rem}@media(max-width:575.98px){.pj-detail-head{align-items:flex-start;flex-direction:column}}
</style>
@endpush
