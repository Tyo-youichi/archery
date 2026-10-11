@php $p = $pct === null ? null : round($pct * 100); $size = $size ?? 150; $color = $color ?? '#0e6e78'; @endphp
<div class="text-center">
    <div class="donut mx-auto" style="width:{{ $size }}px;height:{{ $size }}px">
        <canvas class="viz-donut" data-pct="{{ $p ?? 0 }}" data-color="{{ $color }}" width="{{ $size }}" height="{{ $size }}"></canvas>
        <div class="donut__val">{{ $p === null ? '-' : $p.'%' }}</div>
    </div>
    @isset($label)<div class="donut__label">{{ $label }}</div>@endisset
</div>
