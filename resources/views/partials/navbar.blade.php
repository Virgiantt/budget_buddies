<nav class="navbar navbar-expand-lg">
  <div class="container">
    <a class="navbar-brand" href="{{ route('dashboard') }}">Budget Buddies</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav" aria-label="Menu"><span class="navbar-toggler-icon"></span></button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav mx-lg-auto gap-lg-1">
        <li><a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a></li>
        <li><a class="nav-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}" href="{{ route('transactions.index') }}">Transaksi</a></li>
        <li><a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">Kategori</a></li>
        <li><a class="nav-link {{ request()->routeIs('accounts.*') ? 'active' : '' }}" href="{{ route('accounts.index') }}">Akun</a></li>
        <li><a class="nav-link {{ request()->routeIs('budgets.*') ? 'active' : '' }}" href="{{ route('budgets.index') }}">Anggaran</a></li>
      </ul>
      <div class="d-flex align-items-center gap-3">
        <span class="muted">{{ auth()->user()?->name ?? 'Pengguna' }}</span>
        <form action="{{ route('logout') }}" method="POST">@csrf<button class="btn btn-soft btn-sm">Keluar</button></form>
      </div>
    </div>
  </div>
</nav>
