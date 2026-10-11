<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') · {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .pct-bar{height:.6rem}
        .card-title-sm{font-size:.8rem;text-transform:uppercase;letter-spacing:.04em;color:#6c757d}
        table.table-compact td,table.table-compact th{padding:.35rem .5rem;font-size:.9rem}
    </style>
    @stack('head')
</head>
<body class="bg-body-tertiary">
<nav class="navbar navbar-expand-md navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="{{ route('dashboard') }}">🏹 {{ config('app.name') }}</a>
        @auth
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav me-auto">
                {{-- Menu mengikuti RBAC: hanya item yang diizinkan role user (config/rbac.php). --}}
                @foreach(\App\Support\Rbac::menu(auth()->user()) as $item)
                    <li class="nav-item"><a @class(['nav-link', 'active' => request()->routeIs($item['route'])]) href="{{ $item['url'] }}">{{ $item['label'] }}</a></li>
                @endforeach
            </ul>
            <span class="navbar-text me-3">{{ auth()->user()->name }} <span class="badge text-bg-secondary">{{ auth()->user()->roleLabel() }}</span></span>
            <form method="post" action="{{ route('logout') }}">@csrf <button class="btn btn-outline-light btn-sm">Keluar</button></form>
        </div>
        @endauth
    </div>
</nav>
<main class="container pb-5">
    @if(session('ok'))<div class="alert alert-success">{{ session('ok') }}</div>@endif
    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif
    @yield('content')
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
