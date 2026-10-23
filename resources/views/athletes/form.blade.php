@extends('layouts.app')
@section('title', $athlete->exists ? 'Edit Atlet' : 'Atlet Baru')
@section('content')
@php
    $isStaff = auth()->user()->isStaff();
    $isOwner = $athlete->exists && auth()->id() === $athlete->user_id;
@endphp
<div class="card"><div class="card-body">
    <h1 class="h4 mb-3">{{ $athlete->exists ? 'Edit biodata' : 'Atlet baru' }}</h1>
    <form method="post" action="{{ $athlete->exists ? route('athletes.update', $athlete) : route('athletes.store') }}" class="row g-3">
        @csrf @if($athlete->exists) @method('PUT') @endif

        {{-- Foto atlet: dikompres di browser & disimpan sebagai .webp --}}
        <div class="col-12">
            <label class="form-label">Foto</label>
            <div class="d-flex align-items-center gap-3">
                <span id="photoPreview">@include('partials.avatar', ['url' => $athlete->photo_url, 'size' => 96, 'name' => $athlete->name])</span>
                <div>
                    <input type="file" id="photoFile" accept="image/*" class="form-control form-control-sm" style="max-width:320px">
                    <input type="hidden" name="photo_data" id="photoData">
                    <div class="form-text">JPG/PNG/WEBP. Otomatis dikompres ke WEBP (maks sisi 512px). Kosongkan untuk pakai avatar anonim.</div>
                    @if($athlete->photo)
                        <div class="form-check mt-1"><input class="form-check-input" type="checkbox" name="remove_photo" value="1" id="rmPhoto"><label class="form-check-label small" for="rmPhoto">Hapus foto saat ini</label></div>
                    @endif
                </div>
            </div>
        </div>

        @if($isStaff)<div class="col-md-3"><label class="form-label">Kode</label><input name="code" value="{{ old('code', $athlete->code) }}" class="form-control" required></div>@endif
        <div class="{{ $isStaff ? 'col-md-9' : 'col-12' }}"><label class="form-label">Nama</label><input name="name" value="{{ old('name', $athlete->name) }}" class="form-control" required></div>
        <div class="col-md-4"><label class="form-label">Tempat lahir</label><input name="birth_place" value="{{ old('birth_place', $athlete->birth_place) }}" class="form-control"></div>
        <div class="col-md-4"><label class="form-label">Tanggal lahir</label><input type="date" name="birth_date" value="{{ old('birth_date', optional($athlete->birth_date)->format('Y-m-d')) }}" class="form-control"></div>
        <div class="col-md-4"><label class="form-label">Jenis kelamin</label>
            <select name="gender" class="form-select"><option value="">-</option><option value="L" @selected(old('gender',$athlete->gender)=='L')>Laki-laki</option><option value="P" @selected(old('gender',$athlete->gender)=='P')>Perempuan</option></select></div>
        <div class="col-md-6"><label class="form-label">Asal daerah</label><input name="origin" value="{{ old('origin', $athlete->origin) }}" class="form-control"></div>
        @if($isStaff)
        <div class="col-md-3"><label class="form-label">Divisi</label>
            <select name="division" class="form-select"><option value="">-</option>@foreach(config('archery.divisions') as $d)<option @selected(old('division',$athlete->division)==$d)>{{ $d }}</option>@endforeach</select></div>
        <div class="col-md-3"><label class="form-label">Jenis pendaftaran</label>
            <select name="registration_type" class="form-select">@foreach(config('archery.registration_types') as $k=>$l)<option value="{{ $k }}" @selected(old('registration_type',$athlete->registration_type)==$k)>{{ $l }}</option>@endforeach</select></div>
        <div class="col-12"><div class="form-check"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="act" @checked(old('is_active', $athlete->is_active))><label class="form-check-label" for="act">Aktif</label></div></div>
        @endif
        <div class="col-12"><button class="btn btn-primary">Simpan</button> <a class="btn btn-link" href="{{ $athlete->exists ? route('athletes.show',$athlete) : route('athletes.index') }}">Batal</a></div>
    </form>
</div></div>

@if($isOwner)
{{-- Panel akun login milik atlet sendiri (email / telepon / kata sandi). --}}
<div class="card mt-3"><div class="card-body">
    <h2 class="h5 mb-1">Akun login</h2>
    <p class="text-muted small mb-3">Email dan kata sandi yang Anda pakai untuk masuk ke aplikasi.</p>
    <form method="post" action="{{ route('account.update') }}" class="row g-3" style="max-width:640px">
        @csrf @method('PUT')
        <div class="col-md-7"><label class="form-label">Email</label>
            <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" class="form-control" required></div>
        <div class="col-md-5"><label class="form-label">Telepon</label>
            <input name="phone" value="{{ old('phone', auth()->user()->phone) }}" class="form-control" placeholder="opsional"></div>
        <div class="col-md-6"><label class="form-label">Kata sandi baru</label>
            <input type="password" name="password" class="form-control" autocomplete="new-password">
            <div class="form-text">Kosongkan bila tidak ingin mengubah.</div></div>
        <div class="col-md-6"><label class="form-label">Ulangi kata sandi</label>
            <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password"></div>
        <div class="col-12"><button class="btn btn-outline-primary">Simpan akun</button></div>
    </form>
</div></div>
@endif

@push('scripts')
<script>
(function () {
    const MAX = 512, QUALITY = 0.8;
    const file = document.getElementById('photoFile');
    const hidden = document.getElementById('photoData');
    const preview = document.getElementById('photoPreview');
    const remove = document.getElementById('rmPhoto');
    if (!file) return;

    file.addEventListener('change', function () {
        const f = file.files && file.files[0];
        if (!f) return;
        if (!f.type.startsWith('image/')) { alert('File harus berupa gambar.'); file.value = ''; return; }

        const img = new Image();
        const reader = new FileReader();
        reader.onload = e => { img.src = e.target.result; };
        img.onload = function () {
            let { width: w, height: h } = img;
            if (w > h && w > MAX) { h = Math.round(h * MAX / w); w = MAX; }
            else if (h >= w && h > MAX) { w = Math.round(w * MAX / h); h = MAX; }

            const canvas = document.createElement('canvas');
            canvas.width = w; canvas.height = h;
            canvas.getContext('2d').drawImage(img, 0, 0, w, h);

            canvas.toBlob(function (blob) {
                if (!blob || blob.type !== 'image/webp') {
                    alert('Browser Anda tidak mendukung konversi WEBP. Pakai Chrome/Edge/Firefox terbaru.');
                    return;
                }
                const r = new FileReader();
                r.onload = ev => {
                    hidden.value = ev.target.result;                 // data:image/webp;base64,...
                    preview.innerHTML = '<img src="' + ev.target.result + '" class="rounded-circle border" style="width:96px;height:96px;object-fit:cover" alt="Pratinjau">';
                    if (remove) { remove.checked = false; }
                };
                r.readAsDataURL(blob);
            }, 'image/webp', QUALITY);
        };
        reader.readAsDataURL(f);
    });

    if (remove) {
        remove.addEventListener('change', function () {
            if (remove.checked) { hidden.value = ''; file.value = ''; preview.style.opacity = .4; }
            else { preview.style.opacity = 1; }
        });
    }
})();
</script>
@endpush
@endsection
