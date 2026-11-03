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
            @php $ftype = $f['type'] ?? 'text'; @endphp
            @if($ftype === 'metric')
                {{-- Satu blok per metrik: nilai (atau percobaan) + override BM menyatu.
                     Pasangan kanan/kiri dipaksa satu baris agar sejajar. --}}
                @if(($f['side'] ?? null) === 'right')<div class="w-100"></div>@endif
                <div class="col-md-6">
                    <label class="form-label small mb-1">{{ $f['label'] }}</label>
                    @if(($f['attempts'] ?? 0) > 0)
                        <div class="d-flex flex-wrap gap-1">
                            @for($i = 1; $i <= $f['attempts']; $i++)
                                @php $av = old($name . '_a' . $i, $values[$name . '_a' . $i] ?? null); @endphp
                                <div style="width:58px">
                                    <input type="number" step="{{ $f['step'] }}" min="0"
                                           name="{{ $name }}_a{{ $i }}" value="{{ $av }}"
                                           class="form-control form-control-sm text-center"
                                           placeholder="{{ $i }}" aria-label="{{ $f['label'] }} percobaan {{ $i }}">
                                </div>
                            @endfor
                        </div>
                        <div class="form-text mt-0">7 percobaan — nilai dipakai = tertinggi</div>
                    @else
                        @php $mv = old($name, $values[$name] ?? null); @endphp
                        <input type="number" step="{{ $f['step'] }}" min="0" name="{{ $name }}" value="{{ $mv }}" class="form-control">
                    @endif
                    @php $bmv = old($name . '_bm', $values[$name . '_bm'] ?? null); @endphp
                    <input type="number" step="{{ $f['step'] }}" min="0" name="{{ $name }}_bm" value="{{ $bmv }}"
                           class="form-control form-control-sm mt-1" placeholder="BM {{ $f['label'] }} (kosongkan = default)">
                </div>
                @if(($f['side'] ?? null) === 'left')<div class="w-100"></div>@endif
            @else
                @php
                    $val = old($name, ($values[$name] ?? null) ?? $record?->{$name} ?? ($name === $def['date'] ? now()->format('Y-m-d') : ($name === 'year' ? now()->year : null)));
                    if ($val instanceof \Carbon\CarbonInterface) { $val = $val->format('Y-m-d'); }
                @endphp
                <div class="{{ $ftype === 'textarea' ? 'col-12' : 'col-md-4' }}">
                    <label class="form-label small">{{ $f['label'] }}</label>
                    @if($ftype === 'textarea')
                        <textarea name="{{ $name }}" rows="3" class="form-control">{{ $val }}</textarea>
                    @elseif($ftype === 'select')
                        <select name="{{ $name }}" class="form-select">@foreach(config('archery.'.$f['options']) as $o)<option @selected($val == $o)>{{ $o }}</option>@endforeach</select>
                    @else
                        <input type="{{ $ftype }}" name="{{ $name }}" value="{{ $val }}" class="form-control" @if(isset($f['step']))step="{{ $f['step'] }}"@endif @if(isset($f['rules']) && str_contains($f['rules'], 'required'))required @endif>
                    @endif
                </div>
            @endif
        @endforeach
        <div class="col-12"><button class="btn btn-primary">Simpan</button> <a class="btn btn-link" href="{{ route('athletes.show', $athlete) }}">Batal</a></div>
    </form>
</div></div>
@endsection
