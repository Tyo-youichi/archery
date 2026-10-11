@extends('layouts.app')
@section('title', 'Metrik & Benchmark')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h4 mb-1">Metrik &amp; Benchmark</h1>
        <p class="text-muted mb-0 small">Kelola metrik tes dan nilai benchmark (BM). Perubahan berlaku untuk input & perhitungan berikutnya; data tes lama memakai BM yang tersimpan saat input.</p>
    </div>
    <a href="{{ route('metrics.create') }}" class="btn btn-sm btn-primary">+ Metrik</a>
</div>

@foreach($domains as $key => $title)
    <div class="card mb-3"><div class="card-body">
        <div class="card-title-sm mb-2">{{ $title }}</div>
        <div class="table-responsive"><table class="table table-compact mb-0 align-middle">
            <thead><tr><th>Grup</th><th>Metrik</th><th>Key</th><th class="text-end">BM Putra</th><th class="text-end">BM Putri</th><th>Satuan</th><th class="text-center">Index</th><th class="text-center">Aktif</th><th class="text-end">Urut</th><th></th></tr></thead>
            <tbody>
            @forelse($metrics[$key] ?? [] as $m)
                <tr class="{{ $m->is_active ? '' : 'text-muted' }}">
                    <td><span class="badge text-bg-light">{{ $m->group_label }}</span></td>
                    <td>{{ $m->label }}</td>
                    <td><code>{{ $m->key }}</code></td>
                    <td class="text-end">{{ $m->bm_l !== null ? rtrim(rtrim(number_format($m->bm_l, 2, ',', '.'), '0'), ',') : '-' }}</td>
                    <td class="text-end">{{ $m->bm_p !== null ? rtrim(rtrim(number_format($m->bm_p, 2, ',', '.'), '0'), ',') : '-' }}</td>
                    <td>{{ $m->unit ?? '-' }}</td>
                    <td class="text-center">@if($m->is_promoted)<span class="badge text-bg-info" title="Perlu kolom generated ter-index">★</span>@endif</td>
                    <td class="text-center">@if($m->is_active)<span class="badge text-bg-success">Ya</span>@else<span class="badge text-bg-secondary">Tidak</span>@endif</td>
                    <td class="text-end">{{ $m->sort }}</td>
                    <td class="text-end text-nowrap">
                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('metrics.edit', $m) }}">Edit</a>
                        <form method="post" action="{{ route('metrics.destroy', $m) }}" class="d-inline" onsubmit="return confirm('Hapus metrik ini? Data tes lama tetap tersimpan.')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Hapus</button></form>
                    </td>
                </tr>
            @empty<tr><td colspan="10" class="text-muted p-3">Belum ada metrik untuk domain ini.</td></tr>@endforelse
            </tbody>
        </table></div>
    </div></div>
@endforeach

<p class="text-muted small">★ = ditandai perlu kolom ter-index (generated column) untuk analitik lintas-atlet. Pembuatan kolomnya disiapkan lewat migration oleh developer.</p>
@endsection
