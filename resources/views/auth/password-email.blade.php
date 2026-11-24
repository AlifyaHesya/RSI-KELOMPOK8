@extends('layouts.app')
@section('title', 'Lupa Kata Sandi')
@section('content')
<div class="container py-5" style="max-width: 560px"><div class="card border-0 shadow-sm"><div class="card-body p-4 p-md-5">
    <h1 class="h3">Lupa kata sandi?</h1><p class="text-muted">Masukkan email akun. Jika terdaftar, tautan untuk mengatur ulang kata sandi akan dikirim.</p>
    @if(session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
    <form method="POST" action="{{ route('password.email') }}">@csrf
        <label for="email" class="form-label">Email</label><input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required autocomplete="email">
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <button class="btn btn-primary mt-3 w-100">Kirim tautan reset</button>
    </form><a href="{{ route('login') }}" class="d-block text-center mt-3">Kembali ke login</a>
</div></div></div>
@endsection
