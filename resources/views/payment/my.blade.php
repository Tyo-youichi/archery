@extends('layouts.app')
@section('title', 'Pembayaran')
@php
    $fee = (int) config('archery.monthly_fee', 0);
    $rupiah = fn ($v) => 'Rp ' . number_format((float) $v, 0, ',', '.');
    $badge = ['pending' => 'text-bg-warning', 'verified' => 'text-bg-success', 'rejected' => 'text-bg-danger'];
    $statusLabel = ['pending' => 'Menunggu verifikasi', 'verified' => 'Terverifikasi', 'rejected' => 'Ditolak'];
@endphp
@section('content')

@if($current->exists && $current->isVerified())
    <div class="alert alert-success">✔ Pembayaran periode <strong>{{ $now->translatedFormat('F Y') }}</strong> sudah terverifikasi. Akses aktif.</div>
@else
    <div class="alert alert-warning"><strong>Akses dikunci.</strong> Unggah bukti pembayaran periode <strong>{{ $now->translatedFormat('F Y') }}</strong> lalu tunggu verifikasi admin.</div>
@endif

<div class="row g-3">
    <div class="col-lg-6"><div class="card"><div class="card-body">
        <h1 class="h5 mb-3">Unggah Bukti Pembayaran</h1>
        <table class="table table-sm table-borderless mb-3">
            <tr><th style="width:38%">Atlet</th><td>{{ $athlete->name }} ({{ $athlete->code }})</td></tr>
            <tr><th>Periode</th><td>{{ $now->translatedFormat('F Y') }}</td></tr>
            @if($fee > 0)<tr><th>Iuran</th><td>{{ $rupiah($fee) }}</td></tr>@endif
            <tr><th>Status</th><td>
                @if($current->exists)
                    <span class="badge {{ $badge[$current->status] }}">{{ $statusLabel[$current->status] }}</span>
                    @if($current->status === 'rejected' && $current->note)<div class="small text-danger mt-1">{{ $current->note }}</div>@endif
                @else<span class="text-muted">Belum ada</span>@endif
            </td></tr>
        </table>

        @if(!($current->exists && $current->isVerified()))
        <form method="post" action="{{ route('payment.upload') }}" class="row g-3">
            @csrf
            <div class="col-12">
                <label class="form-label">Bukti transfer (gambar)</label>
                <input type="file" id="proofFile" accept="image/*" class="form-control" required>
                <input type="hidden" name="proof_data" id="proofData">
                <div class="form-text">JPG/PNG/WEBP; otomatis dikompres ke WEBP (maks sisi 1600px, 3 MB).</div>
                <div id="proofPreview" class="mt-2"></div>
            </div>
            <div class="col-md-6"><label class="form-label">Jumlah dibayar (opsional)</label>
                <input type="number" name="amount" value="{{ old('amount', $fee ?: null) }}" step="any" min="0" class="form-control"></div>
            <div class="col-12"><label class="form-label">Catatan (opsional)</label>
                <input name="note" value="{{ old('note') }}" class="form-control" placeholder="mis. transfer BCA a.n. ..."></div>
            <div class="col-12"><button class="btn btn-primary" id="submitBtn" disabled>Kirim bukti</button></div>
        </form>
        @endif
    </div></div></div>

    <div class="col-lg-6"><div class="card"><div class="card-body">
        <div class="card-title-sm mb-2">Riwayat Pembayaran</div>
        <div class="table-responsive"><table class="table table-compact mb-0">
            <thead><tr><th>Periode</th><th>Jumlah</th><th>Status</th><th>Bukti</th></tr></thead>
            <tbody>
            @forelse($history as $p)
                <tr>
                    <td>{{ $p->period_label }}</td>
                    <td>{{ $p->amount ? $rupiah($p->amount) : '-' }}</td>
                    <td><span class="badge {{ $badge[$p->status] }}">{{ $statusLabel[$p->status] }}</span></td>
                    <td>@if($p->proof)<a href="{{ route('payment.proof', $p) }}" target="_blank">Lihat</a>@else-@endif</td>
                </tr>
            @empty<tr><td colspan="4" class="text-muted">Belum ada pembayaran.</td></tr>@endforelse
            </tbody>
        </table></div>
    </div></div></div>
</div>

@push('scripts')
<script>
(function () {
    const MAX = 1600, QUALITY = 0.85;
    const file = document.getElementById('proofFile');
    const hidden = document.getElementById('proofData');
    const preview = document.getElementById('proofPreview');
    const submit = document.getElementById('submitBtn');
    if (!file) return;

    file.addEventListener('change', function () {
        const f = file.files && file.files[0];
        if (!f) return;
        if (!f.type.startsWith('image/')) { alert('File harus berupa gambar.'); file.value = ''; return; }
        const img = new Image(), reader = new FileReader();
        reader.onload = e => { img.src = e.target.result; };
        img.onload = function () {
            let w = img.width, h = img.height;
            if (w > h && w > MAX) { h = Math.round(h * MAX / w); w = MAX; }
            else if (h >= w && h > MAX) { w = Math.round(w * MAX / h); h = MAX; }
            const c = document.createElement('canvas'); c.width = w; c.height = h;
            c.getContext('2d').drawImage(img, 0, 0, w, h);
            c.toBlob(function (blob) {
                if (!blob || blob.type !== 'image/webp') { alert('Browser tidak mendukung konversi WEBP. Pakai Chrome/Edge/Firefox terbaru.'); return; }
                const r = new FileReader();
                r.onload = ev => {
                    hidden.value = ev.target.result;
                    preview.innerHTML = '<img src="' + ev.target.result + '" class="border rounded" style="max-width:260px;max-height:260px">';
                    if (submit) submit.disabled = false;
                };
                r.readAsDataURL(blob);
            }, 'image/webp', QUALITY);
        };
        reader.readAsDataURL(f);
    });
})();
</script>
@endpush
@endsection
