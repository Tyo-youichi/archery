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
 *
 * Tiap metrik dirender sebagai SATU blok (nilai + override BM menyatu). Metrik
 * berpasangan kanan/kiri (key berakhiran _right/_left) ditata sejajar di form.
 *
 * Sebagian metrik fisik direkam BEBERAPA PERCOBAAN (mis. Grip Strength 7x,
 * lihat config('archery.physical_attempts')). Untuk metrik itu tiap percobaan
 * disimpan di results."<key>".attempts[], dan "result" = nilai TERTINGGI (yang
 * dipakai untuk benchmark/profil/laporan).
 */
class RecordController extends Controller
{
    private const METRIC_TYPES = ['physical', 'technique'];

    /** Metrik multi-percobaan (key => jumlah percobaan). Hanya berlaku untuk tes fisik. */
    private function attempts(string $type): array
    {
        return $type === 'physical' ? (array) config('archery.physical_attempts', []) : [];
    }

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

    /**
     * Satu field "metric" per metrik (nilai + BM menyatu). 'attempts' > 0 =>
     * nilai direkam beberapa percobaan; 'side' => right/left untuk tata letak sejajar.
     */
    private function metricFields(string $domain): array
    {
        $attempts = $this->attempts($domain);
        $fields = [];
        foreach (Metrics::metrics($domain) as $key => $m) {
            $unit = $m['unit'] ? ' (' . $m['unit'] . ')' : '';
            $fields[$key] = [
                'label'    => $m['label'] . $unit,
                'type'     => 'metric',
                'attempts' => (int) ($attempts[$key] ?? 0),
                'step'     => $m['step'] ?: '0.01',
                'side'     => str_ends_with($key, '_right') ? 'right' : (str_ends_with($key, '_left') ? 'left' : null),
            ];
        }

        return $fields;
    }

    private function rules(array $def): array
    {
        $rules = [];
        foreach ($def['fields'] as $name => $f) {
            if (($f['type'] ?? '') === 'metric') {
                if (($f['attempts'] ?? 0) > 0) {
                    for ($i = 1; $i <= $f['attempts']; $i++) {
                        $rules[$name . '_a' . $i] = 'nullable|numeric|min:0';
                    }
                } else {
                    $rules[$name] = 'nullable|numeric|min:0';
                }
                $rules[$name . '_bm'] = 'nullable|numeric|min:0';

                continue;
            }
            $rules[$name] = $f['rules'];
        }

        return $rules;
    }

    /** Pindahkan field metrik dari input datar ke struktur JSON results (snapshot BM). */
    private function toResults(string $type, array $data, Athlete $athlete, array $base = []): array
    {
        if (! in_array($type, self::METRIC_TYPES, true)) {
            return $data;
        }

        $attempts = $this->attempts($type);
        $results = $base;   // pertahankan metrik lama (mis. yang sudah dinonaktifkan) saat edit
        foreach (Metrics::metrics($type) as $key => $m) {
            $bm = $data[$key . '_bm'] ?? null;
            unset($data[$key . '_bm']);

            // Snapshot BM: pakai override bila diisi, selain itu default metrik per gender.
            if ($bm === null || $bm === '') {
                $bm = $m['bm'][$athlete->gender] ?? $m['bm']['L'] ?? null;
            }
            $bm = $bm === null ? null : (float) $bm;

            if (isset($attempts[$key])) {
                // Kumpulkan percobaan terisi; hasil = nilai tertinggi.
                $vals = [];
                for ($i = 1; $i <= (int) $attempts[$key]; $i++) {
                    $v = $data[$key . '_a' . $i] ?? null;
                    unset($data[$key . '_a' . $i]);
                    if ($v !== null && $v !== '') {
                        $vals[] = (float) $v;
                    }
                }
                $results[$key] = [
                    'result'   => $vals ? max($vals) : null,
                    'bm'       => $bm,
                    'attempts' => $vals,
                ];
            } else {
                $result = $data[$key] ?? null;
                unset($data[$key]);
                $results[$key] = [
                    'result' => ($result === null || $result === '') ? null : (float) $result,
                    'bm'     => $bm,
                ];
            }
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

        $attempts = $this->attempts($type);
        $vals = [];
        foreach (($record->results ?? []) as $key => $cell) {
            if (isset($attempts[$key])) {
                // Isi kotak percobaan dari attempts; fallback: hasil lama masuk ke percobaan ke-1.
                $arr = (isset($cell['attempts']) && is_array($cell['attempts']))
                    ? array_values($cell['attempts'])
                    : (($cell['result'] ?? null) !== null ? [$cell['result']] : []);
                foreach ($arr as $i => $v) {
                    $vals[$key . '_a' . ($i + 1)] = $v;
                }
            } else {
                $vals[$key] = $cell['result'] ?? null;
            }
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
