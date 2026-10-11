@extends('layouts.app')
@section('title', 'Metrik')
@section('content')
<div class="card"><div class="card-body">
    <h2 class="h5 mb-3">{{ $metric->exists ? 'Edit metrik' : 'Metrik baru' }}</h2>
    <form method="post" action="{{ $metric->exists ? route('metrics.update', $metric) : route('metrics.store') }}" class="row g-3">
        @csrf @if($metric->exists) @method('PUT') @endif

        <div class="col-md-4">
            <label class="form-label">Domain</label>
            @if($metric->exists)
                <input class="form-control" value="{{ $domains[$metric->domain] ?? $metric->domain }}" readonly>
                <div class="form-text">Domain tidak bisa diubah.</div>
            @else
                <select name="domain" class="form-select" required>
                    @foreach($domains as $k => $label)<option value="{{ $k }}" @selected(old('domain', $metric->domain) === $k)>{{ $label }}</option>@endforeach
                </select>
            @endif
        </div>
        <div class="col-md-4">
            <label class="form-label">Key</label>
            <input name="key" value="{{ old('key', $metric->key) }}" class="form-control" {{ $metric->exists ? 'readonly' : 'required' }} placeholder="mis. yoyo_vo2">
            @if($metric->exists)<div class="form-text">Key tidak bisa diubah (kunci data tes).</div>@else<div class="form-text">huruf kecil, angka, underscore.</div>@endif
        </div>
        <div class="col-md-4">
            <label class="form-label">Urutan</label>
            <input type="number" name="sort" value="{{ old('sort', $metric->sort) }}" class="form-control">
        </div>

        <div class="col-md-6"><label class="form-label">Label metrik</label>
            <input name="label" value="{{ old('label', $metric->label) }}" class="form-control" required placeholder="mis. Yo-Yo IR1 (VO2max)"></div>
        <div class="col-md-3"><label class="form-label">Satuan</label>
            <input name="unit" value="{{ old('unit', $metric->unit) }}" class="form-control" placeholder="mis. ml/kg/mnt"></div>
        <div class="col-md-3"><label class="form-label">Step input</label>
            <input name="step" value="{{ old('step', $metric->step) }}" class="form-control" placeholder="0.01"></div>

        <div class="col-md-4"><label class="form-label">Kode grup</label>
            <input name="group_key" value="{{ old('group_key', $metric->group_key) }}" class="form-control" required placeholder="mis. endurance">
            <div class="form-text">huruf kecil, angka, underscore.</div></div>
        <div class="col-md-4"><label class="form-label">Nama grup</label>
            <input name="group_label" value="{{ old('group_label', $metric->group_label) }}" class="form-control" required placeholder="mis. Endurance"></div>

        <div class="col-md-2"><label class="form-label">BM Putra (L)</label>
            <input type="number" step="any" name="bm_l" value="{{ old('bm_l', $metric->bm_l) }}" class="form-control"></div>
        <div class="col-md-2"><label class="form-label">BM Putri (P)</label>
            <input type="number" step="any" name="bm_p" value="{{ old('bm_p', $metric->bm_p) }}" class="form-control">
            <div class="form-text">kosong = samakan L</div></div>

        <div class="col-12">
            <div class="form-check"><input type="checkbox" class="form-check-input" name="is_active" value="1" id="act" @checked(old('is_active', $metric->is_active))><label class="form-check-label" for="act">Aktif (muncul di form & perhitungan)</label></div>
            <div class="form-check"><input type="checkbox" class="form-check-input" name="is_promoted" value="1" id="promo" @checked(old('is_promoted', $metric->is_promoted))><label class="form-check-label" for="promo">Perlu kolom ter-index untuk analitik (★)</label></div>
        </div>

        <div class="col-12"><button class="btn btn-primary">Simpan</button> <a class="btn btn-link" href="{{ route('metrics.index') }}">Batal</a></div>
    </form>
</div></div>
@endsection
