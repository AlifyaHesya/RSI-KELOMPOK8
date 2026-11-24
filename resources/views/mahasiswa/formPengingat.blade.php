{{-- Pengaturan pengingat pendaftaran (UC-07). --}}
@extends('layouts.mahasiswa')
@section('title', 'Aktifkan Pengingat')
@section('tanpa-pencarian', '1')

@section('content')
<div class="container py-5" style="max-width: 560px">
    <div class="pk-card bg-white p-4 p-md-5">
        <h1 class="h4 fw-bold text-pk">Aktifkan Pengingat</h1>
        <p class="text-secondary small">
            {{ $programKerja->nama_proker }}<br>
            Pengingat dapat dipilih sebelum batas pendaftaran:
            <strong>{{ $tanggalDeadline->translatedFormat('d F Y, H.i') }} WIB</strong>.
        </p>

        <form id="pilihWaktuPengingat" method="POST" action="{{ route('pengingat.simpan', $programKerja) }}" novalidate>
            @csrf
            <label for="waktu_pengingat" class="form-label fw-semibold small">Pilih waktu pengingat</label>
            <select id="waktu_pengingat" name="waktu_pengingat" class="form-select @error('waktu_pengingat') is-invalid @enderror" required>
                <option value="">Pilih waktu</option>
                @foreach ([['7 hari sebelumnya', '7 days'], ['3 hari sebelumnya', '3 days'], ['1 hari sebelumnya', '1 day'], ['3 jam sebelumnya', '3 hours'], ['1 jam sebelumnya', '1 hour']] as [$label, $offset])
                    @php($waktuPilihan = $tanggalDeadline->copy()->sub($offset))
                    @if ($waktuPilihan->isFuture())
                        <option value="{{ $waktuPilihan->format('Y-m-d\TH:i') }}" @selected(old('waktu_pengingat') === $waktuPilihan->format('Y-m-d\TH:i'))>{{ $label }} · {{ $waktuPilihan->translatedFormat('d M Y, H:i') }}</option>
                    @endif
                @endforeach
            </select>
            @error('waktu_pengingat')<div class="small text-danger mt-1">*{{ $message }}</div>@enderror

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('katalog.show', $programKerja) }}" class="btn btn-light border">Batal</a>
                <button class="btn btn-pk px-4">Aktifkan</button>
            </div>
        </form>
    </div>
</div>
@endsection
