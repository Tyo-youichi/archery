@extends('layouts.app')
@section('title', 'Peralatan')
@section('content')
<div class="card"><div class="card-body">
    <h1 class="h4 mb-1">Peralatan</h1><p class="text-muted">{{ $athlete->name }} ({{ $athlete->code }})</p>
    <form method="post" action="{{ route('equipment.update', $athlete) }}" class="row g-3">@csrf @method('PUT')
        @foreach(config('archery.equipment_fields') as $k => $label)
            <div class="col-md-4"><label class="form-label small">{{ $label }}</label><input name="{{ $k }}" value="{{ old($k, $equipment->{$k} ?? '') }}" class="form-control"></div>
        @endforeach
        <div class="col-12"><button class="btn btn-primary">Simpan</button> <a class="btn btn-link" href="{{ route('athletes.show', $athlete) }}">Batal</a></div>
    </form>
</div></div>
@endsection
