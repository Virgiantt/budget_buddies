<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Budget Buddies')</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700&display=swap" rel="stylesheet">
  <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
</head>
<body>
  <div class="guest"><div style="width:100%;max-width:420px">
    <h1 class="text-center mb-4" style="color:var(--blue);font-weight:800">Budget Buddies</h1>
    <div class="card p-4">@include('partials.alerts') @yield('content')</div>
  </div></div>
</body>
</html>
