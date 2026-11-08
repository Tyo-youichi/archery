{{--
    Pagination kustom (Bootstrap 5) yang SELALU tampil — termasuk saat hanya ada
    satu halaman — agar tampilan semua tabel konsisten. Dipakai sebagai view
    pagination default aplikasi (Paginator::defaultView di AppServiceProvider).
--}}
<nav class="d-flex flex-wrap align-items-center justify-content-between gap-2">
    <p class="small text-muted mb-0">
        Menampilkan
        @if ($paginator->firstItem())
            <span class="fw-semibold">{{ $paginator->firstItem() }}</span>–<span class="fw-semibold">{{ $paginator->lastItem() }}</span>
        @else
            <span class="fw-semibold">{{ $paginator->count() }}</span>
        @endif
        dari <span class="fw-semibold">{{ $paginator->total() }}</span> data
    </p>

    <ul class="pagination pagination-sm mb-0">
        {{-- Sebelumnya --}}
        @if ($paginator->onFirstPage())
            <li class="page-item disabled" aria-disabled="true" aria-label="Sebelumnya"><span class="page-link" aria-hidden="true">&lsaquo;</span></li>
        @else
            <li class="page-item"><a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Sebelumnya">&lsaquo;</a></li>
        @endif

        {{-- Nomor halaman --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Berikutnya --}}
        @if ($paginator->hasMorePages())
            <li class="page-item"><a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Berikutnya">&rsaquo;</a></li>
        @else
            <li class="page-item disabled" aria-disabled="true" aria-label="Berikutnya"><span class="page-link" aria-hidden="true">&rsaquo;</span></li>
        @endif
    </ul>
</nav>
