@extends('layouts.app')
@section('title', 'RBAC · Roles')
@section('content')
@include('rbac._tabs')
<div class="d-flex justify-content-end mb-2"><a href="{{ route('rbac.roles.create') }}" class="btn btn-sm btn-primary">+ Role</a></div>
<div class="card"><div class="table-responsive"><table class="table table-compact mb-0 align-middle">
    <thead><tr><th>Kode</th><th>Nama</th><th>Staf</th><th>Jumlah user</th><th>Urutan</th><th></th></tr></thead>
    <tbody>
    @forelse($roles as $r)
        <tr>
            <td><code>{{ $r->code }}</code></td>
            <td>{{ $r->name }}</td>
            <td>@if($r->is_staff)<span class="badge text-bg-success">Ya</span>@else<span class="badge text-bg-secondary">Tidak</span>@endif</td>
            <td>{{ $userCounts[$r->code] ?? 0 }}</td>
            <td>{{ $r->sort }}</td>
            <td class="text-end text-nowrap">
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('rbac.roles.edit', $r) }}">Edit</a>
                <form method="post" action="{{ route('rbac.roles.destroy', $r) }}" class="d-inline" onsubmit="return confirm('Hapus role ini?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Hapus</button></form>
            </td>
        </tr>
    @empty<tr><td colspan="6" class="text-muted p-3">Belum ada role.</td></tr>@endforelse
    </tbody>
</table></div></div>
@endsection
