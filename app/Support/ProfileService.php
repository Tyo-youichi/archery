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
            'physical'  => self::evaluate($phys, config('archery.physical_metrics'), config('archery.physical_groups'), $athlete->gender),
            'technique' => self::evaluate($tech, config('archery.technique_metrics'), config('archery.technique_groups'), $athlete->gender),
        ];
    }

    /** @return array{record:mixed, metrics:array, groups:array, overall:?float} */
    public static function evaluate($record, array $metrics, array $groups, ?string $gender): array
    {
        $out = ['record' => $record, 'metrics' => [], 'groups' => [], 'overall' => null];
        if (! $record) {
            return $out;
        }

        $byGroup = [];
        foreach ($metrics as $key => $def) {
            $result = $record->{$key};
            $bm = $record->{$key . '_bm'} ?? self::defaultBm($def['bm'], $gender);
            $pct = ($result !== null && $bm > 0) ? min(1, $result / $bm) : null;

            $out['metrics'][$key] = [
                'label' => $def['label'], 'result' => $result, 'bm' => $bm, 'pct' => $pct,
                'gap' => $pct === null ? null : 1 - $pct,
            ];
            if ($pct !== null) {
                $byGroup[$def['group']][] = $pct;
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

    private static function defaultBm($bm, ?string $gender): float
    {
        if (is_array($bm)) {
            return (float) ($bm[$gender] ?? reset($bm));
        }

        return (float) $bm;
    }
}
