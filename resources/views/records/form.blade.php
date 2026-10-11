@extends('layouts.app')
@section('title', $def['label'])
@section('content')
<div class="card"><div class="card-body">
    <h1 class="h4 mb-1">{{ $record ? 'Edit' : 'Tambah' }} {{ $def['label'] }}</h1>
    <p class="text-muted">{{ $athlete->name }} ({{ $athlete->code }})</p>
    <form method="post" class="row g-3"
          action="{{ $record ? route('records.update', [$athlete, $type, $record->id]) : route('records.store', [$athlete, $type]) }}">
        @csrf @if($record) @method('PUT') @endif
        @php $values = $values ?? []; @endphp
        @foreach($def['fields'] as $name => $f)
            @php
                $val = old($name, ($values[$name] ?? null) ?? $record?->{$name} ?? ($name === $def['date'] ? now()->format('Y-m-d') : ($name === 'year' ? now()->year : null)));
                if ($val instanceof \Carbon\CarbonInterface) { $val = $val->format('Y-m-d'); }
            @endphp
            <div class="{{ ($f['type'] ?? '') === 'textarea' ? 'col-12' : 'col-md-4' }}">
                <label class="form-label small">{{ $f['label'] }}</label>
                @if($f['type'] === 'textarea')
                    <textarea name="{{ $name }}" rows="3" class="form-control">{{ $val }}</textarea>
                @elseif($f['type'] === 'select')
                    <select name="{{ $name }}" class="form-select">@foreach(config('archery.'.$f['options']) as $o)<option @selected($val == $o)>{{ $o }}</option>@endforeach</select>
                @else
                    <input type="{{ $f['type'] }}" name="{{ $name }}" value="{{ $val }}" class="form-control" @if(isset($f['step']))step="{{ $f['step'] }}"@endif @if(str_contains($f['rules'], 'required'))required @endif>
                @endif
            </div>
        @endforeach
        <div class="col-12"><button class="btn btn-primary">Simpan</button> <a class="btn btn-link" href="{{ route('athletes.show', $athlete) }}">Batal</a></div>
    </form>
</div></div>
@endsection
