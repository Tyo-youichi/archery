@extends('layouts.app')
@section('title', 'Verifikasi Pembayaran')
@php
    $badge = ['pending' => 'text-bg-warning', 'verified' => 'text-bg-success', 'rejected' => 'text-bg-danger'];
    $statusLabel = ['pending' => 'Menunggu', 'verified' => 'Terverifikasi', 'rejected' => 'Ditolak'];
    $rupiah = fn ($v) => $v === null ? '-' : 'Rp ' . number_format((float) $v, 0, ',', '.');
@endphp
@section('content')
<h1 class="h4 mb-3">Verifikasi Pembayaran</h1>

<ul class="nav nav-pills mb-3">
    @foreach(['pending' => 'Menunggu', 'verified' => 'Terverifikasi', 'rejected' => 'Ditolak', 'all' => 'Semua'] as $k => $label)
        <li class="nav-item"><a class="nav-link @if($status === $k) active @endif" href="{{ route('payment.index', ['status' => $k]) }}">
            {{ $label }}@if($k !== 'all') <span class="badge text-bg-light">{{ $counts[$k] ?? 0 }}</span>@endif
        </a></li>
    @endforeach
</ul>

<div class="card"><div class="table-responsive"><table class="table table-compact mb-0 align-middle">
    <thead><tr><th>Atlet</th><th>Periode</th><th class="text-end">Jumlah</th><th>Status</th><th>Diperbarui</th><th>Bukti</th><th></th></tr></thead>
    <tbody>
    @forelse($payments as $p)
        <tr>
            <td>{{ $p->athlete->name }}<div class="small text-muted">{{ $p->athlete->code }}</div></td>
            <td>{{ $p->period_label }}</td>
            <td class="text-end">{{ $rupiah($p->amount) }}</td>
            <td><span class="badge {{ $badge[$p->status] }}">{{ $statusLabel[$p->status] }}</span>
                @if($p->status === 'rejected' && $p->note)<div class="small text-danger">{{ $p->note }}</div>@endif</td>
            <td class="small text-muted">{{ $p->updated_at->format('d/m/Y H:i') }}</td>
            <td>@if($p->proof)<a href="{{ route('payment.proof', $p) }}" target="_blank">Lihat</a>@else<span class="text-muted">-</span>@endif</td>
            <td class="text-end text-nowrap">
                @if($p->status !== 'verified')
                    <form method="post" action="{{ route('payment.verify', $p) }}" class="d-inline">@csrf @method('PUT')<button class="btn btn-sm btn-success">Verifikasi</button></form>
                @endif
                @if($p->status !== 'rejected')
                    <form method="post" action="{{ route('payment.reject', $p) }}" class="d-inline" onsubmit="return confirm('Tolak bukti pembayaran ini?')">@csrf @method('PUT')<button class="btn btn-sm btn-outline-danger">Tolak</button></form>
                @endif
            </td>
        </tr>
    @empty<tr><td colspan="7" class="text-muted p-3">Tidak ada data.</td></tr>@endforelse
    </tbody>
</table></div></div>

<div class="mt-3">{{ $payments->links() }}</div>
@endsection
