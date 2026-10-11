<?php

namespace App\Http\Controllers;

use App\Models\Athlete;
use App\Support\Metrics;
use Illuminate\Http\Request;

/**
 * Controller generik untuk data berseri per atlet (antropometri, tes fisik,
 * performa teknik, beban/1RM, skor bulanan). Field dasar didefinisikan di
 * config/archery.php; khusus 'physical' & 'technique' field metriknya DINAMIS,
 * diambil dari tabel `metrics` (App\Support\Metrics) dan disimpan ke kolom JSON
 * `results`: { "<key>": { "result": x, "bm": <snapshot> } }.
 */
class RecordController extends Controller
{
    private const METRIC_TYPES = ['physical', 'technique'];

    private function def(string $type): array
    {
        $def = config("archery.records.$type");
        abort_unless($def, 404);

        // Sisipkan field metrik dinamis untuk tes fisik & teknik.
        if (in_array($type, self::METRIC_TYPES, true)) {
            $def['fields'] = $def['fields'] + $this->metricFields($type);
        }

        return $def;
    }

    /** Field input untuk tiap metrik aktif: nilai + override BM. */
    private function metricFields(string $domain): array
    {
        $fields = [];
        foreach (Metrics::metrics($domain) as $key => $m) {
            $unit = $m['unit'] ? ' (' . $m['unit'] . ')' : '';
            $step = $m['step'] ?: '0.01';
            $fields[$key] = ['label' => $m['label'] . $unit, 'type' => 'number', 'step' => $step, 'rules' => 'nullable|numeric|min:0'];
            $fields[$key . '_bm'] = ['label' => 'BM ' . $m['label'] . ' (kosongkan = default)', 'type' => 'number', 'step' => $step, 'rules' => 'nullable|numeric|min:0'];
        }

        return $fields;
    }

    private function rules(array $def): array
    {
        return collect($def['fields'])->map(fn ($f) => $f['rules'])->all();
    }

    /** Pindahkan field metrik dari input datar ke struktur JSON results (snapshot BM). */
    private function toResults(string $type, array $data, Athlete $athlete, array $base = []): array
    {
        if (! in_array($type, self::METRIC_TYPES, true)) {
            return $data;
        }

        $results = $base;   // pertahankan metrik lama (mis. yang sudah dinonaktifkan) saat edit
        foreach (Metrics::metrics($type) as $key => $m) {
            $result = $data[$key] ?? null;
            $bm = $data[$key . '_bm'] ?? null;
            unset($data[$key], $data[$key . '_bm']);

            // Snapshot BM: pakai override bila diisi, selain itu default metrik per gender.
            if ($bm === null || $bm === '') {
                $bm = $m['bm'][$athlete->gender] ?? $m['bm']['L'] ?? null;
            }

            $results[$key] = [
                'result' => ($result === null || $result === '') ? null : (float) $result,
                'bm'     => $bm === null ? null : (float) $bm,
            ];
        }
        $data['results'] = $results;

        return $data;
    }

    /** Nilai metrik untuk prefill form edit (dari JSON results). */
    private function metricValues(string $type, $record): array
    {
        if (! $record || ! in_array($type, self::METRIC_TYPES, true)) {
            return [];
        }

        $vals = [];
        foreach (($record->results ?? []) as $key => $cell) {
            $vals[$key] = $cell['result'] ?? null;
            $vals[$key . '_bm'] = $cell['bm'] ?? null;
        }

        return $vals;
    }

    public function create(Athlete $athlete, string $type)
    {
        $def = $this->def($type);

        return view('records.form', ['athlete' => $athlete, 'type' => $type, 'def' => $def, 'record' => null, 'values' => []]);
    }

    public function store(Request $request, Athlete $athlete, string $type)
    {
        $def = $this->def($type);
        $data = $request->validate($this->rules($def));
        $data = $this->toResults($type, $data, $athlete);
        $def['model']::create($data + ['athlete_id' => $athlete->id]);

        return redirect()->route('athletes.show', $athlete)->with('ok', $def['label'] . ' tersimpan.');
    }

    public function edit(Athlete $athlete, string $type, int $id)
    {
        $def = $this->def($type);
        $record = $def['model']::where('athlete_id', $athlete->id)->findOrFail($id);
        $values = $this->metricValues($type, $record);

        return view('records.form', compact('athlete', 'type', 'def', 'record', 'values'));
    }

    public function update(Request $request, Athlete $athlete, string $type, int $id)
    {
        $def = $this->def($type);
        $record = $def['model']::where('athlete_id', $athlete->id)->findOrFail($id);
        $data = $request->validate($this->rules($def));
        $data = $this->toResults($type, $data, $athlete, $record->results ?? []);
        $record->update($data);

        return redirect()->route('athletes.show', $athlete)->with('ok', $def['label'] . ' diperbarui.');
    }

    public function destroy(Athlete $athlete, string $type, int $id)
    {
        $def = $this->def($type);
        $def['model']::where('athlete_id', $athlete->id)->findOrFail($id)->delete();

        return redirect()->route('athletes.show', $athlete)->with('ok', 'Data dihapus.');
    }
}
