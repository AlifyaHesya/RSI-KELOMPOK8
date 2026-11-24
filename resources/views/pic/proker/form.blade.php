@extends('layouts.app')
@section('title', $programKerja->exists ? 'Ubah Program Kerja' : 'Buat Program Kerja')
@section('content')
<div class="container py-5" style="max-width: 900px"><div class="pk-card bg-white p-4 p-md-5">
    <h1 class="h2 text-pk fw-bold">{{ $programKerja->exists ? 'Ubah' : 'Buat' }} Program Kerja</h1>
    @if ($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <form method="POST" enctype="multipart/form-data" action="{{ $programKerja->exists ? route('pic.proker.update', $programKerja) : route('pic.proker.store') }}" class="row g-3">
        @csrf @if ($programKerja->exists) @method('PUT') @endif
        <div class="col-md-6"><label class="form-label">Nama program</label><input name="nama_proker" required maxlength="100" class="form-control" value="{{ old('nama_proker', $programKerja->nama_proker) }}"></div>
        <div class="col-md-6"><label class="form-label">Kategori</label><select name="kategori" required class="form-select"><option value="">Pilih kategori</option>@foreach ($kategori as $item)<option value="{{ $item->value }}" @selected(old('kategori', $programKerja->kategori?->value) === $item->value)>{{ $item->value }}</option>@endforeach</select></div>
        <div class="col-12"><label class="form-label">Deskripsi</label><textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi', $programKerja->deskripsi) }}</textarea></div>
        <div class="col-12"><label class="form-label">Poster program (JPG/PNG, maks. 2 MB)</label><input type="file" name="poster" accept="image/jpeg,image/png" class="form-control">@if ($programKerja->poster_path)<img src="{{ asset('storage/'.$programKerja->poster_path) }}" alt="Poster {{ $programKerja->nama_proker }}" class="img-thumbnail mt-2" style="max-height: 180px">@endif @error('poster')<div class="small text-danger">{{ $message }}</div>@enderror</div>
        <div class="col-md-4"><label class="form-label">Biro</label><input name="biro" required class="form-control" value="{{ old('biro', $programKerja->biro) }}"></div>
        <div class="col-md-4"><label class="form-label">Lokasi</label><input name="lokasi" required class="form-control" value="{{ old('lokasi', $programKerja->lokasi) }}"></div>
        <div class="col-md-4"><label class="form-label">Kuota</label><input type="number" name="kuota" min="1" required class="form-control" value="{{ old('kuota', $programKerja->kuota) }}"></div>
        <div class="col-md-4"><label class="form-label">Tanggal pelaksanaan</label><input type="date" name="tanggal_pelaksanaan" required class="form-control" value="{{ old('tanggal_pelaksanaan', $programKerja->tanggal_pelaksanaan?->format('Y-m-d')) }}"></div>
        <div class="col-md-4"><label class="form-label">Waktu pelaksanaan</label><input type="time" name="waktu_pelaksanaan" required class="form-control" value="{{ old('waktu_pelaksanaan', $programKerja->waktu_pelaksanaan ? substr($programKerja->waktu_pelaksanaan, 0, 5) : '') }}"></div>
        <div class="col-md-4"><label class="form-label">Buka pendaftaran</label><input type="datetime-local" name="tanggal_buka_pendaftaran" required class="form-control" value="{{ old('tanggal_buka_pendaftaran', $programKerja->tanggal_buka_pendaftaran?->format('Y-m-d\TH:i')) }}"></div>
        <div class="col-md-4"><label class="form-label">Batas pendaftaran</label><input type="datetime-local" name="batas_pendaftaran" required class="form-control" value="{{ old('batas_pendaftaran', $programKerja->batas_pendaftaran?->format('Y-m-d\TH:i')) }}"></div>
        <div class="col-12 d-flex justify-content-end gap-2">
            <a class="btn btn-outline-secondary" href="{{ $programKerja->exists ? route('pic.proker.edit', $programKerja) : route('pic.proker.create') }}">Reset formulir</a>
            <a class="btn btn-light border" href="{{ route('pic.proker.index') }}">Batal</a>
            <button class="btn btn-pk">Simpan</button>
        </div>
    </form>
</div></div>
@endsection
