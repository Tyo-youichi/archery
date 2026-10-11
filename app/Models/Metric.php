<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Definisi metrik tes (fisik/teknik) + benchmark. Nilai tes atlet ada di JSON
 * `results` pada physical_tests/technique_tests; model ini hanya definisinya.
 */
class Metric extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'bm_l'        => 'float',
        'bm_p'        => 'float',
        'is_promoted' => 'bool',
        'is_active'   => 'bool',
        'sort'        => 'int',
    ];

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function scopeDomain($q, string $domain)
    {
        return $q->where('domain', $domain);
    }

    /** Benchmark untuk gender tertentu (P pakai bm_p bila ada, selain itu bm_l). */
    public function bmFor(?string $gender): ?float
    {
        $bm = $gender === 'P' ? ($this->bm_p ?? $this->bm_l) : ($this->bm_l ?? $this->bm_p);

        return $bm === null ? null : (float) $bm;
    }
}
