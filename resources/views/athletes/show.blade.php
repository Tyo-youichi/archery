@extends('layouts.app')
@section('title', $athlete->name)
@php
    $staff = auth()->user()->isStaff();
    $phys = $profile['physical'];
    $tech = $profile['technique'];
    $an = $profile['anthropometry'];
    $fmt = fn ($v, $d = 1) => $v === null ? '-' : rtrim(rtrim(number_format((float) $v, $d, ',', '.'), '0'), ',');
    $add = fn ($type, $label) => $staff ? '<a class="btn btn-sm btn-outline-primary" href="'.route('records.create', [$athlete, $type]).'">'.e($label).'</a>' : '';
@endphp
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
    <div>
        <h1 class="h3 mb-1">{{ $athlete->name }}</h1>
        <span class="badge text-bg-dark">{{ $athlete->code }}</span>
        <span class="badge text-bg-secondary">{{ $athlete->division ?? 'Divisi -' }}</span>
        <span class="badge text-bg-info">{{ config('archery.registration_types')[$athlete->registration_type] ?? '' }}</span>
    </div>
    <div class="d-flex gap-2">
        <a class="btn btn-success btn-sm" href="{{ route('shoot-log.edit', $athlete) }}">📒 Buku Saku (100 arrow)</a>
        @if($staff)
            <a class="btn btn-outline-secondary btn-sm" href="{{ route('athletes.edit', $athlete) }}">Edit biodata</a>
            <form method="post" action="{{ route('athletes.destroy', $athlete) }}" onsubmit="return confirm('Hapus atlet beserta seluruh datanya?')">@csrf @method('DELETE')<button class="btn btn-outline-danger btn-sm">Hapus</button></form>
        @endif
    </div>
</div>

{{-- a. Biodata + h. Data visual --}}
<div class="row g-3 mb-3">
    <div class="col-lg-5"><div class="card h-100"><div class="card-body">
        <div class="card-title-sm mb-2">Biodata</div>
        <table class="table table-sm table-borderless mb-0">
            <tr><th style="width:38%">Tempat, tgl lahir</th><td>{{ $athlete->birth_place ?? '-' }}{{ $athlete->birth_date ? ', '.$athlete->birth_date->translatedFormat('d F Y') : '' }}</td></tr>
            <tr><th>Usia</th><td>{{ $athlete->age ?? '-' }}</td></tr>
            <tr><th>Jenis kelamin</th><td>{{ $athlete->gender_label }}</td></tr>
            <tr><th>Asal daerah</th><td>{{ $athlete->origin ?? '-' }}</td></tr>
            <tr><th>Profil fisik</th><td>@include('partials.pct', ['pct' => $phys['overall']])</td></tr>
            <tr><th>Profil teknik</th><td>@include('partials.pct', ['pct' => $tech['overall']])</td></tr>
        </table>
    </div></div></div>
    <div class="col-lg-7"><div class="card h-100"><div class="card-body">
        <div class="card-title-sm mb-2">Profil capaian (% terhadap benchmark)</div>
        <canvas id="radar" height="150"></canvas>
    </div></div></div>
</div>

{{-- b. Antropometri --}}
<div class="card mb-3"><div class="card-body">
    <div class="d-flex justify-content-between mb-2"><h2 class="h5 mb-0">Antropometri & Komposisi Tubuh</h2>{!! $add('anthropometry', '+ Pengukuran') !!}</div>
    <div class="table-responsive"><table class="table table-compact mb-0">
        <thead><tr><th>Tanggal</th><th>TB</th><th>T. duduk</th><th>BB</th><th>Tungkai</th><th>Lengan</th><th>Rentang</th><th>Bahu</th><th>IMT</th><th>Nadi (R/M/L)</th>@if($staff)<th></th>@endif</tr></thead>
        <tbody>
        @forelse($anthros as $a)
            <tr><td>{{ $a->measured_at->format('d/m/Y') }}</td><td>{{ $fmt($a->height_cm) }}</td><td>{{ $fmt($a->sitting_height_cm) }}</td><td>{{ $fmt($a->weight_kg) }}</td>
                <td>{{ $fmt($a->leg_length_cm) }}</td><td>{{ $fmt($a->arm_length_cm) }}</td><td>{{ $fmt($a->arm_span_cm) }}</td><td>{{ $fmt($a->shoulder_width_cm) }}</td>
                <td>{{ $fmt($a->bmi, 2) }} <small class="text-muted">{{ $a->bmi_category }}</small></td>
                <td>{{ $a->hr_rest ?? '-' }}/{{ $a->hr_max ?? '-' }}/{{ $a->hr_training ?? '-' }}</td>
                @if($staff)<td class="text-end text-nowrap">@include('records.actions', ['type' => 'anthropometry', 'id' => $a->id])</td>@endif</tr>
        @empty<tr><td colspan="11" class="text-muted">Belum ada data.</td></tr>@endforelse
        </tbody>
    </table></div>
    @if(count($charts['weight']['data']) > 1)<canvas id="weight" height="70" class="mt-3"></canvas>@endif
</div></div>

{{-- c. Fisik, e. Teknik --}}
@foreach([['physical', 'Tes Performa Fisik', $phys, $physicalHistory], ['technique', 'Data Performa Teknik', $tech, $techniqueHistory]] as [$type, $title, $ev, $hist])
<div class="card mb-3"><div class="card-body">
    <div class="d-flex justify-content-between mb-2"><h2 class="h5 mb-0">{{ $title }}
        @if($ev['record'])<small class="text-muted fs-6">· tes terakhir {{ $ev['record']->tested_at->format('d/m/Y') }}</small>@endif</h2>{!! $add($type, '+ Hasil tes') !!}</div>
    @if($ev['record'])
    <div class="table-responsive"><table class="table table-compact align-middle">
        <thead><tr><th>Item</th><th class="text-end">Hasil</th><th class="text-end">BM</th><th style="width:30%">Capaian</th><th class="text-end">Kekurangan</th></tr></thead>
        <tbody>
        @foreach($ev['metrics'] as $m)
            <tr><td>{{ $m['label'] }}</td><td class="text-end">{{ $fmt($m['result'], 2) }}</td><td class="text-end">{{ $fmt($m['bm'], 2) }}</td>
                <td>@include('partials.pct', ['pct' => $m['pct']])</td><td class="text-end">{{ $m['gap'] === null ? '-' : round($m['gap']*100, 1).'%' }}</td></tr>
        @endforeach
        </tbody>
        <tfoot class="table-light">
        @foreach($ev['groups'] as $g)
            <tr><th colspan="3">{{ $g['label'] }}</th><td>@include('partials.pct', ['pct' => $g['pct']])</td><td class="text-end">{{ $g['gap'] === null ? '-' : round($g['gap']*100, 1).'%' }}</td></tr>
        @endforeach
            <tr><th colspan="3">Profil keseluruhan</th><td>@include('partials.pct', ['pct' => $ev['overall']])</td><td></td></tr>
        </tfoot>
    </table></div>
    @if($staff)
        <details><summary class="small text-muted">Riwayat tes ({{ $hist->count() }} terakhir)</summary>
            <ul class="small mt-2">@foreach($hist as $h)<li>{{ $h->tested_at->format('d/m/Y') }} {{ $h->venue ? '· '.$h->venue : '' }} @include('records.actions', ['type' => $type, 'id' => $h->id])</li>@endforeach</ul></details>
    @endif
    @else<p class="text-muted mb-0">Belum ada data.</p>@endif
</div></div>
@endforeach

{{-- d. Data beban --}}
<div class="card mb-3"><div class="card-body">
    <div class="d-flex justify-content-between mb-2"><h2 class="h5 mb-0">Data Beban (1RM terbaru per latihan)</h2>{!! $add('load', '+ Data beban') !!}</div>
    <div class="table-responsive"><table class="table table-compact mb-0">
        <thead><tr><th>Latihan</th><th>Tanggal</th><th class="text-end">Beban (kg)</th><th class="text-end">Reps</th><th class="text-end">1RM (kg)</th>@if($staff)<th></th>@endif</tr></thead>
        <tbody>
        @forelse($loads as $l)
            <tr><td>{{ $l->exercise }}</td><td>{{ $l->tested_at->format('d/m/Y') }}</td><td class="text-end">{{ $fmt($l->weight_kg) }}</td><td class="text-end">{{ $l->reps }}</td><td class="text-end">{{ $fmt($l->one_rm, 2) }}</td>
                @if($staff)<td class="text-end">@include('records.actions', ['type' => 'load', 'id' => $l->id])</td>@endif</tr>
        @empty<tr><td colspan="6" class="text-muted">Belum ada data.</td></tr>@endforelse
        </tbody>
    </table></div>
</div></div>

{{-- f. Skor bulanan --}}
<div class="card mb-3"><div class="card-body">
    <div class="d-flex justify-content-between mb-2"><h2 class="h5 mb-0">Catatan Skor Bulanan</h2>{!! $add('score', '+ Skor') !!}</div>
    @if(count($charts['scores']['data']) > 0)<canvas id="scores" height="70" class="mb-3"></canvas>@endif
    <div class="table-responsive"><table class="table table-compact mb-0">
        <thead><tr><th>Periode</th><th class="text-end">Skor</th><th>Tempat</th><th>Analisis</th>@if($staff)<th></th>@endif</tr></thead>
        <tbody>
        @forelse($scores as $s)
            <tr><td>{{ $s->month_name }} {{ $s->year }}</td><td class="text-end">{{ $s->score ?? ($s->score_note ?: '-') }}</td><td>{{ $s->venue ?? '-' }}</td><td>{{ $s->analysis }}</td>
                @if($staff)<td class="text-end">@include('records.actions', ['type' => 'score', 'id' => $s->id])</td>@endif</tr>
        @empty<tr><td colspan="5" class="text-muted">Belum ada data.</td></tr>@endforelse
        </tbody>
    </table></div>
</div></div>

{{-- g. Peralatan --}}
<div class="card mb-3"><div class="card-body">
    <div class="d-flex justify-content-between mb-2"><h2 class="h5 mb-0">Peralatan</h2>@if($staff)<a class="btn btn-sm btn-outline-primary" href="{{ route('equipment.edit', $athlete) }}">Edit peralatan</a>@endif</div>
    <div class="row g-2">
        @foreach(config('archery.equipment_fields') as $k => $label)
            <div class="col-6 col-md-3"><div class="small text-muted">{{ $label }}</div><div>{{ $athlete->equipment->{$k} ?? '-' }}</div></div>
        @endforeach
    </div>
</div></div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
const charts = @json($charts);
new Chart(document.getElementById('radar'), {
    type: 'radar',
    data: { labels: charts.radar.labels, datasets: [{ label: '% capaian', data: charts.radar.data, fill: true }] },
    options: { scales: { r: { min: 0, max: 100, ticks: { stepSize: 20 } } }, plugins: { legend: { display: false } } }
});
const sc = document.getElementById('scores');
if (sc) new Chart(sc, { type: 'line', data: { labels: charts.scores.labels, datasets: [{ label: 'Skor', data: charts.scores.data, tension: .25, spanGaps: true }] }, options: { plugins: { legend: { display: false } } } });
const wt = document.getElementById('weight');
if (wt) new Chart(wt, { type: 'line', data: { labels: charts.weight.labels, datasets: [{ label: 'Berat badan (kg)', data: charts.weight.data, tension: .25, spanGaps: true }] } });
</script>
@endpush
