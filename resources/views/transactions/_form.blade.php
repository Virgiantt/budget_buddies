@php $t = $transaction ?? null; $type = old('T_type', $t->T_type ?? 'expense'); @endphp
<div class="mb-3">
  <label class="form-label" for="T_title">Judul</label>
  <input id="T_title" name="T_title" class="form-control @error('T_title') is-invalid @enderror" value="{{ old('T_title', $t->T_title ?? '') }}" placeholder="mis. Makan siang">
  @error('T_title') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
<div class="seg mb-3">
  <input type="radio" name="T_type" id="t-out" value="expense" @checked($type === 'expense')><label for="t-out" class="l-out">Pengeluaran</label>
  <input type="radio" name="T_type" id="t-in" value="income" @checked($type === 'income')><label for="t-in" class="l-in">Pemasukan</label>
</div>
<div class="mb-3">
  <label class="form-label" for="T_amount">Jumlah (Rp)</label>
  <input id="T_amount" name="T_amount" type="number" min="1" class="form-control @error('T_amount') is-invalid @enderror" value="{{ old('T_amount', isset($t) ? (int) $t->T_amount : '') }}">
  @error('T_amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
<div class="row g-3 mb-3">
  <div class="col-md-6">
    <label class="form-label" for="cat">Kategori</label>
    <select id="cat" name="Categories_C_id" class="form-select @error('Categories_C_id') is-invalid @enderror">
      @foreach ($categories as $c)
        <option value="{{ $c->C_id }}" @selected(old('Categories_C_id', $t->Categories_C_id ?? '') == $c->C_id)>{{ $c->C_name }}</option>
      @endforeach
    </select>
    @error('Categories_C_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>
  <div class="col-md-6">
    <label class="form-label" for="acc">Akun</label>
    <select id="acc" name="Accounts_A_id" class="form-select @error('Accounts_A_id') is-invalid @enderror">
      @foreach ($accounts as $a)
        <option value="{{ $a->A_id }}" @selected(old('Accounts_A_id', $t->Accounts_A_id ?? '') == $a->A_id)>{{ $a->A_name }}</option>
      @endforeach
    </select>
    @error('Accounts_A_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    @if (count($accounts) === 0) <div class="small muted mt-1">Belum ada akun. <a href="{{ route('accounts.index') }}">Buat akun dulu</a></div> @endif
  </div>
</div>
<div class="mb-4">
  <label class="form-label" for="T_transaction_date">Tanggal</label>
  <input id="T_transaction_date" name="T_transaction_date" type="date" class="form-control @error('T_transaction_date') is-invalid @enderror" value="{{ old('T_transaction_date', $t->T_transaction_date ?? now()->toDateString()) }}">
  @error('T_transaction_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
<div class="d-flex gap-2"><button class="btn btn-primary">Simpan</button><a href="{{ route('transactions.index') }}" class="btn btn-soft">Batal</a></div>
