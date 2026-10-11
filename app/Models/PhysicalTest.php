<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhysicalTest extends Model
{
    protected $table = 'physical_tests';
    protected $guarded = ['id'];
    protected $casts = ['tested_at' => 'date', 'results' => 'array'];

    public function athlete() { return $this->belongsTo(Athlete::class); }

    protected static function booted(): void
    {
        // Isi VO2max otomatis dari tabel referensi Yo-Yo IR1 bila hanya level yang diisi
        // (hasil tersimpan di JSON results.yoyo_vo2.result).
        static::saving(function (self $t) {
            if ($t->yoyo_level === null) {
                return;
            }
            $r = $t->results ?? [];
            $cur = $r['yoyo_vo2']['result'] ?? null;
            if ($cur === null || $cur === '') {
                $vo2 = YoyoReference::where('level', $t->yoyo_level)->value('vo2max');
                if ($vo2 !== null) {
                    $r['yoyo_vo2'] = array_merge($r['yoyo_vo2'] ?? [], ['result' => (float) $vo2]);
                    $t->results = $r;
                }
            }
        });
    }
}
