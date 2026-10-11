@extends('layouts.app')
@section('title', $athlete->name)
@php
    $staff = auth()->user()->isStaff();
    $phys = $profile['physical'];
    $tech = $profile['technique'];
    $fmt = fn ($v, $d = 1) => $v === null ? '-' : rtrim(rtrim(number_format((float) $v, $d, ',', '.'), '0'), ',');
    $add = fn ($type, $label) => $staff ? '<a class="btn btn-sm btn-outline-primary" href="'.route('records.create', [$athlete, $type]).'">'.e($label).'</a>' : '';
    $gcolors = ['#0e6e78', '#f2b01e', '#2f9e6b', '#5a4b9a'];
@endphp
@section('content')

<div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
    <div class="d-flex align-items-center gap-3">
        @include('partials.avatar', ['url' => $athlete->photo_url, 'size' => 72, 'name' => $athlete->name])
        <div>
            <h1 class="h3 mb-1">{{ $athlete->name }}</h1>
            <span class="badge text-bg-dark">{{ $athlete->code }}</span>
            <span class="badge text-bg-secondary">{{ $athlete->division ?? 'Divisi -' }}</span>
            <span class="badge text-bg-info">{{ config('archery.registration_types')[$athlete->registration_type] ?? '' }}</span>
        </div>
    </div>
    <div class="d-flex gap-2">
        <a class="btn btn-success btn-sm" href="{{ route('shoot-log.edit', $athlete) }}">Buku Saku (100 arrow)</a>
        @if($staff)
            <a class="btn btn-outline-secondary btn-sm" href="{{ route('athletes.edit', $athlete) }}">Edit biodata</a>
            @if(auth()->user()->hasRole('admin'))
                <form method="post" action="{{ route('athletes.destroy', $athlete) }}" onsubmit="return confirm('Hapus atlet beserta seluruh datanya?')">@csrf @method('DELETE')<button class="btn btn-outline-danger btn-sm">Hapus</button></form>
            @endif
        @endif
    </div>
</div>

<ul class="nav nav-tabs mb-3" role="tablist">
    @php $tabs = ['ringkasan' => 'Ringkasan', 'antro' => 'Antropometri', 'fisik' => 'Performa Fisik', 'teknik' => 'Performa Teknik', 'beban' => 'Beban', 'skor' => 'Skor Bulanan', 'alat' => 'Peralatan']; @endphp
    @foreach($tabs as $id => $label)
        <li class="nav-item"><button class="nav-link @if($loop->first) active @endif" data-bs-toggle="tab" data-bs-target="#tab-{{ $id }}" type="button">{{ $label }}</button></li>
    @endforeach
</ul>

<div class="tab-content">

    {{-- ================= Ringkasan / Data Visual ================= --}}
    <div class="tab-pane fade show active" id="tab-ringkasan">
        <div class="row g-3">
            <div class="col-lg-5"><div class="card h-100"><div class="card-body">
                <div class="card-title-sm mb-2">Biodata</div>
                <table class="table table-sm table-borderless mb-0">
                    <tr><th style="width:40%">Tempat, tgl lahir</th><td>{{ $athlete->birth_place ?? '-' }}{{ $athlete->birth_date ? ', '.$athlete->birth_date->translatedFormat('d F Y') : '' }}</td></tr>
                    <tr><th>Usia</th><td>{{ $athlete->age ?? '-' }}</td></tr>
                    <tr><th>Jenis kelamin</th><td>{{ $athlete->gender_label }}</td></tr>
                    <tr><th>Asal daerah</th><td>{{ $athlete->origin ?? '-' }}</td></tr>
                </table>
                <div class="d-flex justify-content-around mt-3">
                    @include('partials.donut', ['pct' => $phys['overall'], 'color' => '#0e6e78', 'label' => 'Profil Fisik', 'size' => 140])
                    @include('partials.donut', ['pct' => $tech['overall'], 'color' => '#5a4b9a', 'label' => 'Profil Teknik', 'size' => 140])
                </div>
            </div></div></div>
            <div class="col-lg-7"><div class="card h-100"><div class="card-body">
                <div class="card-title-sm mb-2">Profil capaian (% terhadap benchmark)</div>
                <canvas id="radar" height="150"></canvas>
            </div></div></div>
        </div>
        <div class="card mt-3"><div class="card-body">
            <div class="card-title-sm mb-2">Score Recap</div>
            <div class="vs-scores" style="max-width:520px;margin:auto">
                <div class="score-box score-box--best"><div class="score-box__label">SKOR TERBAIK</div><div class="score-box__value">{{ $charts['bestScore'] ?? '-' }}</div></div>
                <div class="score-box score-box--low"><div class="score-box__label">SKOR TERENDAH</div><div class="score-box__value">{{ $charts['lowScore'] ?? '-' }}</div></div>
            </div>
        </div></div>
    </div>

    {{-- ================= Antropometri ================= --}}
    <div class="tab-pane fade" id="tab-antro">
        <div class="card"><div class="card-body">
            <div class="d-flex justify-content-between mb-2"><h2 class="h5 mb-0">Antropometri &amp; Komposisi Tubuh</h2>{!! $add('anthropometry', '+ Pengukuran') !!}</div>
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
            @php $hr = $anthros->first(fn ($a) => $a->hr_rest || $a->hr_max || $a->hr_training); @endphp
            @if($hr && ($hr->hr_rest || $hr->hr_max || $hr->hr_training))
                <div class="card-title-sm mt-4 mb-3">Monitoring Denyut Nadi <small class="text-muted fw-normal">(acuan % = nadi maksimum · {{ $hr->measured_at->format('d/m/Y') }})</small></div>
                <div class="hr-grid">
                    @include('partials.hr-gauge', ['value' => $hr->hr_rest, 'max' => $hr->hr_max, 'color' => '#2f9e6b', 'label' => 'Denyut Nadi Istirahat'])
                    @include('partials.hr-gauge', ['value' => $hr->hr_training, 'max' => $hr->hr_max, 'color' => '#e08a1e', 'label' => 'Denyut Nadi Latihan'])
                    @include('partials.hr-gauge', ['value' => $hr->hr_max, 'max' => $hr->hr_max, 'color' => '#d93a3a', 'label' => 'Denyut Nadi Maksimum'])
                </div>
            @endif
            @if(count($charts['weight']['data']) > 1)
                <div class="card-title-sm mt-4 mb-2">Grafik berat badan</div>
                <canvas id="weight" height="80"></canvas>
            @endif
        </div></div>
    </div>

    {{-- ================= Performa Fisik & Teknik ================= --}}
    @foreach([['fisik', 'physical', 'Tes Performa Fisik', $phys, $physicalHistory], ['teknik', 'technique', 'Data Performa Teknik', $tech, $techniqueHistory]] as [$tab, $type, $title, $ev, $hist])
    <div class="tab-pane fade" id="tab-{{ $tab }}">
        <div class="card"><div class="card-body">
            <div class="d-flex justify-content-between mb-3"><h2 class="h5 mb-0">{{ $title }}
                @if($ev['record'])<small class="text-muted fs-6">· tes terakhir {{ $ev['record']->tested_at->format('d/m/Y') }}</small>@endif</h2>{!! $add($type, '+ Hasil tes') !!}</div>
            @if($ev['record'])
                {{-- Donut: keseluruhan + per grup --}}
                <div class="viz-grid mb-4">
                    @include('partials.donut', ['pct' => $ev['overall'], 'color' => ($type === 'physical' ? '#0e6e78' : '#5a4b9a'), 'label' => 'Keseluruhan', 'size' => 150])
                    @foreach($ev['groups'] as $g)
                        @include('partials.donut', ['pct' => $g['pct'], 'color' => $gcolors[$loop->index % count($gcolors)], 'label' => $g['label'], 'size' => 130])
                    @endforeach
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-lg-6"><div class="card-title-sm mb-2">Prosentase hasil tes (benchmark vs hasil)</div><canvas id="{{ $type }}Radar" height="240"></canvas></div>
                    <div class="col-lg-6"><div class="card-title-sm mb-2">Capaian hasil tes (hasil vs benchmark)</div><canvas id="{{ $type }}Bar" height="240"></canvas></div>
                </div>
                <div class="table-responsive"><table class="table table-compact align-middle">
                    <thead><tr><th>Item</th><th class="text-end">Hasil</th><th class="text-end">BM</th><th style="width:28%">Capaian</th><th class="text-end">Kekurangan</th></tr></thead>
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
    </div>
    @endforeach

    {{-- ================= Beban ================= --}}
    <div class="tab-pane fade" id="tab-beban">
        <div class="card"><div class="card-body">
            <div class="d-flex justify-content-between mb-2"><h2 class="h5 mb-0">Data Beban (1RM terbaru per latihan)</h2>{!! $add('load', '+ Data beban') !!}</div>
            @if($loads->count())<canvas id="loadBar" height="80" class="mb-3"></canvas>@endif
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
    </div>

    {{-- ================= Skor bulanan ================= --}}
    <div class="tab-pane fade" id="tab-skor">
        <div class="card"><div class="card-body">
            <div class="d-flex justify-content-between mb-2"><h2 class="h5 mb-0">Catatan Skor Bulanan</h2>{!! $add('score', '+ Skor') !!}</div>
            @if(count($charts['scores']['data']) > 0)<canvas id="scores" height="80" class="mb-3"></canvas>@endif
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
    </div>

    {{-- ================= Peralatan ================= --}}
    <div class="tab-pane fade" id="tab-alat">
        <div class="card"><div class="card-body">
            <div class="d-flex justify-content-between mb-2"><h2 class="h5 mb-0">Peralatan</h2>@if($staff)<a class="btn btn-sm btn-outline-primary" href="{{ route('equipment.edit', $athlete) }}">Edit peralatan</a>@endif</div>
            <div class="row g-2">
                @foreach(config('archery.equipment_fields') as $k => $label)
                    <div class="col-6 col-md-3"><div class="small text-muted">{{ $label }}</div><div>{{ $athlete->equipment->{$k} ?? '-' }}</div></div>
                @endforeach
            </div>
        </div></div>
    </div>

</div>
@endsection

@push('scripts')
<script>
(function () {
    const C = @json($charts);
    const teal = '#0e6e78', gold = '#f2b01e', violet = '#5a4b9a';
    const made = [];

    // Donut gauge
    document.querySelectorAll('.viz-donut').forEach(el => {
        const p = +el.dataset.pct, col = el.dataset.color || teal;
        made.push(new Chart(el, {
            type: 'doughnut',
            data: { datasets: [{ data: [p, Math.max(0, 100 - p)], backgroundColor: [col, '#e9edef'], borderWidth: 0 }] },
            options: { cutout: '70%', plugins: { legend: { display: false }, tooltip: { enabled: false } }, animation: false }
        }));
    });

    // Radar gabungan (ringkasan)
    const rc = document.getElementById('radar');
    if (rc) made.push(new Chart(rc, {
        type: 'radar',
        data: { labels: C.radar.labels, datasets: [{ label: '% capaian', data: C.radar.data, borderColor: teal, backgroundColor: 'rgba(14,110,120,.18)', pointBackgroundColor: teal }] },
        options: { scales: { r: { min: 0, max: 100, ticks: { stepSize: 20 } } }, plugins: { legend: { display: false } } }
    }));

    // Radar & bar per dimensi
    [['physical', teal], ['technique', violet]].forEach(([dim, col]) => {
        const d = C[dim]; if (!d) return;
        const rad = document.getElementById(dim + 'Radar');
        if (rad) made.push(new Chart(rad, {
            type: 'radar',
            data: { labels: d.metricLabels, datasets: [
                { label: 'Hasil', data: d.metricPct, borderColor: col, backgroundColor: col + '2e', pointBackgroundColor: col },
                { label: 'Benchmark', data: d.metricLabels.map(() => 100), borderColor: gold, backgroundColor: 'rgba(0,0,0,0)', pointBackgroundColor: gold }
            ]},
            options: { scales: { r: { min: 0, max: 100, ticks: { stepSize: 20 } } }, plugins: { legend: { position: 'bottom' } } }
        }));
        const bar = document.getElementById(dim + 'Bar');
        if (bar) made.push(new Chart(bar, {
            type: 'bar',
            data: { labels: d.metricLabels, datasets: [
                { label: 'Hasil', data: d.metricResult, backgroundColor: col },
                { label: 'Benchmark', data: d.metricBm, backgroundColor: gold }
            ]},
            options: { indexAxis: 'y', plugins: { legend: { position: 'bottom' } }, scales: { x: { beginAtZero: true } } }
        }));
    });

    // Bar 1RM
    const lb = document.getElementById('loadBar');
    if (lb && C.load.labels.length) made.push(new Chart(lb, {
        type: 'bar',
        data: { labels: C.load.labels, datasets: [{ label: '1RM (kg)', data: C.load.data, backgroundColor: teal }] },
        options: { indexAxis: 'y', plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true } } }
    }));

    // Skor (garis)
    const sc = document.getElementById('scores');
    if (sc) made.push(new Chart(sc, {
        type: 'line',
        data: { labels: C.scores.labels, datasets: [{ label: 'Skor', data: C.scores.data, borderColor: teal, backgroundColor: 'rgba(14,110,120,.12)', tension: .25, spanGaps: true, fill: true }] },
        options: { plugins: { legend: { display: false } } }
    }));

    // Berat badan (garis)
    const wt = document.getElementById('weight');
    if (wt) made.push(new Chart(wt, {
        type: 'line',
        data: { labels: C.weight.labels, datasets: [{ label: 'Berat badan (kg)', data: C.weight.data, borderColor: violet, tension: .25, spanGaps: true }] },
        options: { plugins: { legend: { display: false } } }
    }));

    // Perbaiki ukuran grafik saat tab dibuka (canvas di tab tersembunyi awalnya 0)
    document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(t =>
        t.addEventListener('shown.bs.tab', () => made.forEach(c => c.resize())));
})();
</script>
@endpush
