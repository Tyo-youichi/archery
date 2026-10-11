@extends('layouts.app')
@section('title', 'Masuk')
@section('content')
<div class="row justify-content-center"><div class="col-md-5">
    <div class="card shadow-sm"><div class="card-body">
        <h1 class="h4 mb-3">Masuk</h1>
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
