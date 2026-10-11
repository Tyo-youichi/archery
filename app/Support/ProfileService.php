<?php

namespace App\Support;

use App\Models\Athlete;

/**
 * Menghitung persentase capaian terhadap benchmark (BM) dan profil per grup,
 * mengikuti rumus pada file ATHTRIX PROFILE:
 *   % metrik  = min(1, hasil / BM)
 *   % grup    = rata-rata % metrik dalam grup
 *   % profil  = rata-rata % semua grup
 *   kekurangan = 1 - %
 *
 * Definisi metrik & BM diambil dari App\Support\Metrics (tabel `metrics`, fallback config).
 * Nilai tes diambil dari kolom JSON `results` pada record: { key => { result, bm } }.
 * `bm` pada record adalah SNAPSHOT saat input; bila kosong pakai BM default metrik.
 */
class ProfileService
{
    public static function forAthlete(Athlete $athlete): array
    {
        $anthro = $athlete->anthropometries()->first();
        $phys   = $athlete->physicalTests()->first();
        $tech   = $athlete->techniqueTests()->first();

        return [
            'anthropometry' => $anthro,
            'physical'  => self::evaluate($phys, Metrics::for('physical'), $athlete->gender),
            'technique' => self::evaluate($tech, Metrics::for('technique'), $athlete->gender),
        ];
    }

    /**
     * @param  array  $def  hasil Metrics::for(): ['metrics'=>[...], 'groups'=>[...]]
     * @return array{record:mixed, metrics:array, groups:array, overall:?float}
     */
    public static function evaluate($record, array $def, ?string $gender): array
    {
        $metrics = $def['metrics'] ?? [];
        $groups  = $def['groups'] ?? [];

        $out = ['record' => $record, 'metrics' => [], 'groups' => [], 'overall' => null];
        if (! $record) {
            return $out;
        }

        $results = $record->results ?? [];   // cast array pada model
        $byGroup = [];

        foreach ($metrics as $key => $m) {
            $cell   = $results[$key] ?? null;
            $result = is_array($cell) ? ($cell['result'] ?? null) : null;
            $snapBm = is_array($cell) ? ($cell['bm'] ?? null) : null;

            $bm = $snapBm ?? ($m['bm'][$gender] ?? $m['bm']['L'] ?? null);
            $result = $result === null ? null : (float) $result;
            $bm = $bm === null ? null : (float) $bm;
            $pct = ($result !== null && $bm > 0) ? min(1, $result / $bm) : null;

            $out['metrics'][$key] = [
                'label' => $m['label'], 'result' => $result, 'bm' => $bm, 'pct' => $pct,
                'gap' => $pct === null ? null : 1 - $pct,
            ];
            if ($pct !== null) {
                $byGroup[$m['group']][] = $pct;
            }
        }

        foreach ($groups as $g => $label) {
            $vals = $byGroup[$g] ?? [];
            $pct = $vals ? array_sum($vals) / count($vals) : null;
            $out['groups'][$g] = ['label' => $label, 'pct' => $pct, 'gap' => $pct === null ? null : 1 - $pct];
        }

        $gp = array_filter(array_column($out['groups'], 'pct'), fn ($v) => $v !== null);
        $out['overall'] = $gp ? array_sum($gp) / count($gp) : null;

        return $out;
    }
}
