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
@php $ini = collect(explode(' ', trim(auth()->user()->name)))->filter()->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode(''); @endphp
<div class="app-layout">
    <div class="app-backdrop" id="appBackdrop"></div>

    <aside class="app-sidebar" id="sidebar">
        <div class="app-sidebar__head">
            <a class="app-sidebar__brand" href="{{ route('dashboard.index') }}">
                @include('partials.brand-mark')
                <span>Archer<span class="brand-sub"> Monitor</span></span>
            </a>
        </div>
        <nav class="app-sidebar__nav">
            {{-- Menu mengikuti RBAC: hanya item yang diizinkan role user. --}}
            @foreach(\App\Support\Rbac::menu(auth()->user()) as $item)
                <a @class(['side-link', 'active' => request()->routeIs($item['route'])]) href="{{ $item['url'] }}">{{ $item['label'] }}</a>
            @endforeach
        </nav>
        <div class="app-sidebar__user">
            <span class="avatar-initials">{{ $ini ?: 'U' }}</span>
            <span class="app-user__meta">
                <span class="app-user__name">{{ auth()->user()->name }}</span>
                <span class="app-user__role">{{ auth()->user()->roleLabel() }}</span>
            </span>
            <form method="post" action="{{ route('logout') }}">@csrf <button class="btn btn-outline-light btn-sm w-100 mt-2">Keluar</button></form>
        </div>
    </aside>

    <div class="app-main">
        <header class="app-topbar d-md-none">
            <button class="app-burger" id="sidebarToggle" type="button" aria-label="Menu">
                <svg width="22" height="22" viewBox="0 0 16 16" fill="currentColor"><path d="M2.5 4h11a.5.5 0 0 0 0-1h-11a.5.5 0 0 0 0 1M2 8a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11A.5.5 0 0 1 2 8m0 4a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5"/></svg>
            </button>
            <a class="app-topbar__brand" href="{{ route('dashboard.index') }}">Archer Monitor</a>
        </header>
        <main class="app-content">
            @if(session('ok'))<div class="alert alert-success">{{ session('ok') }}</div>@endif
            @if(session('warn'))<div class="alert alert-warning">{{ session('warn') }}</div>@endif
            @if($errors->any())
                <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
            @endif
            @yield('content')
        </main>
    </div>
</div>
@else
<main class="container pb-5 pt-4">
    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif
    @yield('content')
</main>
@endauth
<script src="{{ asset('js/bootstrap.bundle.min.js') }}?v={{ @filemtime(public_path('js/bootstrap.bundle.min.js')) ?: '1' }}"></script>
<script src="{{ asset('js/chart.umd.min.js') }}?v={{ @filemtime(public_path('js/chart.umd.min.js')) ?: '1' }}"></script>
<script>
// Sidebar slide-in untuk layar kecil.
(function () {
    const toggle = document.getElementById('sidebarToggle');
    const backdrop = document.getElementById('appBackdrop');
    const close = () => document.body.classList.remove('sidebar-open');
    if (toggle) toggle.addEventListener('click', () => document.body.classList.toggle('sidebar-open'));
    if (backdrop) backdrop.addEventListener('click', close);
    document.querySelectorAll('.app-sidebar .side-link').forEach(a => a.addEventListener('click', close));
})();
</script>
@stack('scripts')
</body>
</html>
