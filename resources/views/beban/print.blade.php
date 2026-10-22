@extends('layouts.app')
@section('title', 'Data Beban · ' . $athlete->name)
@php $fmt = fn ($v, $d = 1) => $v === null ? '-' : rtrim(rtrim(number_format((float) $v, $d, ',', '.'), '0'), ','); @endphp
@section('content')

<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 no-print">
    <h1 class="page-title mb-0">Data Beban (1RM)</h1>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-success btn-sm" onclick="window.print()">Cetak / Simpan PDF</button>
        <a class="btn btn-outline-secondary btn-sm" href="{{ route('athletes.show', $athlete) }}">Kembali</a>
    </div>
</div>

<div class="card"><div class="card-body">
    <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
        <div>
            <h2 class="h5 mb-1">{{ $athlete->name }}</h2>
            <div class="text-muted small">{{ $athlete->code }} · {{ $athlete->division ?? '-' }} · {{ config('archery.registration_types')[$athlete->registration_type] ?? '' }}</div>
        </div>
        <div class="text-muted small text-end">Dicetak: {{ now()->translatedFormat('d F Y, H:i') }}</div>
    </div>

    <div class="card-title-sm mb-2">Beban Maksimal — 1RM terbaru per latihan</div>
    <div class="table-responsive"><table class="table table-compact mb-0">
        <thead><tr><th>Latihan</th><th>Tanggal tes</th><th class="text-end">Beban (kg)</th><th class="text-end">Reps</th><th class="text-end">1RM (kg)</th></tr></thead>
        <tbody>
        @forelse($latest as $l)
            <tr>
                <td>{{ $l->exercise }}</td>
                <td>{{ $l->tested_at->format('d/m/Y') }}</td>
                <td class="text-end">{{ $fmt($l->weight_kg) }}</td>
                <td class="text-end">{{ $l->reps }}</td>
                <td class="text-end fw-semibold">{{ $fmt($l->one_rm, 2) }}</td>
            </tr>
        @empty
            <tr><td colspan="5" class="text-muted">Belum ada data beban.</td></tr>
        @endforelse
        </tbody>
    </table></div>

    <p class="text-muted small mt-3 mb-0">Rumus 1RM = beban × (1 + 0,0333 × reps).</p>
</div></div>
@endsection
