@extends('layouts.app')
@section('title', $athlete->exists ? 'Edit Atlet' : 'Atlet Baru')
@section('content')
<div class="card"><div class="card-body">
    <h1 class="h4 mb-3">{{ $athlete->exists ? 'Edit biodata' : 'Atlet baru' }}</h1>
    <form method="post" action="{{ $athlete->exists ? route('athletes.update', $athlete) : route('athletes.store') }}" class="row g-3">
        @csrf @if($athlete->exists) @method('PUT') @endif
        <div class="col-md-3"><label class="form-label">Kode</label><input name="code" value="{{ old('code', $athlete->code) }}" class="form-control" required></div>
        <div class="col-md-9"><label class="form-label">Nama</label><input name="name" value="{{ old('name', $athlete->name) }}" class="form-control" required></div>
        <div class="col-md-4"><label class="form-label">Tempat lahir</label><input name="birth_place" value="{{ old('birth_place', $athlete->birth_place) }}" class="form-control"></div>
        <div class="col-md-4"><label class="form-label">Tanggal lahir</label><input type="date" name="birth_date" value="{{ old('birth_date', optional($athlete->birth_date)->format('Y-m-d')) }}" class="form-control"></div>
        <div class="col-md-4"><label class="form-label">Jenis kelamin</label>
            <select name="gender" class="form-select"><option value="">-</option><option value="L" @selected(old('gender',$athlete->gender)=='L')>Laki-laki</option><option value="P" @selected(old('gender',$athlete->gender)=='P')>Perempuan</option></select></div>
        <div class="col-md-6"><label class="form-label">Asal daerah</label><input name="origin" value="{{ old('origin', $athlete->origin) }}" class="form-control"></div>
        <div class="col-md-3"><label class="form-label">Divisi</label>
            <select name="division" class="form-select"><option value="">-</option>@foreach(config('archery.divisions') as $d)<option @selected(old('division',$athlete->division)==$d)>{{ $d }}</option>@endforeach</select></div>
        <div class="col-md-3"><label class="form-label">Jenis pendaftaran</label>
            <select name="registration_type" class="form-select">@foreach(config('archery.registration_types') as $k=>$l)<option value="{{ $k }}" @selected(old('registration_type',$athlete->registration_type)==$k)>{{ $l }}</option>@endforeach</select></div>
        <div class="col-12"><div class="form-check"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="act" @checked(old('is_active', $athlete->is_active))><label class="form-check-label" for="act">Aktif</label></div></div>
        <div class="col-12"><button class="btn btn-primary">Simpan</button> <a class="btn btn-link" href="{{ $athlete->exists ? route('athletes.show',$athlete) : route('athletes.index') }}">Batal</a></div>
    </form>
</div></div>
@endsection
