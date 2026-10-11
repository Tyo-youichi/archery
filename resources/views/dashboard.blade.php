@extends('layouts.app')
@section('content')
@if(!$staff)
    <div class="alert alert-info">Akun Anda belum terhubung ke data atlet. Hubungi pelatih/admin.</div>
@else
<h1 class="page-title mb-3">Dashboard</h1>
<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="card"><div class="card-body stat">
        <span class="stat__icon"><svg width="24" height="24" viewBox="0 0 16 16" fill="currentColor"><path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1A.27.27 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4"/></svg></span>
        <div><div class="stat__value">{{ $total }}</div><div class="stat__label">Atlet aktif</div></div>
    </div></div></div>
    <div class="col-md-4"><div class="card"><div class="card-body stat">
        <span class="stat__icon stat__icon--good"><svg width="24" height="24" viewBox="0 0 16 16" fill="currentColor"><path d="M0 0h1v15h15v1H0zm14.817 3.113a.5.5 0 0 1 .07.704l-4.5 5.5a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61 4.15-5.073a.5.5 0 0 1 .704-.07"/></svg></span>
        <div><div class="stat__value">{{ $avgPhysical === null ? '-' : str_replace('.', ',', round($avgPhysical*100,1)).'%' }}</div><div class="stat__label">Rata-rata profil fisik</div></div>
    </div></div></div>
    <div class="col-md-4"><div class="card"><div class="card-body stat">
        <span class="stat__icon stat__icon--gold"><svg width="24" height="24" viewBox="0 0 16 16" fill="currentColor"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/><path d="M11 8a3 3 0 1 1-6 0 3 3 0 0 1 6 0m-2 0a1 1 0 1 0-2 0 1 1 0 0 0 2 0"/><path d="M8 4a4 4 0 1 0 0 8 4 4 0 0 0 0-8M5 8a3 3 0 1 1 6 0 3 3 0 0 1-6 0"/></svg></span>
        <div><div class="stat__value">{{ $avgTechnique === null ? '-' : str_replace('.', ',', round($avgTechnique*100,1)).'%' }}</div><div class="stat__label">Rata-rata profil teknik</div></div>
    </div></div></div>
</div>
<div class="card"><div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h2 class="h5 mb-0">Ringkasan atlet</h2>
        <a href="{{ route('athletes.create') }}" class="btn btn-sm btn-primary">+ Atlet</a>
    </div>
    <p class="text-muted small">Hari shoot 100 arrow minggu ini (mulai {{ $weekStart->translatedFormat('d M Y') }}) dari Buku Saku.</p>
    <div class="table-responsive"><table class="table table-compact align-middle">
        <thead><tr><th>Kode</th><th>Nama</th><th>Divisi</th><th style="width:20%">Fisik</th><th style="width:20%">Teknik</th><th>100 arrow / minggu</th></tr></thead>
        <tbody>
        @forelse($rows as $r)
            <tr>
                <td>{{ $r['athlete']->code }}</td>
                <td><span class="d-inline-flex align-items-center gap-2">@include('partials.avatar', ['url' => $r['athlete']->photo_url, 'size' => 30, 'name' => $r['athlete']->name])<a href="{{ route('athletes.show', $r['athlete']) }}">{{ $r['athlete']->name }}</a></span></td>
                <td>{{ $r['athlete']->division ?? '-' }}</td>
                <td>@include('partials.pct', ['pct' => $r['physical']])</td>
                <td>@include('partials.pct', ['pct' => $r['technique']])</td>
                <td>{{ $weekLogs[$r['athlete']->id] ?? 0 }} / 6</td>
            </tr>
        @empty
            <tr><td colspan="6" class="text-muted">Belum ada atlet.</td></tr>
        @endforelse
        </tbody>
    </table></div>
</div></div>
@endif
@endsection
