@extends('layouts.app')
@section('title', 'Pengguna')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div>
        <h1 class="h4 mb-1">Pengguna</h1>
        <p class="text-muted mb-0 small">Akun admin &amp; pelatih dikelola penuh di sini. Akun atlet ikut tampil agar bisa dinonaktifkan saat keluar klub — biodatanya di menu Atlet.</p>
    </div>
    <a href="{{ route('users.create') }}" class="btn btn-sm btn-primary">+ Pengguna</a>
</div>

<form method="get" class="row g-2 mb-3">
    <div class="col-sm-6 col-md-4">
        <input name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Cari nama / email…">
    </div>
    <div class="col-sm-4 col-md-3">
        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="">Semua status</option>
            <option value="active" @selected(request('status')==='active')>Aktif saja</option>
            <option value="inactive" @selected(request('status')==='inactive')>Nonaktif saja</option>
        </select>
    </div>
    <div class="col-auto"><button class="btn btn-sm btn-outline-secondary">Terapkan</button></div>
</form>

<div class="card"><div class="table-responsive"><table class="table table-compact mb-0 align-middle">
    <thead><tr><th>Nama</th><th>Email</th><th>Telepon</th><th>Role</th><th>Status</th><th></th></tr></thead>
    <tbody>
    @forelse($users as $u)
        @php $isAthlete = $u->hasRole($athleteRole); @endphp
        <tr class="{{ $u->is_active ? '' : 'opacity-75' }}">
            <td>{{ $u->name }}</td>
            <td>{{ $u->email }}</td>
            <td>{{ $u->phone ?? '-' }}</td>
            <td><span class="badge {{ $u->hasRole('admin') ? 'text-bg-dark' : ($isAthlete ? 'text-bg-light border' : 'text-bg-secondary') }}">{{ $roles[$u->role] ?? $u->role }}</span></td>
            <td>
                @if($u->is_active)
                    <span class="badge text-bg-success">Aktif</span>
                @else
                    <span class="badge text-bg-danger">Nonaktif</span>
                @endif
            </td>
            <td class="text-end text-nowrap">
                @if($isAthlete)
                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('athletes.show', $u->athlete) }}" @disabled(! $u->athlete)>Profil atlet</a>
                @else
                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('users.edit', $u) }}">Edit</a>
                @endif

                <form method="post" action="{{ route('users.toggle', $u) }}" class="d-inline"
                      onsubmit="return confirm('{{ $u->is_active ? 'Nonaktifkan' : 'Aktifkan' }} akun {{ $u->email }}?')">
                    @csrf @method('PUT')
                    <button class="btn btn-sm {{ $u->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}">
                        {{ $u->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                </form>

                @unless($isAthlete)
                    <form method="post" action="{{ route('users.destroy', $u) }}" class="d-inline" onsubmit="return confirm('Hapus akun {{ $u->email }}?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Hapus</button></form>
                @endunless
            </td>
        </tr>
    @empty<tr><td colspan="6" class="text-muted p-3">Tidak ada pengguna yang cocok.</td></tr>@endforelse
    </tbody>
</table></div></div>
@endsection
