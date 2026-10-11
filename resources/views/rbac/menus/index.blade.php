@extends('layouts.app')
@section('title', 'RBAC · Menu')
@section('content')
@include('rbac._tabs')
<div class="d-flex justify-content-end mb-2"><a href="{{ route('rbac.menus.create') }}" class="btn btn-sm btn-primary">+ Menu</a></div>
<div class="card"><div class="table-responsive"><table class="table table-compact mb-0 align-middle">
    <thead><tr><th>Urutan</th><th>Label</th><th>Route</th><th>Param</th><th>Role</th><th>Aktif</th><th></th></tr></thead>
    <tbody>
    @forelse($menus as $m)
        <tr>
            <td>{{ $m->sort }}</td><td>{{ $m->label }}</td><td><code>{{ $m->route }}</code></td><td>{{ $m->param ?? '-' }}</td>
            <td>@foreach($m->roles as $r)<span class="badge text-bg-primary">{{ $r }}</span> @endforeach</td>
            <td>@if($m->is_active)<span class="badge text-bg-success">Ya</span>@else<span class="badge text-bg-secondary">Tidak</span>@endif</td>
            <td class="text-end text-nowrap">
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('rbac.menus.edit', $m) }}">Edit</a>
                <form method="post" action="{{ route('rbac.menus.destroy', $m) }}" class="d-inline" onsubmit="return confirm('Hapus menu ini?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Hapus</button></form>
            </td>
        </tr>
    @empty<tr><td colspan="7" class="text-muted p-3">Belum ada menu.</td></tr>@endforelse
    </tbody>
</table></div></div>
@endsection
