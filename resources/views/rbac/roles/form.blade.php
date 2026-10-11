@extends('layouts.app')
@section('title', 'RBAC · Role')
@section('content')
@include('rbac._tabs')
<div class="card"><div class="card-body">
    <h2 class="h5 mb-3">{{ $role->exists ? 'Edit role' : 'Role baru' }}</h2>
    <form method="post" action="{{ $role->exists ? route('rbac.roles.update', $role) : route('rbac.roles.store') }}" class="row g-3">
        @csrf @if($role->exists) @method('PUT') @endif
        <div class="col-md-4"><label class="form-label">Kode</label>
            <input name="code" value="{{ old('code', $role->code) }}" class="form-control" {{ $role->exists ? 'readonly' : 'required' }} placeholder="mis. supervisor">
            @if($role->exists)<div class="form-text">Kode tidak bisa diubah (dipakai di data user).</div>@else<div class="form-text">huruf kecil, angka, underscore.</div>@endif</div>
        <div class="col-md-5"><label class="form-label">Nama</label><input name="name" value="{{ old('name', $role->name) }}" class="form-control" required></div>
        <div class="col-md-3"><label class="form-label">Urutan</label><input type="number" name="sort" value="{{ old('sort', $role->sort) }}" class="form-control"></div>
        <div class="col-12"><div class="form-check"><input type="checkbox" class="form-check-input" name="is_staff" value="1" id="st" @checked(old('is_staff', $role->is_staff))><label class="form-check-label" for="st">Staf (akses manajemen data)</label></div></div>
        <div class="col-12"><button class="btn btn-primary">Simpan</button> <a class="btn btn-link" href="{{ route('rbac.roles.index') }}">Batal</a></div>
    </form>
</div></div>
@endsection
