@extends('layouts.app')
@section('title', 'Anggaran')
@section('content')
@php
  $month = (int) request('month', now()->month); $year = (int) request('year', now()->year);
  $bulan = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
@endphp
<div class="d-flex justify-content-between flex-wrap gap-2 align-items-center mb-3">
  <div><h1 class="h3 mb-1">Anggaran bulanan</h1><div class="muted">Atur batas pengeluaran per kategori</div></div>
  <form method="GET" class="d-flex gap-2">
    <select name="month" class="form-select" aria-label="Bulan" onchange="this.form.submit()">
      @foreach ($bulan as $n => $nm) <option value="{{ $n }}" @selected($n === $month)>{{ $nm }}</option> @endforeach
    </select>
    <select name="year" class="form-select" aria-label="Tahun" onchange="this.form.submit()">
      @foreach (range(now()->year - 1, now()->year + 1) as $y) <option @selected($y === $year)>{{ $y }}</option> @endforeach
    </select>
  </form>
</div>

<div class="row g-3 mb-4">
  @forelse ($budgets as $b)
    @php
      $pct = $b->B_amount_limit > 0 ? round($b->spent / $b->B_amount_limit * 100) : 0;
      $cls = $pct >= 100 ? 'over' : ($pct >= 80 ? 'warn' : 'ok');
      $bg = ['over' => '#FDE3EA', 'warn' => '#FFF3C9', 'ok' => '#fff'][$cls];
      $label = ['over' => 'Melebihi batas', 'warn' => 'Hampir habis', 'ok' => ''][$cls];
    @endphp
    <div class="col-md-6"><div class="card p-4" style="background:{{ $bg }}">
      <div class="d-flex justify-content-between align-items-center"><b>{{ $b->category->C_name }}</b>
        <span class="badge rounded-pill badge-in">{{ $pct }}% {{ $label ? '· '.$label : '' }}</span></div>
      <div class="bar my-3"><div class="bar-{{ $cls }}" style="width:{{ min($pct, 100) }}%"></div></div>
      <div class="d-flex justify-content-between align-items-center">
        <span class="muted">Rp {{ number_format($b->spent, 0, ',', '.') }} dari Rp {{ number_format($b->B_amount_limit, 0, ',', '.') }}</span>
        <form action="{{ route('budgets.destroy', $b->B_id) }}" method="POST" onsubmit="return confirm('Hapus anggaran ini?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">Hapus</button></form>
      </div>
    </div></div>
  @empty
    <p class="muted">Belum ada anggaran bulan ini. Atur satu di bawah.</p>
  @endforelse
</div>

<div class="card p-4" style="max-width:640px">
  <h2 class="h5 mb-3">Atur anggaran</h2>
  <form action="{{ route('budgets.store') }}" method="POST" class="row g-3 align-items-end">
    @csrf
    <input type="hidden" name="B_month" value="{{ $month }}"><input type="hidden" name="B_year" value="{{ $year }}">
    <div class="col-md-5"><label class="form-label" for="Categories_C_id">Kategori</label>
      <select id="Categories_C_id" name="Categories_C_id" class="form-select">
        @foreach ($categories as $c) <option value="{{ $c->C_id }}">{{ $c->C_name }}</option> @endforeach
      </select></div>
    <div class="col-md-4"><label class="form-label" for="B_amount_limit">Batas (Rp)</label>
      <input id="B_amount_limit" name="B_amount_limit" type="number" min="1" class="form-control @error('B_amount_limit') is-invalid @enderror" value="{{ old('B_amount_limit') }}">
      @error('B_amount_limit') <div class="invalid-feedback">{{ $message }}</div> @enderror</div>
    <div class="col-md-3"><button class="btn btn-primary w-100">Simpan</button></div>
  </form>
</div>
@endsection
