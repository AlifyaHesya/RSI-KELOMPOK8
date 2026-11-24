@extends('layouts.app')
@section('title', 'Rekap · ' . $programKerja->nama_proker)
@section('content')
<div class="container py-5" style="max-width: 760px"><div class="pk-card bg-white p-4 p-md-5">
    <h1 class="h2 text-pk fw-bold">Rekap {{ $programKerja->nama_proker }}</h1>
    @if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if (session('error'))<div class="alert alert-warning">{{ session('error') }}</div>@endif
    <div class="d-flex flex-wrap gap-2 mt-4"><a class="btn btn-pk" href="{{ route('pic.rekap.peserta', $programKerja) }}">Unduh rekap peserta</a><a class="btn btn-outline-primary" href="{{ route('pic.rekap.laporan', $programKerja) }}">Isi laporan kegiatan</a>
        @if ($programKerja->laporan)<a class="btn btn-outline-secondary" href="{{ route('pic.laporan.unduh', $programKerja->laporan) }}">Unduh laporan PDF</a>@endif
    </div>
</div></div>
@endsection
