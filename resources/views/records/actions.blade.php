<a class="small" href="{{ route('records.edit', [$athlete, $type, $id]) }}">edit</a>
<form method="post" action="{{ route('records.destroy', [$athlete, $type, $id]) }}" class="d-inline" onsubmit="return confirm('Hapus data ini?')">@csrf @method('DELETE')<button class="btn btn-link btn-sm p-0 text-danger">hapus</button></form>
