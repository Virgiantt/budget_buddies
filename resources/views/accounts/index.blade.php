@extends('layouts.app')
@section('title', 'Akun')
@section('content')
<h1 class="h3 mb-3">Akun keuangan</h1>
<div class="row g-3 mb-4">
  @forelse ($accounts as $a)
  <div class="col-md-6 col-lg-4"><div class="card p-3">
    <div class="d-flex justify-content-between"><b>{{ $a->A_name }}</b><span class="badge rounded-pill badge-in">{{ $a->A_account_type }}</span></div>
    <div class="muted mt-2">Saldo awal</div><div class="h5 mb-3">Rp {{ number_format($a->A_initial_balance, 0, ',', '.') }}</div>
    <div class="d-flex gap-2">
      <button class="btn btn-soft btn-sm" data-bs-toggle="modal" data-bs-target="#editModal" data-action="{{ route('accounts.update', $a->A_id) }}"
        data-name="{{ $a->A_name }}" data-type="{{ $a->A_account_type }}" data-balance="{{ (int) $a->A_initial_balance }}">Edit</button>
      <form action="{{ route('accounts.destroy', $a->A_id) }}" method="POST" onsubmit="return confirm('Hapus akun ini?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">Hapus</button></form>
    </div>
  </div></div>
  @empty
  <p class="muted">Belum ada akun. Buat akun pertamamu di bawah (mis. Dompet atau Rekening).</p>
  @endforelse
</div>

<div class="card p-4" style="max-width:760px">
  <h2 class="h5 mb-3">Tambah akun</h2>
  <form action="{{ route('accounts.store') }}" method="POST" class="row g-3 align-items-end">
    @csrf
    <div class="col-md-4"><label class="form-label" for="A_name">Nama akun</label>
      <input id="A_name" name="A_name" class="form-control @error('A_name') is-invalid @enderror" value="{{ old('A_name') }}" placeholder="mis. Dompet">
      @error('A_name') <div class="invalid-feedback">{{ $message }}</div> @enderror</div>
    <div class="col-md-3"><label class="form-label" for="A_account_type">Tipe</label>
      <select id="A_account_type" name="A_account_type" class="form-select"><option>Tunai</option><option>Bank</option><option>E-wallet</option></select></div>
    <div class="col-md-3"><label class="form-label" for="A_initial_balance">Saldo awal (Rp)</label>
      <input id="A_initial_balance" name="A_initial_balance" type="number" min="0" class="form-control @error('A_initial_balance') is-invalid @enderror" value="{{ old('A_initial_balance', 0) }}">
      @error('A_initial_balance') <div class="invalid-feedback">{{ $message }}</div> @enderror</div>
    <div class="col-md-2"><button class="btn btn-primary w-100">Simpan</button></div>
  </form>
</div>

<div class="modal fade" id="editModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><form method="POST" class="modal-content p-3" style="border-radius:22px">
  @csrf @method('PUT')
  <h2 class="h5 mb-3">Edit akun</h2>
  <div class="mb-3"><label class="form-label" for="e_name">Nama</label><input id="e_name" name="A_name" class="form-control"></div>
  <div class="mb-3"><label class="form-label" for="e_type">Tipe</label><select id="e_type" name="A_account_type" class="form-select"><option>Tunai</option><option>Bank</option><option>E-wallet</option></select></div>
  <div class="mb-3"><label class="form-label" for="e_bal">Saldo awal (Rp)</label><input id="e_bal" name="A_initial_balance" type="number" class="form-control"></div>
  <div class="d-flex gap-2"><button class="btn btn-primary">Simpan</button><button type="button" class="btn btn-soft" data-bs-dismiss="modal">Batal</button></div>
</form></div></div>
@endsection

@push('scripts')
<script>
document.getElementById('editModal').addEventListener('show.bs.modal', e => {
  const b = e.relatedTarget, m = e.currentTarget;
  m.querySelector('form').action = b.dataset.action;
  m.querySelector('#e_name').value = b.dataset.name;
  m.querySelector('#e_type').value = b.dataset.type;
  m.querySelector('#e_bal').value = b.dataset.balance;
});
</script>
@endpush
