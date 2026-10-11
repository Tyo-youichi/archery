@extends('layouts.app')
@section('title', 'Registrasi Atlet')
@section('content')
<div class="auth-wrap"><div class="auth-card auth-card--wide">
    <div class="auth-hero">
        <svg class="auth-hero__ring" viewBox="0 0 48 48" aria-hidden="true"><circle cx="24" cy="24" r="23" fill="#e4f0f1"/><circle cx="24" cy="24" r="17" fill="#0e6e78"/><circle cx="24" cy="24" r="11" fill="#fff"/><circle cx="24" cy="24" r="5.5" fill="#f2b01e"/></svg>
        <h1>Daftar sebagai atlet</h1>
        <p>Buat akun untuk mulai mencatat latihan dan melihat profilmu.</p>
    </div>
    <div class="card"><div class="card-body p-4">
        <form method="post" action="{{ route('register') }}" class="row g-3">@csrf
            <div class="col-md-6"><label class="form-label">Nama lengkap</label><input name="name" value="{{ old('name') }}" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label">No. HP / WhatsApp</label><input name="phone" value="{{ old('phone') }}" class="form-control"></div>
            <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" value="{{ old('email') }}" class="form-control" required></div>
            <div class="col-md-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required minlength="8"></div>
            <div class="col-md-3"><label class="form-label">Ulangi</label><input type="password" name="password_confirmation" class="form-control" required></div>
            <div class="col-md-4"><label class="form-label">Jenis kelamin</label>
                <select name="gender" class="form-select" required><option value="">-</option><option value="L" @selected(old('gender')=='L')>Laki-laki</option><option value="P" @selected(old('gender')=='P')>Perempuan</option></select></div>
            <div class="col-md-4"><label class="form-label">Tempat lahir</label><input name="birth_place" value="{{ old('birth_place') }}" class="form-control"></div>
            <div class="col-md-4"><label class="form-label">Tanggal lahir</label><input type="date" name="birth_date" value="{{ old('birth_date') }}" class="form-control"></div>
            <div class="col-md-6"><label class="form-label">Asal daerah</label><input name="origin" value="{{ old('origin') }}" class="form-control"></div>
            <div class="col-md-3"><label class="form-label">Divisi</label>
                <select name="division" class="form-select"><option value="">-</option>@foreach(config('archery.divisions') as $d)<option @selected(old('division')==$d)>{{ $d }}</option>@endforeach</select></div>
            <div class="col-md-3"><label class="form-label">Jenis pendaftaran</label>
                <select name="registration_type" class="form-select" required>@foreach(config('archery.registration_types') as $k=>$l)<option value="{{ $k }}" @selected(old('registration_type')==$k)>{{ $l }}</option>@endforeach</select></div>
            <div class="col-12 d-flex align-items-center gap-2"><button class="btn btn-primary">Daftar</button> <a class="btn btn-link" href="{{ route('login') }}">Sudah punya akun</a></div>
        </form>
    </div></div>
</div></div>
@endsection
