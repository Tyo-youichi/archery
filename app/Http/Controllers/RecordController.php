<?php

namespace App\Http\Controllers;

use App\Models\Athlete;
use Illuminate\Http\Request;

/**
 * Controller generik untuk data berseri per atlet (antropometri, tes fisik,
 * performa teknik, beban/1RM, skor bulanan). Definisi field ada di config/archery.php.
 */
class RecordController extends Controller
{
    private function def(string $type): array
    {
        $def = config("archery.records.$type");
        abort_unless($def, 404);

        return $def;
    }

    private function rules(array $def): array
    {
        return collect($def['fields'])->map(fn ($f) => $f['rules'])->all();
    }

    public function create(Athlete $athlete, string $type)
    {
        $def = $this->def($type);

        return view('records.form', ['athlete' => $athlete, 'type' => $type, 'def' => $def, 'record' => null]);
    }

    public function store(Request $request, Athlete $athlete, string $type)
    {
        $def = $this->def($type);
        $data = $request->validate($this->rules($def));
        $def['model']::create($data + ['athlete_id' => $athlete->id]);

        return redirect()->route('athletes.show', $athlete)->with('ok', $def['label'] . ' tersimpan.');
    }

    public function edit(Athlete $athlete, string $type, int $id)
    {
        $def = $this->def($type);
        $record = $def['model']::where('athlete_id', $athlete->id)->findOrFail($id);

        return view('records.form', compact('athlete', 'type', 'def', 'record'));
    }

    public function update(Request $request, Athlete $athlete, string $type, int $id)
    {
        $def = $this->def($type);
        $record = $def['model']::where('athlete_id', $athlete->id)->findOrFail($id);
        $record->update($request->validate($this->rules($def)));

        return redirect()->route('athletes.show', $athlete)->with('ok', $def['label'] . ' diperbarui.');
    }

    public function destroy(Athlete $athlete, string $type, int $id)
    {
        $def = $this->def($type);
        $def['model']::where('athlete_id', $athlete->id)->findOrFail($id)->delete();

        return redirect()->route('athletes.show', $athlete)->with('ok', 'Data dihapus.');
    }
}
