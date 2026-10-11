@extends('layouts.app')
@section('title', 'Atlet')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Database Atlet</h1>
    <a href="{{ route('athletes.create') }}" class="btn btn-primary">+ Atlet</a>
</div>
<form class="row g-2 mb-3" method="get">
    <div class="col-md-5"><input name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama / kode"></div>
    <div class="col-md-3"><select name="division" class="form-select"><option value="">Semua divisi</option>@foreach(config('archery.divisions') as $d)<option @selected(request('division')==$d)>{{ $d }}</option>@endforeach</select></div>
    <div class="col-md-2"><button class="btn btn-outline-secondary w-100">Cari</button></div>
</form>
<div class="card"><div class="table-responsive"><table class="table table-compact table-hover mb-0 align-middle">
    <thead><tr><th>Kode</th><th>Nama</th><th>JK</th><th>Usia</th><th>Divisi</th><th>Pendaftaran</th><th>Status</th></tr></thead>
    <tbody>
    @forelse($athletes as $a)
        <tr>
            <td>{{ $a->code }}</td>
            <td><span class="d-inline-flex align-items-center gap-2">@include('partials.avatar', ['url' => $a->photo_url, 'size' => 30, 'name' => $a->name])<a href="{{ route('athletes.show', $a) }}">{{ $a->name }}</a></span></td>
            <td>{{ $a->gender ?? '-' }}</td><td>{{ $a->age ?? '-' }}</td><td>{{ $a->division ?? '-' }}</td>
            <td>{{ config('archery.registration_types')[$a->registration_type] ?? $a->registration_type }}</td>
            <td>@if($a->is_active)<span class="badge text-bg-success">Aktif</span>@else<span class="badge text-bg-secondary">Nonaktif</span>@endif</td>
        </tr>
    @empty
        <tr><td colspan="7" class="text-muted p-3">Tidak ada data.</td></tr>
    @endforelse
    </tbody>
</table></div></div>
<div class="mt-3">{{ $athletes->links() }}</div>
@endsection
