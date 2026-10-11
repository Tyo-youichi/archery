<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhysicalTest extends Model
{
    protected $table = 'physical_tests';
    protected $guarded = ['id'];
    protected $casts = ['tested_at' => 'date'];

    public function athlete() { return $this->belongsTo(Athlete::class); }

    protected static function booted(): void
    {
        // Isi VO2max otomatis dari tabel referensi Yo-Yo IR1 bila hanya level yang diisi.
        static::saving(function (self $t) {
            if ($t->yoyo_level !== null && $t->yoyo_vo2 === null) {
                $t->yoyo_vo2 = YoyoReference::where('level', $t->yoyo_level)->value('vo2max');
            }
        });
    }
}
