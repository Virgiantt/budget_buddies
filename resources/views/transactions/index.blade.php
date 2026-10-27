@extends('layouts.app')
@section('title', 'Transaksi')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h3 m-0">Riwayat transaksi</h1>
  <a href="{{ route('transactions.create') }}" class="btn btn-primary">+ Tambah</a>
</div>

<form method="GET" class="card p-3 mb-3">
  <div class="row g-3 align-items-end">
    <div class="col-md-3"><label class="form-label" for="from">Dari tanggal</label><input id="from" type="date" name="from" value="{{ request('from') }}" class="form-control"></div>
    <div class="col-md-3"><label class="form-label" for="to">Sampai tanggal</label><input id="to" type="date" name="to" value="{{ request('to') }}" class="form-control"></div>
    <div class="col-md-3"><label class="form-label" for="type">Jenis</label>
      <select id="type" name="type" class="form-select">
        <option value="">Semua</option>
        <option value="income" @selected(request('type') === 'income')>Pemasukan</option>
        <option value="expense" @selected(request('type') === 'expense')>Pengeluaran</option>
      </select></div>
    <div class="col-md-3"><button class="btn btn-primary w-100">Terapkan</button></div>
  </div>
</form>

<div class="card p-3 table-responsive">
  <table class="table align-middle mb-0">
    <thead><tr><th>Tanggal</th><th>Judul</th><th>Kategori</th><th>Jenis</th><th>Jumlah</th><th>Aksi</th></tr></thead>
    <tbody>
    @forelse ($transactions as $t)
      <tr>
        <td>{{ \Carbon\Carbon::parse($t->T_transaction_date)->translatedFormat('d M Y') }}</td>
        <td class="fw-bold">{{ $t->T_title }}</td>
        <td>{{ $t->category->C_name }}</td>
        <td><span class="badge rounded-pill {{ $t->T_type === 'income' ? 'badge-in' : 'badge-out' }}">{{ $t->T_type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}</span></td>
        <td>Rp {{ number_format($t->T_amount, 0, ',', '.') }}</td>
        <td><div class="d-flex gap-2">
          <a href="{{ route('transactions.edit', $t->T_ID) }}" class="btn btn-soft btn-sm">Edit</a>
          <form action="{{ route('transactions.destroy', $t->T_ID) }}" method="POST" onsubmit="return confirm('Hapus transaksi ini?')">
            @csrf @method('DELETE')<button class="btn btn-danger btn-sm">Hapus</button>
          </form></div></td>
      </tr>
    @empty
      <tr><td colspan="6" class="text-center muted py-4">Belum ada transaksi. Klik "+ Tambah" untuk mulai 🌱</td></tr>
    @endforelse
    </tbody>
  </table>
</div>
@endsection
