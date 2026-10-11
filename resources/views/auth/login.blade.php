@extends('layouts.app')
@section('title', 'Masuk')
@section('content')
<div class="auth-wrap"><div class="auth-card">
    <div class="auth-hero">
        <svg class="auth-hero__ring" viewBox="0 0 48 48" aria-hidden="true"><circle cx="24" cy="24" r="23" fill="#e4f0f1"/><circle cx="24" cy="24" r="17" fill="#0e6e78"/><circle cx="24" cy="24" r="11" fill="#fff"/><circle cx="24" cy="24" r="5.5" fill="#f2b01e"/></svg>
        <h1>{{ config('app.name') }}</h1>
        <p>Masuk untuk memantau perkembangan atlet.</p>
    </div>
    <div class="card"><div class="card-body p-4">
        <form method="post" action="{{ route('login') }}">@csrf
            <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus></div>
            <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
            <div class="form-check mb-3"><input class="form-check-input" type="checkbox" name="remember" id="rem"><label class="form-check-label" for="rem">Ingat saya</label></div>
            <button class="btn btn-primary w-100">Masuk</button>
        </form>
        <p class="mt-3 mb-0 text-center"><a href="{{ route('register') }}">Daftar sebagai atlet</a></p>
    </div></div>
</div></div>
@endsection
