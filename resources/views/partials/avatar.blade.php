@php
    $size = $size ?? 48;
@endphp
@if(!empty($url))
    <img src="{{ $url }}" alt="Foto {{ $name ?? 'atlet' }}" width="{{ $size }}" height="{{ $size }}"
         class="rounded-circle border" style="width:{{ $size }}px;height:{{ $size }}px;object-fit:cover">
@else
    {{-- Avatar anonim (ikon user) --}}
    <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-body-secondary text-secondary border"
          style="width:{{ $size }}px;height:{{ $size }}px" title="Belum ada foto" aria-label="Belum ada foto">
        <svg xmlns="http://www.w3.org/2000/svg" width="{{ round($size*0.62) }}" height="{{ round($size*0.62) }}" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/>
        </svg>
    </span>
@endif
