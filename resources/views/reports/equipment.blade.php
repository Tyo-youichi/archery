<table class="table table-bordered table-sm report-table align-middle">
    <thead>
        <tr>
            <th>No</th>
            <th>Kode</th>
            <th class="text-start">Nama</th>
            @foreach($equipmentFields as $label)<th>{{ $label }}</th>@endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($athletes as $i => $a)
            @php $eq = $a->equipment; @endphp
            <tr>
                <td class="num">{{ $i + 1 }}</td>
                <td>{{ $a->code }}</td>
                <td class="name">{{ $a->name }}</td>
                @foreach(array_keys($equipmentFields) as $f)
                    <td>{{ $eq?->$f ?: '–' }}</td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
