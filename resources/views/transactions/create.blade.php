@extends('layouts.app')
@section('title', 'Catat Transaksi')
@section('content')
<h1 class="h3 mb-3">Catat transaksi</h1>
<div class="card p-4" style="max-width:640px">
  <form action="{{ route('transactions.store') }}" method="POST">
    @csrf
    @include('transactions._form')
  </form>
</div>
@endsection
