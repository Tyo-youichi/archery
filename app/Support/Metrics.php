<?php

namespace App\Support;

use App\Models\Metric;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

/**
 * Sumber definisi metrik + benchmark. Data utama = tabel `metrics` (bisa di-CRUD admin),
 * di-seed dari config/archery.php. Bila tabel belum ada / kosong, fallback ke config
 * sehingga aplikasi tetap jalan sebelum di-seed. Hasil di-cache; panggil Metrics::flush()
 * setelah mengubah data metrik.
 *
 * Bentuk per domain:
 *   [
 *     'metrics' => [ '<key>' => ['key','label','group','unit','step','bm'=>['L','P']], ... ],
 *     'groups'  => [ '<group_key>' => '<group_label>', ... ],  // urut sesuai 'sort'
 *   ]
 */
class Metrics
{
    private const CACHE_KEY = 'metrics.snapshot';

    public static function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    public static function for(string $domain): array
    {
        return self::data()[$domain] ?? ['metrics' => [], 'groups' => []];
    }

    public static function metrics(string $domain): array
    {
        return self::for($domain)['metrics'];
    }

    public static function groups(string $domain): array
    {
        return self::for($domain)['groups'];
    }

    private static function data(): array
    {
        if (! config('archery.metrics_cache', true)) {
            return self::load();
        }

        return Cache::rememberForever(self::CACHE_KEY, fn () => self::load());
    }

    private static function load(): array
    {
        try {
            if (! Schema::hasTable('metrics')) {
                return self::fromConfig();
            }
        } catch (\Throwable) {
            return self::fromConfig();
        }

        $rows = Metric::active()->orderBy('sort')->orderBy('id')->get();
        if ($rows->isEmpty()) {
            return self::fromConfig();   // tabel ada tapi belum di-seed
        }

        $out = [];
        foreach (['physical', 'technique'] as $domain) {
            $metrics = [];
            $groups  = [];
            foreach ($rows->where('domain', $domain) as $m) {
                $metrics[$m->key] = [
                    'key'   => $m->key,
                    'label' => $m->label,
                    'group' => $m->group_key,
                    'unit'  => $m->unit,
                    'step'  => $m->step ?: '0.01',
                    'bm'    => ['L' => $m->bm_l, 'P' => $m->bm_p ?? $m->bm_l],
                ];
                $groups[$m->group_key] = $m->group_label;
            }
            $out[$domain] = ['metrics' => $metrics, 'groups' => $groups];
        }

        return $out;
    }

    /** Fallback: bangun struktur dari config/archery.php. */
    private static function fromConfig(): array
    {
        $out = [];
        $map = [
            'physical'  => ['archery.physical_metrics', 'archery.physical_groups'],
            'technique' => ['archery.technique_metrics', 'archery.technique_groups'],
        ];

        foreach ($map as $domain => [$mKey, $gKey]) {
            $groups = config($gKey, []);
            $metrics = [];
            foreach (config($mKey, []) as $key => $def) {
                $bm = $def['bm'] ?? null;
                $bmL = is_array($bm) ? ($bm['L'] ?? null) : $bm;
                $bmP = is_array($bm) ? ($bm['P'] ?? $bmL) : $bm;
                $metrics[$key] = [
                    'key'   => $key,
                    'label' => $def['label'],
                    'group' => $def['group'],
                    'unit'  => $def['unit'] ?? null,
                    'step'  => $def['step'] ?? '0.01',
                    'bm'    => ['L' => $bmL === null ? null : (float) $bmL, 'P' => $bmP === null ? null : (float) $bmP],
                ];
            }
            $out[$domain] = ['metrics' => $metrics, 'groups' => $groups];
        }

        return $out;
    }
}
