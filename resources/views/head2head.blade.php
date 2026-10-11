@extends('layouts.app')
@section('title', 'Head to Head')
@php
    $pc = fn ($v) => $v === null ? '-' : round($v * 100) . '%';
    $num = fn ($v, $d = 0) => $v === null ? '-' : rtrim(rtrim(number_format((float) $v, $d, ',', '.'), '0'), ',');
@endphp
@section('content')
<div class="h2h">

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 no-print">
        <h1 class="page-title mb-0">Head to Head</h1>
        @if($cmp)
            <button type="button" class="btn btn-success btn-sm" onclick="window.print()">Cetak / Simpan PDF</button>
        @endif
    </div>

    <form class="row g-2 vs-selectbar mb-4 no-print" method="get">
        @foreach(['a' => $a, 'b' => $b] as $key => $sel)
            <div class="col-md-5"><select name="{{ $key }}" class="form-select" required>
                <option value="">Pilih atlet {{ strtoupper($key) }}</option>
                @foreach($athletes as $x)<option value="{{ $x->id }}" @selected($sel && $sel->id == $x->id)>{{ $x->code }} · {{ $x->name }}</option>@endforeach
            </select></div>
        @endforeach
        <div class="col-md-2"><button class="btn btn-primary w-100">Bandingkan</button></div>
    </form>

    @if($cmp)
    @php [$A, $B] = $cmp; $anL = $A['profile']['anthropometry']; $anR = $B['profile']['anthropometry']; @endphp

    {{-- VS banner --}}
    <div class="card"><div class="card-body">
        <div class="vs-names">
            <div class="vs-name vs-name--left">{{ $A['athlete']->name }}</div>
            <div class="vs-badge">VS</div>
            <div class="vs-name vs-name--right">{{ $B['athlete']->name }}</div>
        </div>
        <div class="vs-meta">
            <div><span class="div">{{ $A['athlete']->age ? $A['athlete']->age.' Th' : '' }}</span> · {{ $A['athlete']->division ?? '-' }}</div>
            <div class="right"><span class="div">{{ $B['athlete']->age ? $B['athlete']->age.' Th' : '' }}</span> · {{ $B['athlete']->division ?? '-' }}</div>
        </div>

        {{-- Antropometri --}}
        <div class="vs-band">Antropometri &amp; Komposisi Tubuh</div>
        @php
            $photo = function ($ath) {
                if ($ath->photo_url) {
                    return '<img src="'.e($ath->photo_url).'" class="vs-photo" alt="'.e($ath->name).'">';
                }
                return '<div class="vs-photo vs-photo--ph"><svg width="56" height="56" viewBox="0 0 16 16" fill="currentColor"><path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/></svg></div>';
            };
            $anthroDefs = [
                ['Tinggi Badan (m)',   fn ($x) => $x && $x->height_cm ? number_format($x->height_cm / 100, 2, ',', '.') : '-'],
                ['Berat Badan (kg)',   fn ($x) => $x ? $num($x->weight_kg, 1) : '-'],
                ['Body Mass Index',    fn ($x) => $x ? $num($x->bmi, 1) : '-'],
                ['Kategori BMI',       fn ($x) => $x->bmi_category ?? '-'],
                ['Tinggi Duduk (cm)',  fn ($x) => $x ? $num($x->sitting_height_cm, 1) : '-'],
                ['Panjang Tungkai (cm)', fn ($x) => $x ? $num($x->leg_length_cm, 1) : '-'],
                ['Panjang Lengan (cm)', fn ($x) => $x ? $num($x->arm_length_cm, 1) : '-'],
                ['Rentang Lengan (cm)', fn ($x) => $x ? $num($x->arm_span_cm, 1) : '-'],
                ['Lebar Bahu (cm)',    fn ($x) => $x ? $num($x->shoulder_width_cm, 1) : '-'],
            ];
        @endphp
        <div class="vs-anthro">
            <div class="text-center">{!! $photo($A['athlete']) !!}</div>
            <div class="vs-rows">
                @foreach($anthroDefs as [$label, $f])
                    <div class="vs-row"><div class="l">{{ $f($anL) }}</div><div class="m">{{ $label }}</div><div class="r">{{ $f($anR) }}</div></div>
                @endforeach
            </div>
            <div class="text-center">{!! $photo($B['athlete']) !!}</div>
        </div>

        {{-- Physical & Skill performance --}}
        @foreach([['physical', 'Physical Performance'], ['technique', 'Skill Performance']] as [$dim, $title])
            <div class="vs-band">{{ $title }}</div>
            <div class="vs-perf">
                <div class="vs-big vs-big--left">{{ $pc($A['profile'][$dim]['overall']) }}</div>
                <div class="vs-rows">
                    @foreach(($A['profile'][$dim]['groups'] ?: $B['profile'][$dim]['groups']) as $g => $row)
                        <div class="vs-row">
                            <div class="l">{{ $pc($A['profile'][$dim]['groups'][$g]['pct'] ?? null) }}</div>
                            <div class="m">{{ $row['label'] }}</div>
                            <div class="r">{{ $pc($B['profile'][$dim]['groups'][$g]['pct'] ?? null) }}</div>
                        </div>
                    @endforeach
                </div>
                <div class="vs-big vs-big--right">{{ $pc($B['profile'][$dim]['overall']) }}</div>
            </div>
        @endforeach

        {{-- Score recap --}}
        <div class="vs-band">Score Recap</div>
        <div class="vs-scores">
            @foreach([$A, $B] as $S)
                <div class="score-pair">
                    <div class="score-box score-box--best"><div class="score-box__label">SKOR TERTINGGI</div><div class="score-box__value">{{ $S['best'] ?? '-' }}</div></div>
                    <div class="score-box score-box--low"><div class="score-box__label">SKOR TERENDAH</div><div class="score-box__value">{{ $S['low'] ?? '-' }}</div></div>
                </div>
            @endforeach
        </div>
        <p class="text-muted small text-center mt-3 mb-0">Rata-rata skor: {{ $A['athlete']->name }} {{ $A['avg'] ?? '-' }} · {{ $B['athlete']->name }} {{ $B['avg'] ?? '-' }} (dari {{ $A['count'] }} dan {{ $B['count'] }} catatan)</p>
    </div></div>
    @endif

</div>
@endsection
