@extends('layouts.app')
@section('title', 'Head to Head')
@php
    $fmt = fn ($v, $d = 1) => $v === null ? '-' : rtrim(rtrim(number_format((float) $v, $d, ',', '.'), '0'), ',');
    $pc = fn ($v) => $v === null ? '-' : round($v * 100, 1) . '%';
@endphp
@section('content')
<h1 class="h4 mb-3">Dashboard Head to Head</h1>
<form class="row g-2 mb-4" method="get">
    @foreach(['a' => $a, 'b' => $b] as $k => $sel)
        <div class="col-md-4"><select name="{{ $k }}" class="form-select" required><option value="">Pilih atlet {{ strtoupper($k) }}</option>
            @foreach($athletes as $x)<option value="{{ $x->id }}" @selected($sel && $sel->id == $x->id)>{{ $x->code }} · {{ $x->name }}</option>@endforeach</select></div>
    @endforeach
    <div class="col-md-2"><button class="btn btn-primary w-100">Bandingkan</button></div>
</form>

@if($cmp)
@php [$A, $B] = $cmp; @endphp
<div class="row g-3 mb-3">
    <div class="col-lg-6"><div class="card h-100"><div class="card-body"><canvas id="radar"></canvas></div></div></div>
    <div class="col-lg-6"><div class="card h-100"><div class="card-body">
        <table class="table table-compact mb-0">
            <thead><tr><th></th><th>{{ $A['athlete']->name }}</th><th>{{ $B['athlete']->name }}</th></tr></thead>
            <tbody>
                <tr><th>Usia</th><td>{{ $A['athlete']->age ?? '-' }}</td><td>{{ $B['athlete']->age ?? '-' }}</td></tr>
                <tr><th>Divisi</th><td>{{ $A['athlete']->division ?? '-' }}</td><td>{{ $B['athlete']->division ?? '-' }}</td></tr>
                <tr><th>Profil fisik</th><td>{{ $pc($A['profile']['physical']['overall']) }}</td><td>{{ $pc($B['profile']['physical']['overall']) }}</td></tr>
                <tr><th>Profil teknik</th><td>{{ $pc($A['profile']['technique']['overall']) }}</td><td>{{ $pc($B['profile']['technique']['overall']) }}</td></tr>
                <tr><th>Skor terbaik</th><td>{{ $A['best'] ?? '-' }}</td><td>{{ $B['best'] ?? '-' }}</td></tr>
                <tr><th>Rata-rata skor</th><td>{{ $A['avg'] ?? '-' }}</td><td>{{ $B['avg'] ?? '-' }}</td></tr>
                <tr><th>Jumlah catatan skor</th><td>{{ $A['count'] }}</td><td>{{ $B['count'] }}</td></tr>
            </tbody>
        </table>
    </div></div></div>
</div>

<div class="row g-3">
    @foreach(['anthropometry' => 'Antropometri'] as $_ => $title)
    <div class="col-lg-6"><div class="card"><div class="card-body">
        <h2 class="h6">{{ $title }} (pengukuran terakhir)</h2>
        <table class="table table-compact mb-0"><tbody>
            @foreach(['height_cm' => 'Tinggi badan (cm)', 'weight_kg' => 'Berat badan (kg)', 'arm_span_cm' => 'Rentang lengan (cm)', 'shoulder_width_cm' => 'Lebar bahu (cm)', 'bmi' => 'IMT'] as $f => $l)
                <tr><th>{{ $l }}</th><td>{{ $fmt($A['profile']['anthropometry']?->{$f}, 2) }}</td><td>{{ $fmt($B['profile']['anthropometry']?->{$f}, 2) }}</td></tr>
            @endforeach
        </tbody></table>
    </div></div></div>
    @endforeach
    @foreach(['physical' => 'Performa Fisik', 'technique' => 'Performa Teknik (Skill)'] as $t => $title)
    <div class="col-lg-6"><div class="card"><div class="card-body">
        <h2 class="h6">{{ $title }} (% capaian)</h2>
        <table class="table table-compact mb-0"><tbody>
            @foreach($A['profile'][$t]['metrics'] ?: $B['profile'][$t]['metrics'] as $key => $m)
                <tr><th>{{ $m['label'] }}</th><td>{{ $pc($A['profile'][$t]['metrics'][$key]['pct'] ?? null) }}</td><td>{{ $pc($B['profile'][$t]['metrics'][$key]['pct'] ?? null) }}</td></tr>
            @endforeach
        </tbody></table>
    </div></div></div>
    @endforeach
</div>
@endif

@if($cmp)
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
@php
    $labels = []; $series = [[], []];
    foreach (['physical', 'technique'] as $t) {
        foreach ($cmp[0]['profile'][$t]['groups'] ?: $cmp[1]['profile'][$t]['groups'] as $g => $row) {
            $labels[] = $row['label'];
            foreach ([0, 1] as $i) { $v = $cmp[$i]['profile'][$t]['groups'][$g]['pct'] ?? null; $series[$i][] = $v === null ? 0 : round($v * 100, 1); }
        }
    }
@endphp
new Chart(document.getElementById('radar'), {
    type: 'radar',
    data: { labels: @json($labels), datasets: [
        { label: @json($cmp[0]['athlete']->name), data: @json($series[0]), fill: true },
        { label: @json($cmp[1]['athlete']->name), data: @json($series[1]), fill: true }
    ]},
    options: { scales: { r: { min: 0, max: 100, ticks: { stepSize: 20 } } } }
});
</script>
@endpush
@endif
@endsection
