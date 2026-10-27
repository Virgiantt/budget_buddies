@extends('layouts.guest')
@section('title', 'Daftar')
@section('content')
<h2 class="h4 mb-3">Buat akun</h2>
<form action="{{ route('register') }}" method="POST">
  @csrf
  @foreach ([['name','Nama','text'],['email','Email','email'],['password','Password','password'],['password_confirmation','Ulangi password','password']] as [$f,$l,$t])
  <div class="mb-3">
    <label class="form-label" for="{{ $f }}">{{ $l }}</label>
    <input id="{{ $f }}" name="{{ $f }}" type="{{ $t }}" class="form-control @error($f) is-invalid @enderror" value="{{ $t === 'password' ? '' : old($f) }}">
    @error($f) <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>
  @endforeach
  <button class="btn btn-primary w-100 mt-2">Daftar</button>
</form>
<p class="text-center muted mt-3 mb-0">Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></p>
@endsection
