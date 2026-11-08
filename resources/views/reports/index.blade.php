@extends('layouts.app')
@section('title', 'Laporan · ' . $title)

@push('head')
<style>
    .report-tabs { gap:.35rem; }
    .report-scroll { overflow-x:auto; -webkit-overflow-scrolling:touch; padding-bottom:.85rem; }
    .report-table { font-size:.78rem; white-space:nowrap; margin-bottom:0; }
    .report-table thead th { background:#f1f5f9; vertical-align:middle; text-align:center; position:sticky; top:0; z-index:1; }
    .report-table th, .report-table td { padding:.3rem .5rem; }
    .report-table td.num { text-align:right; font-variant-numeric:tabular-nums; }
    .report-table td.name { white-space:normal; min-width:160px; font-weight:600; }
    .report-table .grp { background:#e2e8f0; }
    .report-muted { color:#94a3b8; }

    @media print {
        @page { size: A4 landscape; margin: 9mm; }
        .no-print { display:none !important; }
        .report-scroll { overflow:visible !important; padding-bottom:0 !important; }
        .report-table { font-size:9.5px; }
        .report-table th, .report-table td { padding:2px 4px; }
        .report-table thead th { position:static; }
        .card { border:none !important; box-shadow:none !important; }
        a[href]:after { content:""; }
    }
</style>
@endpush

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3 no-print">
    <div>
        <h1 class="h4 mb-1">Laporan</h1>
        <p class="text-muted mb-0 small">Rekap seluruh atlet. Untuk cetak/PDF seluruh atlet, pilih baris <strong>Semua</strong> lalu Cetak.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ request()->fullUrlWithQuery(['all' => $showingAll ? null : 1, 'page' => null]) }}" class="btn btn-sm btn-outline-secondary">
            {{ $showingAll ? 'Hanya atlet aktif' : 'Tampilkan semua atlet' }}
        </a>
        <button type="button" class="btn btn-sm btn-primary" onclick="window.print()">Cetak / PDF</button>
    </div>
</div>

<ul class="nav nav-pills report-tabs mb-3 no-print">
    @foreach($types as $t)
        <li class="nav-item">
            <a class="nav-link {{ $t === $type ? 'active' : '' }} py-1 px-3"
               href="{{ route('reports.index', array_merge(['type' => $t], $showingAll ? ['all' => 1] : [])) }}">
                {{ $titles[$t] }}
            </a>
        </li>
    @endforeach
</ul>

<div class="card"><div class="card-body">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
        <h2 class="h6 mb-0">{{ $title }}
            @if($type === 'skor')<span class="text-muted">— Tahun {{ $year }}</span>@endif
        </h2>
        <form method="get" class="d-flex flex-wrap align-items-center gap-2 no-print">
            @if($showingAll)<input type="hidden" name="all" value="1">@endif
            @if($type === 'skor')
                <label class="small text-muted mb-0">Tahun</label>
                <select name="year" class="form-select form-select-sm" style="width:auto" onchange="this.form.submit()">
                    @foreach($years as $y)<option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>@endforeach
                </select>
            @endif
            <label class="small text-muted mb-0">Baris</label>
            <select name="per" class="form-select form-select-sm" style="width:auto" onchange="this.form.submit()">
                <option value="25" @selected($per === '25')>25</option>
                <option value="50" @selected($per === '50')>50</option>
                <option value="all" @selected($per === 'all')>Semua</option>
            </select>
        </form>
    </div>

    @if($athletes->isEmpty())
        <p class="text-muted mb-0 p-2">Belum ada atlet{{ $showingAll ? '' : ' aktif' }}.</p>
    @else
        @php $partials = ['fisik' => 'physical', 'teknik' => 'technique', 'skor' => 'score', 'peralatan' => 'equipment', 'beban' => 'load']; @endphp
        <div class="report-scroll">
            @include('reports.' . ($partials[$type] ?? $type))
        </div>

        <div class="mt-3 no-print">{{ $athletes->links() }}</div>
    @endif
</div></div>
@endsection
