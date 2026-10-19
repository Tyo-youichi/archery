@extends('layouts.app')
@section('title', 'Pengguna')
@section('content')
<div class="card" style="max-width:640px"><div class="card-body">
    <h2 class="h5 mb-3">{{ $user->exists ? 'Edit pengguna' : 'Pengguna baru' }}</h2>
    <form method="post" action="{{ $user->exists ? route('users.update', $user) : route('users.store') }}" class="row g-3">
        @csrf @if($user->exists) @method('PUT') @endif

        <div class="col-md-7"><label class="form-label">Nama</label>
            <input name="name" value="{{ old('name', $user->name) }}" class="form-control" required></div>
        <div class="col-md-5"><label class="form-label">Role</label>
            <select name="role" class="form-select" required>
                @foreach($roles as $code => $label)<option value="{{ $code }}" @selected(old('role', $user->role) === $code)>{{ $label }}</option>@endforeach
            </select></div>

        <div class="col-md-7"><label class="form-label">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required></div>
        <div class="col-md-5"><label class="form-label">Telepon</label>
            <input name="phone" value="{{ old('phone', $user->phone) }}" class="form-control" placeholder="opsional"></div>

        <div class="col-md-6"><label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" autocomplete="new-password" {{ $user->exists ? '' : 'required' }}>
            @if($user->exists)<div class="form-text">Kosongkan bila tidak ingin mengubah.</div>@else<div class="form-text">Minimal 8 karakter.</div>@endif</div>
        <div class="col-md-6"><label class="form-label">Ulangi password</label>
            <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password" {{ $user->exists ? '' : 'required' }}></div>

        <div class="col-12"><button class="btn btn-primary">Simpan</button> <a class="btn btn-link" href="{{ route('users.index') }}">Batal</a></div>
    </form>
</div></div>
@endsection
