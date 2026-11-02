@php
    $fmt = fn ($v) => ($v === null || $v === '') ? '–' : rtrim(rtrim(number_format((float) $v, 2, '.', ''), '0'), '.');
@endphp
<table class="table table-bordered table-sm report-table align-middle">
    <thead>
        <tr>
            <th rowspan="2">No</th>
            <th rowspan="2">Kode</th>
            <th rowspan="2" class="text-start">Nama</th>
            <th colspan="{{ count($hrCols) }}" class="grp">Denyut Nadi</th>
            <th colspan="{{ count($anthroCols) }}" class="grp">Antropometri</th>
            <th colspan="{{ max(count($metricDefs), 1) }}" class="grp">Performa Fisik</th>
        </tr>
        <tr>
            @foreach($hrCols as $label)<th>{{ $label }}</th>@endforeach
            @foreach($anthroCols as $label)<th>{{ $label }}</th>@endforeach
            @forelse($metricDefs as $m)<th>{{ $m['label'] }}</th>@empty<th class="report-muted">—</th>@endforelse
        </tr>
    </thead>
    <tbody>
        @foreach($athletes as $i => $a)
            @php $an = $a->anthropometries->first(); $t = $a->physicalTests->first(); @endphp
            <tr>
                <td class="num">{{ $i + 1 }}</td>
                <td>{{ $a->code }}</td>
                <td class="name">{{ $a->name }}</td>
                @foreach(array_keys($hrCols) as $f)<td class="num">{{ $fmt($an?->$f) }}</td>@endforeach
                @foreach(array_keys($anthroCols) as $f)<td class="num">{{ $fmt($an?->$f) }}</td>@endforeach
                @forelse($metricDefs as $key => $m)
                    <td class="num">{{ $fmt(data_get($t?->results, $key . '.result')) }}</td>
                @empty<td></td>@endforelse
            </tr>
        @endforeach
    </tbody>
</table>
