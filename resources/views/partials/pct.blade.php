@php
    $v = $pct === null ? null : round($pct * 100, 1);
    $cls = $v === null ? 'bg-secondary' : ($v >= 90 ? 'bg-success' : ($v >= 75 ? 'bg-warning' : 'bg-danger'));
@endphp
<div class="d-flex align-items-center gap-2">
    <div class="progress flex-grow-1 pct-bar"><div class="progress-bar {{ $cls }}" style="width: {{ $v ?? 0 }}%"></div></div>
    <small class="text-nowrap" style="min-width:3.2rem">{{ $v === null ? '-' : $v . '%' }}</small>
</div>
