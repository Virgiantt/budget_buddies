@extends('layouts.guest')
@section('title', 'Masuk')
@section('content')
<h2 class="h4 mb-3">Masuk</h2>
<form action="{{ route('login') }}" method="POST">
  @csrf
  <div class="mb-3">
    <label class="form-label" for="email">Email</label>
    <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" autofocus>
    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>
  <div class="mb-4">
    <label class="form-label" for="password">Password</label>
    <input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror">
    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>
  <button class="btn btn-primary w-100">Masuk</button>
</form>
<p class="text-center muted mt-3 mb-0">Belum punya akun? <a href="{{ route('register') }}">Daftar</a></p>
@endsection
