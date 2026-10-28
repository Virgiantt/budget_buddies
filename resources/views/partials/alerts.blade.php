@foreach (['success' => 'success', 'error' => 'danger'] as $key => $type)
  @if (session($key))
    <div class="alert alert-{{ $type }} alert-dismissible fade show fw-bold" style="border-radius:14px">
      {{ session($key) }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
  @endif
@endforeach
@if (isset($errors) && $errors->any())
  <div class="alert alert-danger fw-bold" style="border-radius:14px">Ada isian yang belum benar. Cek pesan merah di bawah kolom.</div>
@endif
