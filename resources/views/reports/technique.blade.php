@php
    $fmt = fn ($v) => ($v === null || $v === '') ? '–' : rtrim(rtrim(number_format((float) $v, 2, '.', ''), '0'), '.');
@endphp
<table class="table table-bordered table-sm report-table align-middle">
    <thead>
        <tr>
            <th>No</th>
            <th>Kode</th>
            <th class="text-start">Nama</th>
            @forelse($metricDefs as $m)<th>{{ $m['label'] }}</th>@empty<th class="report-muted">Belum ada metrik teknik</th>@endforelse
        </tr>
    </thead>
    <tbody>
        @foreach($athletes as $i => $a)
            @php $t = $a->techniqueTests->first(); @endphp
            <tr>
                <td class="num">{{ $i + 1 }}</td>
                <td>{{ $a->code }}</td>
                <td class="name">{{ $a->name }}</td>
                @forelse($metricDefs as $key => $m)
                    <td class="num">{{ $fmt(data_get($t?->results, $key . '.result')) }}</td>
                @empty<td></td>@endforelse
            </tr>
        @endforeach
    </tbody>
</table>
