@php
    $fmt = fn ($v) => ($v === null || $v === '') ? '–' : rtrim(rtrim(number_format((float) $v, 2, '.', ''), '0'), '.');
@endphp
<table class="table table-bordered table-sm report-table align-middle">
    <thead>
        <tr>
            <th rowspan="2">No</th>
            <th rowspan="2">Kode</th>
            <th rowspan="2" class="text-start">Nama</th>
            <th colspan="{{ count($exercises) }}" class="grp">1RM per Latihan (kg)</th>
        </tr>
        <tr>
            @foreach($exercises as $ex)<th>{{ $ex }}</th>@endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($athletes as $a)
            <tr>
                <td class="num">{{ $athletes->firstItem() + $loop->index }}</td>
                <td>{{ $a->code }}</td>
                <td class="name">{{ $a->name }}</td>
                @foreach($exercises as $ex)
                    <td class="num">{{ $fmt($oneRm[$a->id][$ex] ?? null) }}</td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
