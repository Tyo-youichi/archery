<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoadTest extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['tested_at' => 'date'];

    public function athlete() { return $this->belongsTo(Athlete::class); }

    protected static function booted(): void
    {
        // 1RM = beban x (1 + 0.0333 x reps)  (sama dengan rumus di Excel)
        static::saving(function (self $t) {
            $t->one_rm = round($t->weight_kg * (1 + config('archery.one_rm_factor') * $t->reps), 2);
        });
    }
}
