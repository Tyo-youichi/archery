@extends('layouts.app')
@section('title', 'RBAC · Izin Route')
@section('content')
@include('rbac._tabs')
<div class="card"><div class="card-body">
    <h2 class="h5 mb-3">{{ $permission->exists ? 'Edit izin route' : 'Izin route baru' }}</h2>
    <form method="post" action="{{ $permission->exists ? route('rbac.permissions.update', $permission) : route('rbac.permissions.store') }}" class="row g-3">
        @csrf @if($permission->exists) @method('PUT') @endif
        <div class="col-md-8"><label class="form-label">Pola route</label>
            <input name="pattern" value="{{ old('pattern', $permission->pattern) }}" class="form-control" list="routeNames" required placeholder="mis. athletes.* atau h2h">
            <datalist id="routeNames">@foreach($routeNames ?? [] as $n)<option value="{{ $n }}">@endforeach</datalist>
            <div class="form-text">Nama route Laravel; boleh wildcard <code>*</code>.</div></div>
        <div class="col-md-4"><label class="form-label">Urutan</label><input type="number" name="sort" value="{{ old('sort', $permission->sort) }}" class="form-control"></div>
        <div class="col-12"><label class="form-label">Role yang boleh</label>
            <div class="d-flex flex-wrap gap-3">
                @foreach($roleOptions as $code => $name)
                    <div class="form-check"><input class="form-check-input" type="checkbox" name="roles[]" value="{{ $code }}" id="r_{{ $code }}" @checked(in_array($code, old('roles', $permission->roles ?? [])))><label class="form-check-label" for="r_{{ $code }}">{{ $name }} <code>{{ $code }}</code></label></div>
                @endforeach
            </div></div>
        <div class="col-12"><label class="form-label">Keterangan</label><input name="description" value="{{ old('description', $permission->description) }}" class="form-control"></div>
        <div class="col-12"><button class="btn btn-primary">Simpan</button> <a class="btn btn-link" href="{{ route('rbac.permissions.index') }}">Batal</a></div>
    </form>
</div></div>
@endsection
