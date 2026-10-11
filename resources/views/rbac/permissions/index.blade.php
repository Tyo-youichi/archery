@extends('layouts.app')
@section('title', 'RBAC · Izin Route')
@section('content')
@include('rbac._tabs')
<div class="d-flex justify-content-between align-items-center mb-2">
    <p class="text-muted small mb-0">Izin per-route. Pola boleh wildcard (mis. <code>records.*</code>). Urutan kecil diperiksa lebih dulu; route yang tidak tercantum boleh untuk semua user login.</p>
    <a href="{{ route('rbac.permissions.create') }}" class="btn btn-sm btn-primary text-nowrap">+ Izin</a>
</div>
<div class="card"><div class="table-responsive"><table class="table table-compact mb-0 align-middle">
    <thead><tr><th>Urutan</th><th>Pola route</th><th>Role yang boleh</th><th>Keterangan</th><th></th></tr></thead>
    <tbody>
    @forelse($permissions as $p)
        <tr>
            <td>{{ $p->sort }}</td>
            <td><code>{{ $p->pattern }}</code></td>
            <td>@foreach($p->roles as $r)<span class="badge text-bg-primary">{{ $r }}</span> @endforeach</td>
            <td class="small text-muted">{{ $p->description }}</td>
            <td class="text-end text-nowrap">
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('rbac.permissions.edit', $p) }}">Edit</a>
                <form method="post" action="{{ route('rbac.permissions.destroy', $p) }}" class="d-inline" onsubmit="return confirm('Hapus izin ini?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Hapus</button></form>
            </td>
        </tr>
    @empty<tr><td colspan="5" class="text-muted p-3">Belum ada izin route.</td></tr>@endforelse
    </tbody>
</table></div></div>
@endsection
