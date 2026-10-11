<?php

namespace Database\Seeders;

use App\Models\Metric;
use App\Support\Metrics;
use Illuminate\Database\Seeder;

/**
 * Mengisi tabel `metrics` DARI config/archery.php (physical_metrics/technique_metrics
 * + grup). Config = definisi awal; DB = sumber runtime yang bisa di-CRUD admin.
 * Idempotent (updateOrCreate per domain+key).
 *
 *   php artisan db:seed --class=MetricSeeder
 */
class MetricSeeder extends Seeder
{
    public function run(): void
    {
        // Key yang punya kolom GENERATED ter-index di migration (analitik lintas-atlet).
        $promoted = ['yoyo_vo2', 'qualification'];

        $map = [
            'physical'  => ['archery.physical_metrics', 'archery.physical_groups'],
            'technique' => ['archery.technique_metrics', 'archery.technique_groups'],
        ];

        foreach ($map as $domain => [$mKey, $gKey]) {
            $groups = config($gKey, []);
            $sort = 0;
            foreach (config($mKey, []) as $key => $def) {
                $bm = $def['bm'] ?? null;
                $bmL = is_array($bm) ? ($bm['L'] ?? null) : $bm;
                $bmP = is_array($bm) ? ($bm['P'] ?? null) : null;   // scalar => null = samakan L

                Metric::updateOrCreate(
                    ['domain' => $domain, 'key' => $key],
                    [
                        'group_key'   => $def['group'],
                        'group_label' => $groups[$def['group']] ?? ucfirst($def['group']),
                        'label'       => $def['label'],
                        'unit'        => $def['unit'] ?? null,
                        'step'        => $def['step'] ?? '0.01',
                        'bm_l'        => $bmL,
                        'bm_p'        => $bmP,
                        'is_promoted' => in_array($key, $promoted, true),
                        'is_active'   => true,
                        'sort'        => $sort++,
                    ]
                );
            }
        }

        Metrics::flush();
    }
}
