@extends('layouts.app')
@section('title', 'Pengguna')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h4 mb-1">Pengguna (non-atlet)</h1>
        <p class="text-muted mb-0 small">Akun admin & pelatih. Atlet mendaftar sendiri lewat halaman registrasi dan dikelola di menu Atlet.</p>
    </div>
    <a href="{{ route('users.create') }}" class="btn btn-sm btn-primary">+ Pengguna</a>
</div>

<div class="card"><div class="table-responsive"><table class="table table-compact mb-0 align-middle">
    <thead><tr><th>Nama</th><th>Email</th><th>Telepon</th><th>Role</th><th></th></tr></thead>
    <tbody>
    @forelse($users as $u)
        <tr>
            <td>{{ $u->name }}</td>
            <td>{{ $u->email }}</td>
            <td>{{ $u->phone ?? '-' }}</td>
            <td><span class="badge {{ $u->hasRole('admin') ? 'text-bg-dark' : 'text-bg-secondary' }}">{{ $roles[$u->role] ?? $u->role }}</span></td>
            <td class="text-end text-nowrap">
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('users.edit', $u) }}">Edit</a>
                <form method="post" action="{{ route('users.destroy', $u) }}" class="d-inline" onsubmit="return confirm('Hapus akun {{ $u->email }}?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Hapus</button></form>
            </td>
        </tr>
    @empty<tr><td colspan="5" class="text-muted p-3">Belum ada pengguna non-atlet.</td></tr>@endforelse
    </tbody>
</table></div></div>
@endsection
