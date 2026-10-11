@extends('layouts.app')
@section('title', 'Registrasi Atlet')
@section('content')
<div class="row justify-content-center"><div class="col-lg-7">
    <div class="card shadow-sm"><div class="card-body">
        <h1 class="h4 mb-3">Registrasi Atlet</h1>
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
            <div class="col-12"><button class="btn btn-primary">Daftar</button> <a href="{{ route('login') }}" class="btn btn-link">Sudah punya akun</a></div>
        </form>
    </div></div>
</div></div>
@endsection
