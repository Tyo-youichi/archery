@php
    // Persentase relatif ke nadi maksimum (acuan 100%, seperti dashboard Excel).
    $hrMax = $max ?? null;
    $pct = ($hrMax && $value) ? min(100, (int) round($value / $hrMax * 100)) : 0;
    $color = $color ?? '#0e6e78';
@endphp
<div class="text-center">
    <div class="donut mx-auto" style="width:132px;height:132px">
        <canvas class="viz-donut" data-pct="{{ $pct }}" data-color="{{ $color }}" width="132" height="132"></canvas>
        <div class="hr-center">
            <div class="hr-bpm">{{ $value ?? '-' }}</div>
            <div class="hr-unit">/menit</div>
        </div>
    </div>
    <div class="donut__label">{{ $label }}</div>
    @if($value && $hrMax)<div class="hr-pct" style="color:{{ $color }}">{{ $pct }}%</div>@endif
</div>
