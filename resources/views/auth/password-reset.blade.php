@extends('layouts.app')
@section('title', 'Atur Ulang Kata Sandi')
@section('content')
<div class="container py-5" style="max-width: 560px"><div class="card border-0 shadow-sm"><div class="card-body p-4 p-md-5">
    <h1 class="h3">Atur ulang kata sandi</h1>
    <form method="POST" action="{{ route('password.update') }}">@csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="mb-3"><label for="email" class="form-label">Email</label><input id="email" type="email" name="email" value="{{ old('email', $email) }}" class="form-control @error('email') is-invalid @enderror" required autocomplete="email">@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="mb-3"><label for="password" class="form-label">Kata sandi baru</label><input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="new-password">@error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="mb-3"><label for="password_confirmation" class="form-label">Konfirmasi kata sandi</label><input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required autocomplete="new-password"></div>
        <button class="btn btn-primary w-100">Simpan kata sandi baru</button>
    </form>
</div></div></div>
@endsection
