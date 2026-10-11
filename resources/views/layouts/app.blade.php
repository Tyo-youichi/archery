<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') · {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}?v={{ @filemtime(public_path('css/app.css')) ?: '1' }}" rel="stylesheet">
    @stack('head')
</head>
<body>
@auth
<nav class="navbar navbar-expand-md app-navbar mb-4">
    <div class="container">
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            @include('partials.brand-mark')
            <span>Archer<span class="brand-sub"> Monitor</span></span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav me-auto">
                {{-- Menu mengikuti RBAC: hanya item yang diizinkan role user. --}}
                @foreach(\App\Support\Rbac::menu(auth()->user()) as $item)
                    <li class="nav-item"><a @class(['nav-link', 'active' => request()->routeIs($item['route'])]) href="{{ $item['url'] }}">{{ $item['label'] }}</a></li>
                @endforeach
            </ul>
            <div class="app-user">
                <span class="text-end d-none d-md-block">
                    <span class="app-user__name d-block">{{ auth()->user()->name }}</span>
                    <span class="app-user__role">{{ auth()->user()->roleLabel() }}</span>
                </span>
                @php $ini = collect(explode(' ', trim(auth()->user()->name)))->filter()->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode(''); @endphp
                <span class="avatar-initials">{{ $ini ?: 'U' }}</span>
                <form method="post" action="{{ route('logout') }}">@csrf <button class="btn btn-outline-light btn-sm">Keluar</button></form>
            </div>
        </div>
    </div>
</nav>
@endauth
<main class="container pb-5">
    @if(session('ok'))<div class="alert alert-success">{{ session('ok') }}</div>@endif
    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif
    @yield('content')
</main>
<script src="{{ asset('js/bootstrap.bundle.min.js') }}?v={{ @filemtime(public_path('js/bootstrap.bundle.min.js')) ?: '1' }}"></script>
<script src="{{ asset('js/chart.umd.min.js') }}?v={{ @filemtime(public_path('js/chart.umd.min.js')) ?: '1' }}"></script>
@stack('scripts')
</body>
</html>
