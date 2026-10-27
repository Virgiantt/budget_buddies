@extends('layouts.app')
@section('title', 'Edit Transaksi')
@section('content')
<h1 class="h3 mb-3">Edit transaksi</h1>
<div class="card p-4" style="max-width:640px">
  <form action="{{ route('transactions.update', $transaction->T_ID) }}" method="POST">
    @csrf @method('PUT')
    @include('transactions._form')
  </form>
</div>
@endsection
