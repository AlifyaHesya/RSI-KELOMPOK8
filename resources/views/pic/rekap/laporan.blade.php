@extends('layouts.app')
@section('title', 'Laporan Kegiatan')
@section('content')
<div class="container py-5" style="max-width: 850px"><div class="pk-card bg-white p-4 p-md-5">
    <h1 class="h2 text-pk fw-bold">Laporan Kegiatan</h1><p class="text-secondary">{{ $programKerja->nama_proker }}</p>
    @if ($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <form method="POST" action="{{ route('pic.rekap.laporan.simpan', $programKerja) }}" class="row g-3">@csrf
        <div class="col-12"><label class="form-label">Ringkasan kegiatan</label><textarea name="ringkasan_kegiatan" required rows="4" class="form-control">{{ old('ringkasan_kegiatan', $laporan->ringkasan_kegiatan) }}</textarea></div>
        <div class="col-md-6"><label class="form-label">Kendala</label><textarea name="kendala" rows="3" class="form-control">{{ old('kendala', $laporan->kendala) }}</textarea></div>
        <div class="col-md-6"><label class="form-label">Evaluasi</label><textarea name="evaluasi" rows="3" class="form-control">{{ old('evaluasi', $laporan->evaluasi) }}</textarea></div>
        <div class="col-md-4"><label class="form-label">Realisasi anggaran</label><input type="number" min="0" step="0.01" name="anggaran_realisasi" value="{{ old('anggaran_realisasi', $laporan->anggaran_realisasi) }}" class="form-control"></div>
        <div class="col-md-4"><label class="form-label">Peserta hadir</label><input type="number" min="0" name="jumlah_peserta_hadir" value="{{ old('jumlah_peserta_hadir', $laporan->jumlah_peserta_hadir) }}" class="form-control"></div>
        <div class="col-md-4"><label class="form-label">Tanggal realisasi</label><input type="date" name="tanggal_realisasi" value="{{ old('tanggal_realisasi', $laporan->tanggal_realisasi?->format('Y-m-d')) }}" class="form-control"></div>
        <div class="col-12 d-flex justify-content-end gap-2"><a class="btn btn-light border" href="{{ route('pic.rekap.opsi', $programKerja) }}">Batal</a><button class="btn btn-pk">Simpan laporan</button></div>
    </form>
</div></div>
@endsection
