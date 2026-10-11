@extends('layouts.app')
@section('title', 'Buku Saku Atlet')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div>
        <h1 class="h4 mb-0">Tabel Monitor Shoot 100 Arrow</h1>
        <div class="text-muted">{{ $athlete->name }} · Minggu {{ $start->translatedFormat('d M') }} – {{ $start->copy()->addDays(5)->translatedFormat('d M Y') }}</div>
    </div>
    <div class="btn-group">
        <a class="btn btn-outline-secondary btn-sm" href="{{ route('shoot-log.edit', [$athlete, 'week' => $prev]) }}">← Minggu lalu</a>
        <a class="btn btn-outline-secondary btn-sm" href="{{ route('shoot-log.edit', $athlete) }}">Minggu ini</a>
        <a class="btn btn-outline-secondary btn-sm" href="{{ route('shoot-log.edit', [$athlete, 'week' => $next]) }}">Minggu depan →</a>
    </div>
</div>

<form method="post" action="{{ route('shoot-log.update', $athlete) }}">
    @csrf @method('PUT')
    <input type="hidden" name="week" value="{{ $start->toDateString() }}">
    <div class="card mb-3"><div class="table-responsive"><table class="table table-bordered align-middle text-center mb-0">
        <thead class="table-light">
            <tr><th rowspan="2" class="align-middle">Hari</th><th colspan="6">Teknik diperbaiki</th><th colspan="2">100 arrow</th><th rowspan="2" class="align-middle" style="min-width:220px">Catatan</th></tr>
            <tr>@foreach(config('archery.technique_codes') as $c => $n)<th title="{{ $n }}">{{ $c }}</th>@endforeach<th>Ya</th><th>Tdk</th></tr>
        </thead>
        <tbody>
        @foreach($days as $d)
            @php $key = $d['date']->toDateString(); $log = $d['log']; $sel = old("logs.$key.techniques", $log?->techniques ?? []); @endphp
            <tr>
                <th class="text-start">{{ $d['name'] }}<div class="small fw-normal text-muted">{{ $d['date']->format('d/m') }}</div></th>
                @foreach(config('archery.technique_codes') as $c => $n)
                    <td><input class="form-check-input" type="checkbox" name="logs[{{ $key }}][techniques][]" value="{{ $c }}" @checked(in_array($c, $sel))></td>
                @endforeach
                @php $shot = old("logs.$key.shot_100", $log ? ($log->shot_100 ? 'ya' : 'tdk') : null); @endphp
                <td><input class="form-check-input" type="radio" name="logs[{{ $key }}][shot_100]" value="ya" @checked($shot === 'ya')></td>
                <td><input class="form-check-input" type="radio" name="logs[{{ $key }}][shot_100]" value="tdk" @checked($shot === 'tdk')></td>
                <td><textarea name="logs[{{ $key }}][notes]" rows="2" class="form-control form-control-sm">{{ old("logs.$key.notes", $log?->notes) }}</textarea></td>
            </tr>
        @endforeach
        </tbody>
    </table></div></div>

    <div class="row g-3 mb-3">
        <div class="col-md-6"><div class="card h-100"><div class="card-body small">
            <strong>Note</strong>
            <ol class="mb-0 ps-3">
                <li>Beri tanda (√) pada teknik yang kalian perbaiki</li>
                <li>Beri tanda pada Ya/Tdk jika melakukan/tidak shoot 100 arrow</li>
                <li>Kode teknik: @foreach(config('archery.technique_codes') as $c => $n)<strong>{{ $c }}</strong>. {{ $n }}@if(!$loop->last); @endif @endforeach</li>
                <li>Catat semua temuan kalian di latihan</li>
            </ol>
        </div></div></div>
        <div class="col-md-6"><div class="card h-100"><div class="card-body small">
            <strong>Rekap hari shoot 100 arrow (8 minggu terakhir)</strong>
            <ul class="mb-0 ps-3">@forelse($recap->sortKeysDesc() as $w => $n)<li>Minggu {{ \Illuminate\Support\Carbon::parse($w)->format('d/m') }}: {{ $n }} hari</li>@empty<li class="text-muted">Belum ada catatan.</li>@endforelse</ul>
        </div></div></div>
    </div>
    <button class="btn btn-primary">Simpan minggu ini</button>
    <a class="btn btn-link" href="{{ route('athletes.show', $athlete) }}">Kembali ke profil</a>
</form>
@endsection
