@extends('layouts.app')
@section('title', 'RBAC · Menu')
@section('content')
@include('rbac._tabs')
<div class="card"><div class="card-body">
    <h2 class="h5 mb-3">{{ $menu->exists ? 'Edit menu' : 'Menu baru' }}</h2>
    <form method="post" action="{{ $menu->exists ? route('rbac.menus.update', $menu) : route('rbac.menus.store') }}" class="row g-3">
        @csrf @if($menu->exists) @method('PUT') @endif
        <div class="col-md-5"><label class="form-label">Label</label><input name="label" value="{{ old('label', $menu->label) }}" class="form-control" required></div>
        <div class="col-md-4"><label class="form-label">Route (nama)</label><input name="route" value="{{ old('route', $menu->route) }}" class="form-control" required placeholder="mis. dashboard"></div>
        <div class="col-md-3"><label class="form-label">Urutan</label><input type="number" name="sort" value="{{ old('sort', $menu->sort) }}" class="form-control"></div>
        <div class="col-md-4"><label class="form-label">Parameter</label>
            <select name="param" class="form-select"><option value="">(tanpa)</option><option value="self_athlete" @selected(old('param', $menu->param)=='self_athlete')>self_athlete (atlet sendiri)</option></select></div>
        <div class="col-md-5"><label class="form-label">Role yang boleh melihat</label>
            <div class="d-flex flex-wrap gap-3">
                @foreach($roleOptions as $code => $name)
                    <div class="form-check"><input class="form-check-input" type="checkbox" name="roles[]" value="{{ $code }}" id="m_{{ $code }}" @checked(in_array($code, old('roles', $menu->roles ?? [])))><label class="form-check-label" for="m_{{ $code }}">{{ $name }}</label></div>
                @endforeach
            </div></div>
        <div class="col-md-3 d-flex align-items-end"><div class="form-check"><input type="checkbox" class="form-check-input" name="is_active" value="1" id="act" @checked(old('is_active', $menu->is_active))><label class="form-check-label" for="act">Aktif</label></div></div>
        <div class="col-12"><button class="btn btn-primary">Simpan</button> <a class="btn btn-link" href="{{ route('rbac.menus.index') }}">Batal</a></div>
    </form>
</div></div>
@endsection
