<table class="table table-bordered table-sm report-table align-middle text-center">
    <thead>
        <tr>
            <th>No</th>
            <th>Kode</th>
            <th class="text-start">Nama</th>
            @foreach($months as $label)<th>{{ $label }}</th>@endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($athletes as $a)
            @php $byMonth = $a->monthlyScores->keyBy('month'); @endphp
            <tr>
                <td class="num">{{ $athletes->firstItem() + $loop->index }}</td>
                <td>{{ $a->code }}</td>
                <td class="name text-start">{{ $a->name }}</td>
                @foreach($months as $mn => $label)
                    @php $s = $byMonth->get($mn); @endphp
                    <td>
                        @if($s && $s->score !== null)
                            <strong>{{ $s->score }}</strong>
                            @if($s->venue)<div class="report-muted" style="font-size:.68rem">{{ $s->venue }}</div>@endif
                        @else
                            <span class="report-muted">–</span>
                        @endif
                    </td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
