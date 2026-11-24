@extends('layouts.app')
@section('title', 'Unduh Rekap Peserta')
@section('content')
<div class="container py-5" style="max-width: 620px"><div class="pk-card bg-white p-4 p-md-5">
    <h1 class="h3 text-pk fw-bold">Rekap Peserta</h1><p>{{ $programKerja->nama_proker }} · {{ $jumlah }} peserta terdaftar.</p>
    <form method="GET" action="{{ route('pic.rekap.peserta.unduh', $programKerja) }}">
        <label class="form-label" for="format">Format file</label><select id="format" name="format" required class="form-select"><option value="pdf">PDF</option><option value="excel">CSV (Excel)</option></select>
        <div class="d-flex justify-content-end gap-2 mt-4"><a class="btn btn-light border" href="{{ route('pic.rekap.opsi', $programKerja) }}">Batal</a><button class="btn btn-pk">Unduh</button></div>
    </form>
</div></div>
@endsection
