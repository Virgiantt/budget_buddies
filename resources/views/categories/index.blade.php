@extends('layouts.app')
@section('title', 'Kategori')
@section('content')
<h1 class="h3 mb-3">Kategori transaksi</h1>
<div class="row g-3 mb-4">
  @forelse ($categories as $c)
  <div class="col-md-6 col-lg-4"><div class="card p-3 d-flex flex-row justify-content-between align-items-center">
    <div><b>{{ $c->C_name }}</b><div class="mt-1"><span class="badge rounded-pill {{ $c->C_type === 'income' ? 'badge-in' : 'badge-out' }}">{{ $c->C_type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}</span></div></div>
    <div class="d-flex gap-2">
      <button class="btn btn-soft btn-sm" data-bs-toggle="modal" data-bs-target="#editModal"
        data-action="{{ route('categories.update', $c->C_id) }}" data-name="{{ $c->C_name }}" data-type="{{ $c->C_type }}">Edit</button>
      <form action="{{ route('categories.destroy', $c->C_id) }}" method="POST" onsubmit="return confirm('Hapus kategori ini?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">Hapus</button></form>
    </div>
  </div></div>
  @empty
  <p class="muted">Belum ada kategori. Tambahkan di bawah.</p>
  @endforelse
</div>

<div class="card p-4" style="max-width:640px">
  <h2 class="h5 mb-3">Tambah kategori</h2>
  <form action="{{ route('categories.store') }}" method="POST" class="row g-3 align-items-end">
    @csrf
    <div class="col-md-5"><label class="form-label" for="C_name">Nama kategori</label>
      <input id="C_name" name="C_name" class="form-control @error('C_name') is-invalid @enderror" value="{{ old('C_name') }}" placeholder="mis. Hiburan">
      @error('C_name') <div class="invalid-feedback">{{ $message }}</div> @enderror</div>
    <div class="col-md-4"><label class="form-label" for="C_type">Jenis</label>
      <select id="C_type" name="C_type" class="form-select"><option value="expense">Pengeluaran</option><option value="income">Pemasukan</option></select></div>
    <div class="col-md-3"><button class="btn btn-primary w-100">Simpan</button></div>
  </form>
</div>

<div class="modal fade" id="editModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><form method="POST" class="modal-content p-3" style="border-radius:22px">
  @csrf @method('PUT')
  <h2 class="h5 mb-3">Edit kategori</h2>
  <div class="mb-3"><label class="form-label" for="e_name">Nama</label><input id="e_name" name="C_name" class="form-control"></div>
  <div class="mb-3"><label class="form-label" for="e_type">Jenis</label><select id="e_type" name="C_type" class="form-select"><option value="expense">Pengeluaran</option><option value="income">Pemasukan</option></select></div>
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
});
</script>
@endpush
