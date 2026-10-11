@extends('layouts.app')
@section('content')
@if(!$staff)
    <div class="alert alert-info">Akun Anda belum terhubung ke data atlet. Hubungi pelatih/admin.</div>
@else
<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="card"><div class="card-body"><div class="card-title-sm">Atlet aktif</div><div class="display-6">{{ $total }}</div></div></div></div>
    <div class="col-md-4"><div class="card"><div class="card-body"><div class="card-title-sm">Rata-rata profil fisik</div><div class="display-6">{{ $avgPhysical === null ? '-' : round($avgPhysical*100,1).'%' }}</div></div></div></div>
    <div class="col-md-4"><div class="card"><div class="card-body"><div class="card-title-sm">Rata-rata profil teknik</div><div class="display-6">{{ $avgTechnique === null ? '-' : round($avgTechnique*100,1).'%' }}</div></div></div></div>
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
                <td><a href="{{ route('athletes.show', $r['athlete']) }}">{{ $r['athlete']->name }}</a></td>
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
