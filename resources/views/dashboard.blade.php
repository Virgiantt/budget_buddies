@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<h1 class="h3 mb-1">Hai, {{ auth()->user()?->U_name ?? 'Kyla' }}! 👋</h1>
<p class="muted mb-4">Ringkasan keuanganmu</p>

<div class="row g-3 mb-4">
  <div class="col-md-4"><div class="stat" style="background:#CFE3F8"><div class="muted">Saldo</div><h2 class="mt-2 mb-0">Rp {{ number_format($balance, 0, ',', '.') }}</h2></div></div>
  <div class="col-md-4"><div class="stat" style="background:#FFE9A8"><div class="muted">Total pemasukan</div><h2 class="mt-2 mb-0">Rp {{ number_format($totalIncome, 0, ',', '.') }}</h2></div></div>
  <div class="col-md-4"><div class="stat" style="background:#FBD5E0"><div class="muted">Total pengeluaran</div><h2 class="mt-2 mb-0">Rp {{ number_format($totalExpense, 0, ',', '.') }}</h2></div></div>
</div>

<div class="row g-3 mb-4">
  <div class="col-lg-6"><div class="card p-4 h-100"><h3 class="h5">Pemasukan vs pengeluaran</h3><canvas id="chart" height="200"></canvas></div></div>
  <div class="col-lg-6"><div class="card p-4 h-100">
    <div class="d-flex justify-content-between"><h3 class="h5">Transaksi terbaru</h3><a href="{{ route('transactions.index') }}" class="muted">Lihat semua</a></div>
    @forelse ($recentTransactions as $t)
      <div class="d-flex justify-content-between align-items-center py-2 border-top">
        <div><b>{{ $t->T_title }}</b><div class="muted small">{{ $t->category->C_name }} · {{ \Carbon\Carbon::parse($t->T_transaction_date)->translatedFormat('d M') }}</div></div>
        <span class="badge rounded-pill {{ $t->T_type === 'income' ? 'badge-in' : 'badge-out' }}">{{ $t->T_type === 'income' ? '+' : '−' }} Rp {{ number_format($t->T_amount, 0, ',', '.') }}</span>
      </div>
    @empty
      <p class="muted mt-3">Belum ada transaksi. Catat yang pertama!</p>
    @endforelse
  </div></div>
</div>
<a href="{{ route('transactions.create') }}" class="btn btn-primary">+ Catat transaksi</a>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
  const d = @json($weeklyChart);
  new Chart(document.getElementById('chart'), {
    type: 'bar',
    data: { labels: d.labels, datasets: [
      { label: 'Pemasukan', data: d.income, backgroundColor: '#8DB8EA', borderRadius: 8 },
      { label: 'Pengeluaran', data: d.expense, backgroundColor: '#F4A9BF', borderRadius: 8 }
    ]},
    options: { plugins: { legend: { position: 'bottom' } }, scales: { y: { beginAtZero: true } } }
  });
</script>
@endpush
